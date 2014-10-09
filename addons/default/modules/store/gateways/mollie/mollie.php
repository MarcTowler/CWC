<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Gateway_Mollie extends PaymentGateway {

    public $title = 'Mollie API Payment';
    public $description = 'Accepting iDEAL, Mister Cash, SOFORT Banking, Creditcard, SEPA Bank transfer, Bitcoin, PayPal, and paysafecard online payments';
    public $author = 'Błażej Adamczyk';
    public $website = 'http://sein.com.pl/';
    public $version = '1.0';
    
    public $fields = array(
        array(
            'field' => 'options[api]',
            'label' => 'Api Key',
            'rules' => 'trim|max_length[100]|required'
        ),
    );
    public $options = array(
        'api' => ''
        );

    public function __construct() {
        parent::__construct();
        require_once dirname(__FILE__) . "/src/Mollie/API/Autoloader.php";
        $this->load->driver('cache', array('adapter' => 'file', 'backup' => 'apc'));
    }

    public function form($options) {
        return $options;
    }

    public function run($options, $data) {
        
        //$order_id = $data->order->id;

        $protocol = isset($_SERVER['HTTPS']) && strcasecmp('off', $_SERVER['HTTPS']) !== 0 ? "https" : "http";
        $hostname = $_SERVER['HTTP_HOST'];
        $path     = dirname(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF']);

        $mollie = new Mollie_API_Client;
        $mollie->setApiKey($options['api']);

        $payment = $mollie->payments->create(array(
            "amount"       => $data->order->total + $data->order->shipping,
            "description"  => "Order #{$data->order->id} from ".Settings::get('site_name'),
            "redirectUrl"  => site_url('store/payments/callback/mollie')."?redirect=1&order_id=".$data->order->id,
            "metadata"     => array(
                "order_id" => $data->order->id,
            ),
        ));

        if (!$this->cache->save('mollie_order_'.$data->order->id, $payment->id, 604800)) {
            log_message('error', 'cache not supported');
        }
        //echo $this->cache->get('mollie_order_'.$data->order->id);
        header("Location: " . $payment->getPaymentUrl());
        
        return $options;
    }

    public function callback($options) { //
        $order_id = $this->input->get('order_id');
        $mollie = new Mollie_API_Client;
        $mollie->setApiKey($options['api']);
        
        $payment_id = $this->cache->get('mollie_order_'.$order_id);

        //echo $order_id;
        $result = array(
            'order_id' => $order_id,
            'transaction_id' => '',
            'status' => 'pending', 
            'reason' => '',
            'amount' => 0,
            'data' => '',
            'gateway' => 'mollie',
        );
        if ($payment_id) {
            //$this->cache->delete('mollie_order_'.$order_id);

            $payment = $mollie->payments->get($payment_id);
            $result = array_merge($result, array(
                'transaction_id' => $payment_id,
                'reason' => $payment->method,
                'amount' => $payment->amount,
                'data' => json_encode($payment),
            ));

            if ($payment->status == 'paid') {
                $result['status'] = 'accepted';
            } elseif ($payment->status == 'expired' || $payment->status == 'cancelled') {
                $result['status'] = 'rejected';
            }
        }
        return $result;
    }

}
