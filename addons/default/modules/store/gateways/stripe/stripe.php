<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Gateway_Stripe extends PaymentGateway {

    public $title = 'Stripe';
    public $description = 'Process Credit Card via Stripe';
    public $author = 'Błażej Adamczyk';
    public $website = 'http://sein.com.pl/';
    public $version = '1.0';
    
    public $fields = array(
        array(
            'field' => 'options[service]',
            'label' => 'Sandbox Mode',
            'rules' => 'trim|max_length[100]|required'
        ),
        array(
            'field' => 'options[api]',
            'label' => 'Api Key',
            'rules' => 'trim|max_length[100]|required'
        ),
        array(
            'field' => 'options[secret]',
            'label' => 'Secret Key',
            'rules' => 'trim|max_length[100]|required'
        ),
    );

    public function __construct() {
        parent::__construct();
    }

    public function form($options) {

        return $options;
    }

    public function run($options) {
        
        return $options;
    }
    
    public function callback($settings) {
        
        require_once 'lib/Stripe.php';
        
        Stripe::setApiKey($settings['secret']);

        // get the credit card details submitted by the form
        $token = $this->input->post('stripeToken');

        // create the charge on Stripe's servers - this will charge the user's card
        try {
            $charge = Stripe_Charge::create(array(
                "amount" => $this->input->post('amount') * 100, // amount in cents, again
                "currency" => $this->input->post('currency'),
                "card" => $token,
                "description" => $this->input->post('reference'))
            );
            $result = array(
                    'order_id' => $this->input->post('order_id'),
                    'transaction_id' => $token,
                    'status' => $charge['paid'] ? 'accepted' : 'rejected', // status: accepted, rejected, pending
                    'reason' => $charge['failure_message'] ? $charge['failure_message'] : '',
                    'amount' => $charge['amount'] * 0.01,
                    'gateway' => 'stripe',
                    'data' => json_encode($charge)
                );
        } catch (Exception $e) {
            $result = array(
                    'order_id' => $this->input->post('order_id'),
                    'transaction_id' => $token,
                    'status' => 'rejected', // status: accepted, rejected, pending
                    'reason' => $e->getMessage(),
                    'amount' => 0,
                    'gateway' => 'stripe',
                    'data' => ''
                );
        }
        
        
        return $result;
    }

}
