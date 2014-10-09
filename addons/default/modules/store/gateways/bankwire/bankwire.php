<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Gateway_Bankwire extends PaymentGateway {

    public $title = 'Bank Wire';
    public $description = 'Process Payment via bank money transfer';
    public $author = 'Błażej Adamczyk';
    public $website = 'http://sein.com.pl/';
    public $version = '1.0';
    public $fields = array(
        array(
            'field' => 'options[account]',
            'label' => 'Account Number',
            'rules' => 'trim|max_length[100]|required'
        ),
        array(
            'field' => 'options[bic]',
            'label' => 'Bank BIC ID',
            'rules' => 'trim|max_length[100]'
        ),
        array(
            'field' => 'options[receiver]',
            'label' => 'Receiver',
            'rules' => 'trim|max_length[100]|required'
        ),
        array(
            'field' => 'options[address]',
            'label' => 'Address',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'options[email]',
            'label' => 'Send Email',
            'rules' => 'trim|required'
        ),
    );

    public function __construct() {
        parent::__construct();
    }

    public function form($options) {


        return $options;
    }

    public function run($options) {
        if (isset($options['email']) && $options['email'] === 1) {
            $this->load->model('orders_m');
            $order_token = $this->uri->segment(4);
            $order = $this->orders_m->get_by('token', $order_token);
            $address = $this->orders_m->get_address($order->billing_address_id);

            $bankwire_notify['slug'] = 'store_notify';
            $bankwire_notify['to'] = $address->email;
            $bankwire_notify['order_id'] = $order->id;
            $bankwire_notify['status'] = 'pending';
            $bankwire_notify['comment'] = '
            Account Number: ' . $options['account'] . '<br />
            Bank BIC: ' . $options['bic'] . '<br />
            Title: ' . $order->id . '/' . date('m/Y', $order->order_date) . '<br />
            Receiver: ' . $options['receiver'] . '<br />
            Address: ' . $options['address'] . '<br />
            Amount: ' . ($order->shipping + $order->total) . ' {{ settings:currency }}';

            Events::trigger('email', $bankwire_notify, 'array');
        }

        return $options;
    }

}
