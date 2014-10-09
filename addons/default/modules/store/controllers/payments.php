<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * This is a store module for PyroCMS
 *
 * @author 		Blazej Adamczyk
 * @website		http://sein.com.pl
 * @package 	PyroStore
 * @subpackage 	Payments Controller
 */
class Payments extends Public_Controller {

    public function __construct() {
        parent::__construct();

        if ($this->settings->store_ssl_required and strtolower(substr(current_url(), 4, 1)) != 's') {
            redirect(str_replace('http:', 'https:', current_url()) . '?session=' . session_id());
            exit();
        }
        if ($this->input->get('session')) {
            session_id($this->input->get('session'));
            session_regenerate_id();
        }

        $this->load->library('store/basket', '', 'cart');
        $this->load->model('orders_m');
        $this->load->model('gateways_m');
        $this->load->model('shipments_m');
        $this->lang->load('store');
        //$order_id OR redirect('store');
        $this->data = new stdClass();
        
        

        //$this->data OR redirect('store');
    }

    public function index() {
        redirect('store');
    }

    /**
     * All items
     */
    public function process() {
        $order_id = $this->uri->segment(4);
        
        if (is_string($order_id)) {

            $this->data->order = $this->orders_m->get_by('token', $order_id);

            if (!$this->data->order) {
                show_404();
            }
            $this->data->items = $this->orders_m->get_contents($this->data->order->id);
            $this->data->billing = $this->orders_m->get_address($this->data->order->billing_address_id);
            $this->data->shipping_method = $this->shipments_m->get($this->data->order->shipment_method_id);
        }
        
        $gateway = $this->gateways_m->load($this->data->order->payment_method_id);
        $options = $gateway->run($gateway->options, $this->data);

        $this->data->options = $options;
        $this->data->gateway = $gateway;
        
        //$this->orders_m->update($this->data->order->id, array('token' => ''));
        $this->template->title($this->module_details['name'], lang('store:payment_title'))
                ->build('payments/complete', $this->data);
    }

    public function callback($slug = NULL) {
        
        $gateway = $this->gateways_m->load($slug);
        if (!$gateway) {
            return;
        }
        $this->load->model('transactions_m');
        
        $item = $gateway->callback($gateway->options);
        $item['timestamp'] = time();
        $item['data'] = isset($item['data']) ? $item['data'] : json_encode($_POST);
        
        log_message('debug', print_r($item, true));
        
        $this->transactions_m->insert($item);
        Events::trigger('payment_callback', $item);
        if ($this->input->get('redirect'))
            redirect('store/payments/success');
    }

    public function success() {
        if (strtolower(substr(current_url(), 4, 1)) == 's') {
            redirect(str_replace('https:', 'http:', site_url('store')) . '?session=' . session_id(), 'refresh');
        }
        $this->template->title($this->module_details['name'], lang('store:payment_title'))
                ->build('payments/success');
    }

    public function cancel() {
        if (strtolower(substr(current_url(), 4, 1)) == 's') {
            redirect(str_replace('https:', 'http:', site_url('store')) . '?session=' . session_id());
        } else {
            $this->session->set_flashdata('notice', 'Payment Canceled'); // TODO: Translate
            redirect('store');
        }
    }

}