<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Gateway_Cod extends PaymentGateway {

    public $title = 'Cash on Delivery';
    public $description = 'Payment on delivery';
    public $author = 'Błażej Adamczyk';
    public $website = 'http://sein.com.pl/';
    public $version = '1.0';
    
    public $fields = array(
//        array(
//            'field' => 'options[cost]',
//            'label' => 'Additional cost',
//            'rules' => 'trim|max_length[100]|required|float_val'
//        ),
    );

    public function __construct() {
        parent::__construct();
    }

    public function form($options) {
        return $options;
    }

    public function run($options) {
        redirect('store/payments/success');
        return $options;
    }

}
