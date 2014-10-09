<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once(BASEPATH . 'libraries/Cart.php');

class Basket extends CI_Cart {

    private $_coupon = array();
    private $_discounted = 0;

    public function __construct($params = array()) {
        $this->CI = &get_instance();
        $this->product_name_safe = false;
        $this->CI->load->model('store/affiliate_m');
        $this->CI->load->helper('store/store');
        parent::__construct($params);

        $this->_coupon = $this->CI->session->userdata('cart_coupon');
        if ($this->_coupon === FALSE) {
            $this->_coupon = array();
        }
        $this->_discounted = $this->calculate_discount();
    }

    public function update($items = array()) {
        if (isset($items['coupon'])) {
            $this->insert_coupon($items['coupon']);
        }

        return parent::update($items);
    }

    public function totals() {
        $totals = array();
        $coupon = $this->_coupon;

        $total = parent::total();
        $discounted_total = parent::total();
        $currency = Settings::get('currency');


        if (!empty($coupon) && array_key_exists('id', $coupon) && array_key_exists('name', $coupon) && array_key_exists('code', $coupon) && array_key_exists('type', $coupon) && array_key_exists('amount', $coupon)) {
            $discount = $coupon['type'] == 'fixed' ? $coupon['amount'] : $total * ($coupon['amount'] * 0.01);
            $discounted_total = $total - $discount;
            $totals[] = array('slug' => 'coupons', 'title' => $coupon['name'], 'value' => $coupon['type'] == 'fixed' ? '-' . $coupon['amount'] . $currency : $coupon['amount'] . '%');
        }
        if ($this->CI->affiliate_m->is_affiliate()) {
            $discounted_total = $discounted_total * $this->CI->affiliate_m->get_multiplier();
            $totals[] = array('slug' => 'affiliate', 'title' => lang('store:affiliate'), 'value' => $this->CI->affiliate_m->get_discount() . '%');
        }
        if ($ship_id = $this->CI->session->userdata('shipment_id')) {
            class_exists('shipments_m') OR $this->CI->load->model('store/shipments_m');
            $shipping = $this->CI->shipments_m->calculate($ship_id);
            $discounted_total = $discounted_total + $shipping->price;
            $totals[] = array('slug' => 'shipping', 'title' => $shipping->name, 'value' => $shipping->price . $currency);
        }


        $totals[] = array('slug' => 'total', 'title' => lang('store:total_cost'), 'value' => format_price($discounted_total) . $currency);

        return $totals;
    }

    public function discounted_total() {
        return $this->_discounted;
    }

    public function calculate_discount() {
        $total = parent::total();
        $coupon = $this->_coupon;
        if (!empty($coupon) && array_key_exists('id', $coupon) && array_key_exists('name', $coupon) && array_key_exists('code', $coupon) && array_key_exists('type', $coupon) && array_key_exists('amount', $coupon)) {
            $discount = $coupon['type'] == 'fixed' ? $coupon['amount'] : $total * ($coupon['amount'] * 0.01);
            $total = $total - $discount;
        }
        if ($this->CI->affiliate_m->is_affiliate()) {
            $total = $total * $this->CI->affiliate_m->get_multiplier();
        }

        return $total;
    }

    public function insert_coupon($item) {
        if (!is_array($item)) {
            return FALSE;
        }
        extract($item);

        if (!preg_match('/^[\.0-9]+$/i', $id)) {
            log_message('error', 'An invalid id was submitted as the coupon id. The name can only contain alpha-numeric characters, dashes, underscores, colons, and spaces');
            return FALSE;
        }

        if (!preg_match('/^[' . $this->product_id_rules . ']+$/i', $code)) {
            log_message('error', 'An invalid code was submitted as the coupon code. The name can only contain alpha-numeric characters, dashes, underscores, colons, and spaces');
            return FALSE;
        }

        if ($this->product_name_safe && !preg_match('/^[' . $this->product_name_rules . ']+$/i', $name)) {
            log_message('error', 'An invalid name was submitted as the coupon name:  The name can only contain alpha-numeric characters, dashes, underscores, colons, and spaces');
            return FALSE;
        }

        $amount = (float) $amount;

        if (!is_numeric($amount)) {
            log_message('error', 'An invalid amount was submitted for coupon: ' . $code);
            return FALSE;
        }

        if (!in_array($type, array('percent', 'fixed'))) {
            log_message('error', 'An invalid value submited as coupon type');
            return FALSE;
        }


        $this->_coupon = array('id' => $id, 'name' => $name, 'code' => $code, 'amount' => $amount, 'type' => $type);
        $this->CI->session->set_userdata(array('cart_coupon' => $this->_coupon));

        return $this->_coupon;
    }

    public function coupon($prop = NULL) {
        if (empty($this->_coupon)) {
            return FALSE;
        }
        if ($prop === NULL) {
            return $this->_coupon;
        }
        if (array_key_exists($prop, $this->_coupon)) {
            return $this->_coupon[$prop];
        } else {
            return FALSE;
        }

        return $this->_coupon;
    }

    public function remove_coupon() {

        $this->_coupon = array();
        $this->CI->session->unset_userdata('cart_coupon');
        return TRUE;
    }

    public function destroy() {
        $this->remove_coupon();
        parent::destroy();
    }

    public function display_form($price, $name, $product_id = 0, $show_quantity = TRUE, $uri = NULL, $module = NULL) {
         $this->CI->lang->load('store/store');
        $uri OR $uri = $this->CI->uri->uri_string();
        $module OR $module = $this->CI->module;
        
        $form = form_open('store/cart/add', 'class="basket-form"');
        $form .= form_hidden('id', $module.'_'.$product_id);
        $form .= form_hidden('module', $module);
        $form .= form_hidden('uri', $uri);
        $form .= form_hidden('price', $price);
        $form .= form_hidden('name', $name);
        if ($show_quantity) {
            $form .= form_input('qty', 1, 'id="basket_quantity"');
        } else {
            $form .= form_hidden('qty', 1);
        }
        $form .= form_submit('add_to_cart', lang('store:add_to_cart'), 'id="basket_submit"');
        $form .= form_close();
        return $form;
    }

}
