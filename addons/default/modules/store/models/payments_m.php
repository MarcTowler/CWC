<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Payments_m extends MY_Model {

    public function __construct() {
        parent::__construct();

        $this->load->library('store/basket', '', 'cart');
        $this->_table = 'store_orders';
    }

    public function place_order($inputs, $cart) {
        $this->db->insert('store_orders', array(
            'user_id' => $inputs['user_id'],
            'total' => $inputs['total'],
            'shipping' => $inputs['shipping'],
            'shipment_method_id' => $inputs['shipment_method_id'],
            'payment_method_id' => $inputs['payment_method'],
            'billing_address_id' => $inputs['billing_address_id'],
            'delivery_address_id' => $inputs['delivery_address_id'],
            'order_date' => time(),
            'coupon' => $inputs['coupon'],
            'token' => $inputs['token'],
            'fraud_score' => $inputs['fraud_score'],
            'session_id' =>  $this->session->userdata('session_id')
        ));

        $order_id = $this->db->insert_id();
        $contents = array();

        foreach ($cart as $item) {
            $contents[] = array(
                'order_id' => $order_id,
                'product_id' => $this->int_from_str($item['id']),
                'name' => $item['name'],
                'module' => $item['module'],
                'uri' => $item['uri'],
                'options' => $this->cart->has_options($item['rowid']) ? serialize($this->cart->product_options($item['rowid'])) : '',
                'qty' => $item['qty'],
                'price_per' => $item['price']
            );
        }

        $this->db->insert_batch('store_order_contents', $contents);

        $this->db->insert('store_order_history', array(
            'status' => 'pending',
            'notify' => 0,
            'order_id' => $order_id,
            'comment' => "Order has been placed",
            'date_added' => time()
        ));

        return $order_id;
    }

    public function set_address($input) {
        $data = array(
            'user_id' => $input['user_id'],
            'email' => $input['email'],
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'company' => $input['company'],
            'nip' => $input['nip'],
            'address1' => $input['address1'],
            'address2' => $input['address2'],
            'city' => $input['city'],
            'state' => $input['state'],
            'country' => $input['country'],
            'zip' => $input['zip'],
            'phone' => $input['phone'],
        );

        if (isset($input['id'])) {
            $this->db->where('id', $input['id']);
            $this->db->update('store_addresses', $data);
            return $input['id'];
        } else {
            $this->db->insert('store_addresses', $data);
            return $this->db->insert_id();
        }
    }

    public function get_address($id) {
        $data = (object) array(
                    'type_both' => '',
                    'type_invoice' => '',
                    'type_shipment' => '',
                    'address_name' => '',
                    'address1' => '',
                    'address2' => '',
                    'city' => '',
                    'state' => '',
                    'country' => '',
                    'zip' => '',
                    'phone' => '',
        );
        if ($id) {
            $item = $this->db->where('id', $id)->limit(1)->get('store_addresses')->row();
            if ($item) {
                $data = $item;
                $data->address_name = $data->name;
                unset($data->name);
                $data->type_both = $item->type == "both" ? TRUE : FALSE;
                $data->type_invoice = $item->type == "invoice" ? TRUE : FALSE;
                $data->type_shipment = $item->type == "shipment" ? TRUE : FALSE;
            }
        }
        return $data;
    }

    private function int_from_str($s) {
        return(int) preg_replace('/[^\-\d]*(\-?\d*).*/', '$1', $s);
    }

}
