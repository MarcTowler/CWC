<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Gateway_Dotpay extends PaymentGateway {

    public $title = 'Dotpay';
    public $description = 'Płatność Dotpay';
    public $author = 'Błażej Adamczyk';
    public $website = 'http://sein.com.pl/';
    public $version = '1.0';
    
    public $fields = array(
        array(
            'field' => 'options[id]',
            'label' => 'Account Number',
            'rules' => 'trim|max_length[100]|required'
        ),
        array(
            'field' => 'options[lang]',
            'label' => 'Prefered Language',
            'rules' => 'trim|max_length[100]'
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
        $data = $_POST;
        $sid = $data['t_status'];
        $statuses = array(
            1 => 'new',
            2 => 'accepted',
            3 => 'rejected',
        );

        $result = array(
                    'order_id' => intval($order),
                    'transaction_id' => $data['txn_id'],
                    'status' => $statuses[$sid],
                    'reason' => '',
                    'amount' => $data['amount'],
                    'gateway' => 'dotpay',
                );
        
        return $result;
    }
}
