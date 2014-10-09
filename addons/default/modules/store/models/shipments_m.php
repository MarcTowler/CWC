<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Shipments_m extends MY_Model {

    public function __construct() {
        parent::__construct();

        $this->_table = 'store_shipments';
    }
    
    //create a new item
    public function create($input) {
        $to_insert = array(
            'name' => $input['name'],
            'desc' => strip_tags($input['desc']),
            'price' => floatval($input['price']),
            'min' => floatval($input['min']),
            'max' => floatval($input['max']),
            'url' => $input['url'],
            'type' => $input['type'],
            'status' => $input['status'],
            'options' => isset($input['options']) ? serialize($input['options']) : '',
            //'payment' => isset($input['payment']) ? implode(',', $input['payment']) : ''
        );
        
        $product_id = $this->insert($to_insert);
        
        return $product_id;
    }
    
    public function update($primary_value, $input, $skip_validation = FALSE) {
        
        $data = array(
            'name' => $input['name'],
            'desc' => strip_tags($input['desc']),
            'price' => floatval($input['price']),
            'min' => floatval($input['min']),
            'max' => floatval($input['max']),
            'url' => $input['url'],
            'type' => $input['type'],
            'status' => $input['status'],
            'options' => isset($input['options']) ? serialize($input['options']) : '',
            //'payment' => isset($input['payment']) ? implode(',', $input['payment']) : ''
        );
        
        return parent::update($primary_value, $data, $skip_validation);
    }
    
     public function calculate($id) {
        $carrier = $this->get($id);
        $carrier = $this->calculate_rate($carrier);
        return $carrier;
    }

    public function get_carriers() {
        $carriers = $this->order_by('price')->where('min <', $this->cart->discounted_total())->get_many_by('status', 'active');

        foreach ($carriers as &$carrier) {
            $carrier = $this->calculate_rate($carrier);
        }
        return $carriers;
    }

    public function calculate_rate($carrier) {
        $options = $carrier->options ? unserialize($carrier->options) : array();

        switch ($carrier->type) {
            case 'flat':
                $carrier->price = $this->_rate_flat($carrier->price, key_exists('tax', $options));
                break;
            case 'weight':
                $carrier->price = $this->_rate_weight($options, $carrier->price);
                break;
            case 'quantity':
                $carrier->price = $this->_rate_quantity($options, $carrier->price);
                break;
            case 'distance':
                $carrier->price = $this->_rate_distance($options, $carrier->price);
                break;
            default:
                $carrier->price = $carrier->price;
                break;
        }
        return $carrier;
    }

    private function _rate_flat($price, $include_tax = FALSE) {
        if ($include_tax) {
            $tax = Settings::get('store_tax_value') / 100;
            $taxation = $price * $tax;
            return $price + $taxation;
        } else {
            return $price;
        }
    }

    private function _rate_weight($options, $price) {
        $total_weight = 0;
        $items = $this->cart->contents();
        class_exists('products_m') OR $this->load->model('products_m');

        $is_weight_available = true;

        foreach ($items as $product) {
            $db_product = $this->products_m->get_by('id', $product['id']);
            if (isset($db_product->weight)) {
                $total_weight += floatval($db_product->weight);
            } else {
                $is_weight_available = false;
                break;
            }
        }
        if ($is_weight_available) {
            foreach ($options as $value) {
                if (($total_weight >= $value['from']) AND ($total_weight <= $value['to'])) {
                    $price = $value['price'];
                    break;
                }
            }
        }

        return format_price($price);
    }

    private function _rate_quantity($options, $price) {
        $total_items = $this->cart->total_items();
        foreach ($options as $value) {
            if (($total_items >= $value['from']) AND ($total_items <= $value['to'])) {
                $price = $value['price'];
                break;
            }
        }
        return format_price($price);
    }

    private function _rate_distance($options, $price) {
        $source_location = $options['location'];
        $target_location = '';
        $delivery_id = $this->session->userdata('delivery');
        $this->load->model('address_m');
        $address = $this->address_m->get($delivery_id);
        $target_location = $address->city . ', ' . $address->address1 . ' ' . $address->address2.', '.$address->zip;
        try {
            $distance = $this->_get_driving_distance($source_location, $target_location);
        } catch (Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
            log_message('error', $e->getMessage());
        }
        
        foreach ($options as $key => $value) {
            if (is_numeric($key)) {
                if (($distance['distance'] >= $value['from']) AND ($distance['distance'] <= $value['to'])) {
                    $price = $value['price'];
                    break;
                }
            }
        }
        //return format_price($distance['distance']*$price);
        return format_price($price);
    }

    private function _get_lang_distance($lat1, $lng1, $lat2, $lng2, $miles = true) {
        $pi80 = M_PI / 180;
        $lat1 *= $pi80;
        $lng1 *= $pi80;
        $lat2 *= $pi80;
        $lng2 *= $pi80;

        $r = 6372.797; // mean radius of Earth in km
        $dlat = $lat2 - $lat1;
        $dlng = $lng2 - $lng1;
        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $km = $r * $c;

        return ($miles ? ($km * 0.621371192) : $km);
    }

    private function _get_driving_distance($start, $finish, $raw = false) {
        if (strcmp($start, $finish) == 0) {
            $time = 0;
            if ($raw) {
                $time .= ' seconds';
            }

            return array('distance' => 0, 'time' => $time);
        }

        $start = urlencode($start);
        $finish = urlencode($finish);

        $distance = 'unknown';
        $time = 'unknown';

        $url = 'http://maps.googleapis.com/maps/api/directions/xml?origin=' . $start . '&destination=' . $finish . '&sensor=false';
        
        if ($data = file_get_contents($url)) {
            $xml = new SimpleXMLElement($data);

            if (isset($xml->route->leg->duration->value) AND (int) $xml->route->leg->duration->value > 0) {
                if ($raw) {
                    $distance = (string) $xml->route->leg->distance->text;
                    $time = (string) $xml->route->leg->duration->text;
                } else {
                    $distance = (int) $xml->route->leg->distance->value / 1000 / 1.609344;
                    $time = (int) $xml->route->leg->duration->value;
                }
            } else {
                return array('distance' => 0, 'time' => 0);
            }

            return array('distance' => $distance, 'time' => $time);
        } else {
            throw new Exception('Could not resolve URL');
        }
    }

}
