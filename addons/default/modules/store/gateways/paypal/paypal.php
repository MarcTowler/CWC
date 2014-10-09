<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Gateway_Paypal extends PaymentGateway {

    public $title = 'Paypal Simple GateWay';
    public $description = 'Process Payments via Paypal Account or Credit Card';
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
            'field' => 'options[account]',
            'label' => 'Paypal Account',
            'rules' => 'trim|max_length[100]|valid_email|required'
        ),
        array(
            'field' => 'options[image]',
            'label' => 'Image',
            'rules' => 'trim|max_length[100]'
        ),
        array(
            'field' => 'options[auto]',
            'label' => 'Self submit',
            'rules' => 'trim|max_length[100]|numeric'
        ),
    );

    public function __construct() {
        parent::__construct();
    }

    public function form($options) {
        

        return $options;
    }

    public function run($options) {
        $actions['live'] = 'https://www.paypal.com/cgi-bin/webscr';
        $actions['sandbox'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        $options['action'] = $actions[$options['service']];
        
        return $options;
    }

    public function callback() { //
        $data = !empty($_POST) ? $_POST : $_GET;
        
        $accepted = array(
            'Canceled_Reversal', 'Completed', 'Processed', 'Voided'
        );
        $pending = array (
            'Created', 'Pending'
        );
        $rejected = array (
            'Denied', 'Expired', 'Failed', 'Reversed', 'Refunded'
        );
        $reason = '';
        if (in_array($data['payment_status'], $accepted)) {
            $status = 'accepted';
            $reason = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        } else if (in_array($data['payment_status'], $rejected)) {
            $status = 'rejected';
            $reason = isset($data['reason_code']) ? $data['reason_code'] : 'reason unknown';
        } else {
            $status = 'pending';
            $reason = isset($data['pending_reason']) ? $data['pending_reason'] : 'reason unknown';
        }
        
        
        $result = array(
                    'order_id' => $data['custom'],
                    'transaction_id' => $data['txn_id'],
                    'status' => $status, // status: accepted, rejected, pending
                    'reason' => $reason,
                    'amount' => $data['mc_gross'],
                    'gateway' => 'paypal',
                );
        
        return $result;
    }

}
