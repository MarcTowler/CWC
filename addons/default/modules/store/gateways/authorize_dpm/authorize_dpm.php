<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Gateway_Authorize_dpm extends PaymentGateway {

    public $title = 'Authorize.NET (DPM)';
    public $description = 'Process Payment using Authorize.Net Direct Post Method (Local Payment Form)';
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
    );

    // TODO: Add redirect w8 information
    public function __construct() {
        parent::__construct();
        require 'src/autoload.php';
    }

    public function form($options) {

        return $options;
    }

    public function run($options, $data) {

        $url             = site_url('store/payments/callback/authorize_dpm').'?order_id='.$data->order->id;
        $api_login_id    = $options['id']; //'9aj7hFDbv5UL';
        $transaction_key = $options['key']; //'79V755CjnFCy2BkL';
        $fp_sequence     = $data->order->id;
        $amount          = $data->order->total + $data->order->shipping;

        $test = $options['test'] == 'true' ? true : false;

        $options['form'] =  AuthorizeNetDPM::getCreditCardForm($amount, $fp_sequence, $url,$api_login_id, $transaction_key, $test, $test);
       
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
                    'gateway' => 'authorize_dpm',
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
                    'gateway' => 'authorize_dpm',
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
                    'gateway' => 'authorize_dpm',
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
    