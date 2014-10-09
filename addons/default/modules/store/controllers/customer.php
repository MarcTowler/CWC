<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * This is a store module for PyroCMS
 *
 * @author 		Blazej Adamczyk
 * @website		http://sein.com.pl
 * @package 	PyroStore
 * @subpackage 	Customer Controller
 */
class Customer extends Public_Controller {

    public function __construct() {
        parent::__construct();

        if (!$this->current_user) {
            $this->session->set_flashdata('notice', lang('store:not_logged_in'));
            $this->session->set_userdata('redirect_to', 'store/customer');
            redirect('users/login');
        }
        
        $this->lang->load('store');
    }

    public function index() {

        // we're using the pagination helper to do the pagination for us. Params are: (module/method, total count, limit, uri segment)
        $this->template->set_breadcrumb(lang('store:store_title'), 'store');
        $this->template->set_breadcrumb(lang('store:customer_title'));
        $this->template->title($this->module_details['name'])
                ->build('customer/home');
    }

    public function addresses() {
        $this->load->model('address_m');
        $data = new stdClass();
        $data->items = $this->address_m->get_many_by('user_id', $this->current_user->id);

        $this->template->set_breadcrumb(lang('store:store_title'), 'store');
        $this->template->set_breadcrumb(lang('store:customer_title'), 'store/customer');
        $this->template->set_breadcrumb(lang('store:addresses_title'));
        $this->template->title($this->module_details['name'])
                ->build('customer/addresses', $data);
    }
    
    public function address($id = 0) {
        $this->load->model('address_m');
        $data = new stdClass();
        $data = $id ? $this->address_m->where('user_id', $this->current_user->id)->get($id) : (object) array();
        $data OR redirect('store/customer/addresses');
        $data->user_id = $this->current_user->id;
        
        if ($this->input->post()) {

            $input = $this->input->post();
            if ($id) {
                if ($this->address_m->update($id, $input)) {
                    $this->session->set_flashdata('success', lang('store:success'));
                    redirect('store/customer/addresses');
                } 
            } else {
                if ($this->address_m->insert($input)) {
                    $this->session->set_flashdata('success', lang('store:success'));
                    redirect('store/customer/addresses');
                }
            }
        }
        if (!$id) {
            foreach ($this->address_m->validate as $item) {
                $data->{$item['field']} = '';
            }
        }

        $this->template->set_breadcrumb(lang('store:store_title'), 'store')
                        ->set_breadcrumb(lang('store:customer_title'), 'store/customer')
                        ->set_breadcrumb(lang('store:addresses_title'), 'store/customer/addresses');
        
        $this->template->title($this->module_details['name'])
                ->build('customer/address', $data);
    }
    
    public function delete_address($id) {
        
        $this->db->where('user_id', $this->current_user->id)
                ->where('id', $id)->delete('store_addresses');
        
        redirect('store/customer/addresses');
    }

    public function wishlist() {
        $this->load->model('wishlist_m');
        $data = new stdClass();
        $data->items = $this->wishlist_m->get_many_by('store_wishlist.user_id', $this->current_user->id);

        $this->template->set_breadcrumb(lang('store:store_title'), 'store');
        $this->template->set_breadcrumb(lang('store:customer_title'), 'store/customer');
        $this->template->set_breadcrumb(lang('store:wishlist_title'));
        $this->template->title($this->module_details['name'])
                ->build('customer/wishlist', $data);
    }

    public function add_to_wishlist($product_id = 0) {
        $this->load->model('wishlist_m');
        $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'store/customer/wishlist';

        $product_id = $this->input->post('product_id') ? $this->input->post('product_id') : $product_id;

        if (is_numeric($product_id) && $product_id > 0) {
            $data = array('user_id' => $this->current_user->id, 'product_id' => $product_id);
            if ($this->wishlist_m->get_by($data)) {
                $this->session->set_flashdata('notice', lang('store:item_exists')); // TODO: translate
            } else {
                $data['added_on'] = time();
                $this->wishlist_m->insert($data);
                $this->session->set_flashdata('success', lang('store:success')); // TODO: translate
            } 
        } else {
            $this->session->set_flashdata('error', lang('store:error')); 
        }
        redirect($redirect);
    }
    
    public function remove_from_wishlist($product_id = 0) {
        $this->load->model('wishlist_m');

        if (is_numeric($product_id) && $product_id > 0) {
            $data = array('user_id' => $this->current_user->id, 'product_id' => $product_id);
            if ($this->wishlist_m->delete_by($data)) {
                $this->session->set_flashdata('success', lang('store:success')); 
            } else {
                $this->session->set_flashdata('error', lang('store:error'));
            } 
        } else {
            $this->session->set_flashdata('error', lang('store:error')); 
        }
        redirect('store/customer/wishlist');
    }
    

    public function orders() {
        $data = new stdClass();
        $this->load->model('orders_m');
        $data->items = $this->orders_m->get_many_by('store_orders.user_id', $this->current_user->id);

        $this->template->set_breadcrumb(lang('store:store_title'), 'store');
        $this->template->set_breadcrumb(lang('store:customer_title'), 'store/customer');
        $this->template->set_breadcrumb(lang('store:orders_title'));
        $this->template->title($this->module_details['name'])
                ->build('customer/orders', $data);
    }

    public function order($id) {
        $this->load->model('orders_m');
        $order = $this->orders_m->where('user_id', $this->current_user->id)->get($id);
        if (!$order) {
            Modules::run('pages/_remap', '404');
            exit;
        }
        if ($this->input->post('comment')) {
            if ($this->orders_m->customer_comment($id, $this->input->post('comment'))) {
                $this->session->set_flashdata('success', lang('store:success'));
                redirect('store/customer/order/' . $id);
            } else {
                $this->session->set_flashdata('error', lang('store:error'));
                redirect('store/customer/order/' . $id);
            }
        }

        $shipping = $this->orders_m->get_address($order->billing_address_id);
        $invoice = $this->orders_m->get_address($order->delivery_address_id);
        
        $shipping_method = $this->db->where('id', $order->shipment_method_id)->get('store_shipments')->row();
        $payment_method = $this->db->where('id', $order->payment_method_id)->get('store_gateways')->row();
        $order->shipping_method = $shipping_method->name;
        $order->payment_method = $payment_method->name;
        
        $history = $this->db->where('order_id', $id)->get('store_order_history')->result();
        $transactions = $this->db->where('order_id', $id)->get('store_transactions')->result();

        $contents = $this->orders_m->get_contents($order->id);

        $this->template->title($this->module_details['name'])
                ->set('order', $order)
                ->set('shipping', $shipping)
                ->set('invoice', $invoice)
                ->set('contents', $contents)
                ->set('history', $history)
                ->set('transactions', $transactions)
                ->build('customer/order');
    }

}