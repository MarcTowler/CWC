<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * This is a store module for PyroCMS
 *
 * @author 		Blazej Adamczyk
 * @website		http://sein.com.pl
 * @package 	PyroStore
 * @subpackage 	Cart Controller
 */
class Cart extends Public_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model('products_m');
        $this->load->model('options_m');
        $this->load->library('store/basket', '', 'cart');
        $this->load->helper('store');
        $this->lang->load('store');

        if (Settings::get('store_login_required') && !$this->current_user) {
            $this->session->set_flashdata('notice', lang('store:not_logged_in'));
            $this->session->set_userdata('redirect_to', 'store');
            redirect('users/login');
        }
    }

    /**
     * All items
     */
    public function index() {
        // set the pagination limit
        $data = new stdClass();
        $data->cart = $this->cart->contents();
        if (count($data->cart)) {
            foreach($data->cart as &$prod) {
                //$product = (array) $this->products_m->get($prod['id']);
                if ($prod['module'] == 'store') {
                    $product = (array) $this->pyrocache->model('products_m', 'get', array($prod['id']), 3600);
                    $prod = array_merge($product, $prod);
                }
            };
        }

        if (count($data->cart)) {
            $data->items_exist = TRUE;
        } else {
            $data->items_exist = FALSE;
        }

        $this->template->title($this->module_details['name'])
                ->build('cart', $data);
    }

    public function add($id = NULL, $quantity = 1) {
        $data = array();

        $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'store/cart';

        $data = array(
            'id' => $id,
            'uri' => '',
            'module' => 'store',
            'qty' => $quantity,
            'price' => 0,
            'name' => '',
            'options' => array()
        );
        $post_data = $this->input->post() ? $this->input->post() : array();


        $data = array_merge($data, $post_data);
        if (is_array($data['options']) && count($data['options'])) {
            
            foreach ($data['options'] as $key => &$value) {
                if (is_array($value)) {
                    $data['options'][$key] = implode(', ', $value);
                }
                if (empty($data['options'][$key])) {
                    unset($data['options'][$key]);
                }
            }
        } else {
            $data['options'] = array();
        }

        if ($this->input->post('quantity')) {
            $data['qty'] = $this->input->post('quantity');
        }

        if (empty($post_data) || $data['module'] == 'store') {
            $item = $this->products_m->get($data['id']);
            if (!$item) {
                $this->session->set_flashdata('error', lang('store:cart_error'));
                redirect($redirect);
            }

            $price = $item->new_price_tax && $item->new_price_tax < $item->price_tax ? $item->new_price_tax : $item->price_tax;
            if (count($data['options']))
                $price = $price + $this->options_m->count_price($data['id'], $data['options']);
            
            $data['name'] = $item->name;
            $data['price'] = $price;
            $data['uri'] = 'store/product/' . $item->slug;
        }
        $data['name'] = convert_accented_characters($data['name']);
        $data['name'] = preg_replace("/[^A-Za-z0-9 ]/", "", $data['name']);

        $cart = $this->cart->insert($data);

        if ($cart) {
            $this->session->set_flashdata('success', lang('store:cart_success') . ': ' . $data['name']);
        } else {
            $this->session->set_flashdata('error', lang('store:cart_error') . ': ' . $data['name']);
        }
        redirect($redirect);
    }

    public function update() {
        $data = $this->input->post();

        if ($data['coupon']) {
            $this->load->model('coupons_m');
            $coupon = $this->coupons_m->validate($data['coupon']);
            if ($coupon) {
                $data['coupon'] = array('id' => $coupon->id, 'name' => $coupon->name, 'code' => $coupon->code, 'amount' => $coupon->amount, 'type' => $coupon->type);
                $this->session->set_flashdata('success', lang('store:coupon_success'));
            } else {
                unset($data['coupon']);
                $this->session->set_flashdata('notice', lang('store:coupon_error'));
            }
        }

        $this->cart->update($data);
        redirect('store/cart');
    }

    public function delete($rowid) {
        $data = array(
            'rowid' => $rowid,
            'qty' => 0
        );

        $this->cart->update($data);
        redirect('store/cart');
    }

    public function destroy() {
        $uri = $this->input->get_post("redirect_to") ? $this->input->get_post("redirect_to")
            : ($this->uri->total_segments() > 3 ? $this->uri->segment_array() : 'store/cart');

        if (is_array($uri)) {
            unset($uri[1],$uri[2], $uri[3]);
            $uri = implode('/', $uri);
        }

        $this->cart->destroy();
        redirect($uri);
    }

}