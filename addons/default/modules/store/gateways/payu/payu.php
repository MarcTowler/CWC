<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Gateway_Payu extends PaymentGateway {

    public $title = 'Płatności PayU';
    public $description = 'Zapłać z dowolnego banku za pomocą PayU';
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
            'field' => 'options[pos_id]',
            'label' => 'POS ID',
            'rules' => 'trim|max_length[100]|required'
        ),
        array(
            'field' => 'options[pos_auth_key]',
            'label' => 'Klucz POS AUTH',
            'rules' => 'trim|max_length[100]|required'
        ),
        array(
            'field' => 'options[key1]',
            'label' => 'Klucz API #1',
            'rules' => 'trim|max_length[100]|required'
        ),
        array(
            'field' => 'options[key2]',
            'label' => 'Klucz API #2',
            'rules' => 'trim|max_length[100]|required'
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
        $actions['live'] = 'https://www.platnosci.pl/paygw/UTF/NewPayment';
        $actions['sandbox'] = 'https://sandbox.payu.pl/paygw/UTF/NewPayment';
        $options['action'] = $actions[$options['service']];
        
        return $options;
    }

    public function callback($settings) { //
        $code[1] = 'new';
        $code[2] = 'canceled';
        $code[3] = 'rejected';
        $code[4] = 'initiated';
        $code[5] = 'awaiting';
        $code[6] = 'unauthorized';
        $code[7] = 'denied';
        $code[99] = 'accepted';
        $code[888] = 'error';
        
        $status_array[1] = 'pending';
        $status_array[2] = 'rejected';
        $status_array[3] = 'rejected';
        $status_array[4] = 'pending';
        $status_array[5] = 'pending';
        $status_array[6] = 'rejected';
        $status_array[7] = 'rejected';
        $status_array[99] = 'accepted';
        $status_array[888] = 'rejected';

        $server = $settings['service'] == 'sandbox' ? 'sandbox.payu.pl' : 'www.platnosci.pl';
        $server_script = '/paygw/UTF/Payment/get';
        $pos_id = $settings['pos_id'];
        $key1 = $settings['key1'];
        $key2 = $settings['key2'];


        if (!isset($_POST['pos_id']) || !isset($_POST['session_id']) || !isset($_POST['ts']) || !isset($_POST['sig']))
            return array(
                'order_id' => 0,
                'transaction_id' => '',
                'status' => 'pending',
                'reason' => 'missing params',
                'amount' => 0,
                'gateway' => 'payu',
                'data' => '',
            );

        if ($_POST['pos_id'] != $pos_id)
            return array(
                'order_id' => 0,
                'transaction_id' => $_POST['sig'],
                'status' => 'pending',
                'reason' => 'wrong pos id',
                'amount' => 0,
                'gateway' => 'payu',
                'data' => '',
            );

        $sig = md5($_POST['pos_id'] . $_POST['session_id'] . $_POST['ts'] . $key2);
        if ($_POST['sig'] != $sig)
            return array(
                'order_id' => 0,
                'status' => 'pending',
                'transaction_id' => $_POST['sig'],
                'reason' => 'wrong pos signature',
                'amount' => 0,
                'gateway' => 'payu',
                'data' => '',
            );

        $ts = time();
        $sig = md5($pos_id . $_POST['session_id'] . $ts . $key1);
        $parameters = "pos_id=" . $pos_id . "&session_id=" . $_POST['session_id'] . "&ts=" . $ts . "&sig=" . $sig;

        $fsocket = false;
        $curl = false;
        $result = false;

        if ((PHP_VERSION >= 4.3) && ($fp = @fsockopen('ssl://' . $server, 443, $errno, $errstr, 30))) {
            $fsocket = true;
        } elseif (function_exists('curl_exec')) {
            $curl = true;
        }

        if ($fsocket == true) {
            $header = 'POST ' . $server_script . ' HTTP/1.0' . "\r\n" .
                    'Host: ' . $server . "\r\n" .
                    'Content-Type: application/x-www-form-urlencoded' . "\r\n" .
                    'Content-Length: ' . strlen($parameters) . "\r\n" .
                    'Connection: close' . "\r\n\r\n";
            @fputs($fp, $header . $parameters);
            $platnosci_response = '';
            while (!@feof($fp)) {
                $res = @fgets($fp, 1024);
                $platnosci_response .= $res;
            }
            @fclose($fp);
        } elseif ($curl == true) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://" . $server . $server_script);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $platnosci_response = curl_exec($ch);
            curl_close($ch);
        } else {
            return array(
                'order_id' => 0,
                'transaction_id' => $_POST['session_id'],
                'status' => 'pending',
                'reason' => 'wrong method',
                'amount' => 0,
                'gateway' => 'payu',
                'data' => '',
            );
        }

        $string = substr($platnosci_response, strpos($platnosci_response, '<'));
        
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->loadXML($string);

        $result = $this->_xml_to_arr($doc->documentElement);

        if ($result) {  //--- rozpoznany status transakcji
            $order_id = $result['trans']['order_id'];
            $status = $status_array[$result['trans']['status']];
            $amount = $result['trans']['amount'];
            $order_id = $result['trans']['order_id'];
            $session_id = $result['trans']['session_id'];

            $return = array(
                'order_id' => $order_id,
                'transaction_id' => $session_id,
                'status' => $status,
                'reason' => $code[$result['trans']['status']],
                'amount' => floatval($amount * 0.01),
                'gateway' => 'payu',
            );
            echo 'OK';
            return $return;
        } else {
            $return = array(
                'order_id' => 0,
                'transaction_id' => $_POST['session_id'],
                'status' => 'pending',
                'reason' => 'xml not parsed',
                'amount' => 0,
                'gateway' => 'payu',
                'data' => $platnosci_response,
            );
            return $return;
        }
    }
    
    private function _xml_to_arr($curr_node) {
        $val_array = array();
        $typ_array = array();

        foreach ($curr_node->childNodes as $node) {
            if ($node->nodeType == XML_ELEMENT_NODE) {

                $val = $this->_xml_to_arr($node);

                if (array_key_exists($node->tagName, $val_array)) {

                    if (!is_array($val_array[$node->tagName]) || $type_array[$node->tagName] == 'hash') {
                        $existing_val = $val_array[$node->tagName];
                        unset($val_array[$node->tagName]);
                        $val_array[$node->tagName][0] = $existing_val;
                        $type_array[$node->tagName] = 'array';
                    }
                    $val_array[$node->tagName][] = $val;
                } else {

                    $val_array[$node->tagName] = $val;
                    if (is_array($val)) {
                        $type_array[$node->tagName] = 'hash';
                    }
                } // end if array key exists
            } // end if elment node
        }// end for each

        if (count($val_array) == 0) {
            return $curr_node->nodeValue;
        } else {
            return $val_array;
        }
    }

}
