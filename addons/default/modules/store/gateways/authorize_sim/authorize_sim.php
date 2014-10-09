<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Gateway_Authorize_sim extends PaymentGateway {

    public $title = 'Authorize.NET (SIM)';
    public $description = 'Process Payment using Authorize.Net Server Integration Method (Remote Payment Form)';
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
            'field' => 'options[test]',
            'label' => 'Test Mode',
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
        array(
            'field' => 'options[currency]',
            'label' => 'Currency',
            'rules' => 'trim'
        ),
        array(
            'field' => 'options[auto]',
            'label' => 'Self submit',
            'rules' => 'trim|max_length[100]|numeric'
        ),
    );
    // Default values
    public $options = array(
        'env' => 'sandbox',
        'test' => 'true',
        'id' => '',
        'key' => '',
        'currency' => '',
        'auto' => '1',
    );

    // AUD, USD, CAD, EUR, GBP or NZD
    public $currencies = array(
        '' => 'Default', 
        'USD' => 'USD', 
        'AUD' => 'AUD',
        'CAD' => 'CAD',
        'EUR' => 'EUR',
        'GBP' => 'GBP',
        'NZD' => 'NZD',
    );

    public function __construct() {
        parent::__construct();
        require_once 'src/autoload.php'; 
    }

    public function form($options) {
        $this->options['currencies'] = $this->currencies;
        return $options;
    }

    public function run($options, $data) {

        $transaction_key = $options['key'];
        $amount = $data->order->total + $data->order->shipping;
        $fp_sequence = $data->order->id; // Any sequential number like an invoice number.
        $currency = 'USD'; // AUD, USD, CAD, EUR, GBP or NZD

        $options['server'] = $options['env'] == 'sandbox' ? 'https://test.authorize.net/gateway/transact.dll' : 'https://secure.authorize.net/gateway/transact.dll';

        $options['fingerprint'] = AuthorizeNetSIM_Form::getFingerprint($options['id'], $options['key'], $amount, $fp_sequence, time());

        return $options;
    }

    public function callback($options) {
        //$redirect_url = site_url('store/payments/success');

        $api_login_id = $options['id'];
        // $md5_setting = "E9eHdKK3QI4dWg7eWYCL2g=="; // Your MD5 Setting
        $response = new AuthorizeNetSIM($api_login_id, false);
        //print_r($response);
        if ($response->isAuthorizeNet()) {
            if ($response->approved) {
                $redirect_url = site_url('store/payments/success').'?response_code=1&transaction_id=' .$response->transaction_id;

                $this->session->set_flashdata('successs', 'Transaction Successful');
                $result = array(
                    'order_id' => $response->invoice_number ? intval($response->invoice_number) : $this->input->get('order_id'),
                    'transaction_id' => $response->transaction_id,
                    'status' => 'accepted', // status: accepted, rejected, pending
                    'reason' => $response->response_reason_text,
                    'amount' => $response->amount,
                    'data' => json_encode($response),
                    'gateway' => 'authorize_sim',
                );
                echo $this->snippet($redirect_url);
                return $result;
            } else {
                $redirect_url = site_url('store/payments/cancel').'?response_code=' . $response->response_code . '&response_reason_text=' . $response->response_reason_text;
                $result = array(
                    'order_id' => $response->invoice_number ? intval($response->invoice_number) : $this->input->get('order_id'),
                    'transaction_id' => $response->transaction_id,
                    'status' => 'rejected', // status: accepted, rejected, pending
                    'reason' => $response->response_reason_text,
                    'amount' => $response->amount,
                    'data' => json_encode($response),
                    'gateway' => 'authorize_sim',
                );
                echo $this->snippet($redirect_url);
                return $result;
            }
        } else {
            $redirect_url = site_url('store/payments/');
            $redirect_url .= $response->response_code == 1 ? '/success' : '/cancel';
            $result = array(
                    'order_id' => $response->invoice_number ? intval($response->invoice_number) : $this->input->get('order_id'),
                    'transaction_id' => $response->transaction_id,
                    'status' => $response->response_code == 1 ? 'accepted' : 'rejected', // status: accepted, rejected, pending
                    'reason' => $response->response_reason_text,
                    'amount' => $response->amount,
                    'data' => json_encode($response),
                    'gateway' => 'authorize_sim',
                );
            //echo AuthorizeNetDPM::getRelayResponseSnippet($redirect_url);
            echo $this->snippet($redirect_url);
            return $result;
        }
    }

    private function snippet($redirect_url)
    {
        return "<html><head><script language=\"javascript\">
                <!--
                setTimeout(function () {
                    window.location=\"{$redirect_url}\";
                }, 5000);
                //-->
                </script>
                </head><body>Please wait, you will be redirected in 5 seconds...<noscript><meta http-equiv=\"refresh\" content=\"5;url={$redirect_url}\"></noscript></body></html>";
    }
}
    