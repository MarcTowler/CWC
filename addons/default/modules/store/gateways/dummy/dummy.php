<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Gateway_Dummy extends PaymentGateway {

    public $title = 'Dummy Gateway';
    public $description = 'Process Payments via Dummy Gateway';
    public $author = 'John Doe';
    public $website = 'http://john-doe.com/';
    public $version = '1.0';
    
    public $fields = array( // There you have to set all fields available in CP, along with validation rules
        array(
            'field' => 'options[mode]',
            'label' => 'Mode',
            'rules' => 'trim|max_length[100]|required'
        ),
        array(
            'field' => 'options[account]',
            'label' => 'Account',
            'rules' => 'trim|max_length[100]|valid_email|required'
        ),
    );

    public function __construct() {
        parent::__construct();
    }

    public function form($options) { 
        /* Available in backend: 
        Here you can create very specific data that can be passed to settings form in CP */
        return $options;
    }

    public function run($options) {
        /* Available in frontend: 
        Here you determine variables passed to payment form based on settings you set up in CP */
        $options['form_url'] = $options['mode'] == "sandbox" ? 'https://sandbox.payment.com' : 'https://secure.payment.com';
        return $options;
    }
    
    public function callback() { 
        /* Available only as callback: 
        Here you can process data returned from gateway server, and determine if it was successful  */
        
        $result = array(
                    'order_id' => '', // reference to users order in store, 
                    'transaction_id' => '', // reference to transaction number in gateway's server
                    'status' => '', // status can be only: accepted, rejected, pending
                    'reason' => '', // message returned from server; helpful if status is pending or rejected
                    'amount' => '', // amount gateway accepted - just in case user was insolvent
                    'gateway' => $this->uri->segment(4), // fetched from segment, as private var or hardtyped
                );
        
        return $result;
    }

}