<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * This is a store module for PyroCMS
 *
 * @author 		Blazej Adamczyk
 * @website		http://sein.com.pl
 * @package 	PyroStore
 * @subpackage 	Store Module
 */
class Store extends Admin_Controller {

    protected $section = 'dashboard';

    public function __construct() {
        parent::__construct();

        // Load all the required classes
        $this->load->model('orders_m');
        $this->load->model('stats_m');
        $this->load->library('form_validation');
        $this->load->helper('store');
        $this->lang->load('store');
        $this->data = new stdClass();

        // We'll set the partials and metadata here since they're used everywhere
        $this->template->append_js('module::admin.js')                
                ->append_css('module::admin.css');
    }

    /**
     * List all items
     */
    public function index($offset = 0) {
        // here we use MY_Model's get_all() method to fetch everything
        $this->data->items = $this->orders_m->limit(Settings::get('records_per_page'))
                ->offset($offset)->order_by('order_date','desc')->get_all();
         $this->data->pagination = create_pagination('admin/store/index', $this->orders_m->count_all());

        $this->data->stats = $this->stats_m->get_all();
        // Build the view with store/views/admin/items.php
        $this->template->title($this->module_details['name'])
                //->append_metadata('<script type="text/javascript" src="https://www.google.com/jsapi"></script>')
                ->append_js('jquery/jquery.flot.js')
                ->append_js('module::stats.js')
                ->build('admin/dashboard', $this->data);
    }
    
    public function order($id) {
        $order = $this->orders_m->get($id);
        $order OR redirect('admin/store');
        
        $this->load->model('gateways_m');
        
        $shipping = $this->orders_m->get_address($order->billing_address_id);
        $invoice = $this->orders_m->get_address($order->delivery_address_id);
        $history = $this->db->where('order_id', $id)->get('store_order_history')->result();
        $transactions = $this->db->where('order_id', $id)->get('store_transactions')->result();
        $user = $this->db->where('user_id', $order->user_id)->get('profiles')->row();
        
        $contents = $this->orders_m->get_contents($order->id);
        $this->data->gateways = array_for_select($this->gateways_m->get_all(), 'slug', 'name');
        
        $this->template->title($this->module_details['name'])
                ->set('order', $order)
                ->set('shipping', $shipping)
                ->set('invoice', $invoice)
                ->set('contents', $contents)
                ->set('customer', $user)
                ->set('history', $history)
                ->set('transactions', $transactions)
                ->build('admin/order', $this->data);
    }

    public function update($order_id = NULL) {
        $order_id OR redirect('admin/store');
        
        if ($this->orders_m->update($order_id, $this->input->post())) {
            $this->session->set_flashdata('success', lang('store:success'));
        } else {
            $this->session->set_flashdata('error', lang('store:error'));
        }
        redirect('admin/store/order/'.$order_id);
    }
    
    public function transaction() {
        $this->load->model('transactions_m');
        $post = $this->input->post();
        $post['transaction_id'] = '';
        $post['data'] = '';
        $post['timestamp'] = strtotime($post['timestamp']);
        if ($post && $this->transactions_m->insert($post)) {
            $this->session->set_flashdata('success', lang('store:success'));
        } else {
            $this->session->set_flashdata('error', lang('store:error'));
        }
        redirect(isset($post['order_id']) ? 'admin/store/order/'.$post['order_id'].'#transactions-tab' : 'admin/store');
    }
    
    public function history() {
        $order_id = $this->input->post('order_id');
        if ($order_id && $this->orders_m->update_history($this->input->post())) {
            $this->session->set_flashdata('success', lang('store:success'));
        } else {
            $this->session->set_flashdata('error', lang('store:error'));
        }
        redirect('admin/store/order/'.$order_id);
    }

    public function status($id, $status) {
        $this->orders_m->update($id, array('status' => $status));
        redirect('admin/store');
    }
    
    public function delete($id = 0) {

        if (isset($_POST['btnAction']) AND is_array($_POST['action_to'])) {

            $this->orders_m->delete_many($this->input->post('action_to'));
        } elseif (is_numeric($id)) {
            $this->orders_m->delete($id);
        }
        redirect('admin/store');
    }
    
}
