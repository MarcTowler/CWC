<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Gateway_Authorize_aim extends PaymentGateway {

    public $title = 'Authorize.NET (AIM)';
    public $description = 'Process Payment using Authorize.Net Advanced Integration Method (Server Request)';
    public $author = 'Błażej Adamczyk';
    public $website = 'http://sein.com.pl/';
    public $version = '1.0';
    public $fields = array(
        array(
            'field' => 'options[env]',
            'label' => 'Environment',
            'rules' => 'trim|max_length[100]|required'
        ),
        array(
            'field' => 'options[id]',
            'label' => 'Api Login ID',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'options[key]',
            'label' => 'Transaction Key',
            'rules' => 'trim|required'
        ),
    );

    public $options = array(
        'env' => 'sandbox',
        'id' => '',
        'key' => '',
    );

    public function __construct() {
        parent::__construct();
        require 'src/autoload.php';
    }

    public function form($options) {

        return $options;
    }

    public function run($options, $data) {


        return $options;
    }

    public function callback($options) {
        define("AUTHORIZENET_API_LOGIN_ID", $options['id']);
        define("AUTHORIZENET_TRANSACTION_KEY", $options['key']);
        define("AUTHORIZENET_SANDBOX", $options['env'] == 'sandbox');

        $order = $this->input->post('order_id');

        $sale = new AuthorizeNetAIM;

        $sale->invoice_num = $order;

        $sale->setCustomField("order_id", $order);

        $sale->amount   = $this->input->post('amount');
        $sale->card_num = $this->input->post('card_num');
        $sale->exp_date = $this->input->post('exp_date');

        $response = $sale->authorizeAndCapture();

        if ($response->approved) {
            if ($this->input->is_ajax_request())
                echo '1';
            return array(
                'order_id' => $order,
                'transaction_id' => $response->transaction_id,
                'status' => 'accepted', // status: accepted, rejected, pending
                'reason' => $response->response_reason_text,
                'amount' => $response->amount,
                'data' => json_encode($response),
                'gateway' => 'authorize_aim',
            );
        }
        if ($this->input->is_ajax_request())
            echo '0';
        return array(
            'order_id' => $order,
            'transaction_id' => $response->transaction_id,
            'status' => $response->declined ? 'rejected' : 'pending', // status: accepted, rejected, pending
            'reason' => $response->response_reason_text,
            'amount' => $response->amount,
            'data' => json_encode($response),
            'gateway' => 'authorize_aim',
        );
    }
}
    