<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Gateway_GCheckout extends PaymentGateway {

    public $title = 'Google Checkout';
    public $description = 'Process Payments via Google Wallet';
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
            'field' => 'options[merchant_id]',
            'label' => 'Merchant ID',
            'rules' => 'trim|max_length[100]|required'
        ),
        array(
            'field' => 'options[merchant_key]',
            'label' => 'Merchant Key',
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
        require_once('library/googlecart.php');
        require_once('library/googleitem.php');
        require_once('library/googleshipping.php');
        require_once('library/googletax.php');

        $actions['live'] = 'https://checkout.google.com/api/checkout/v2/checkout/Merchant/';
        $actions['sandbox'] = 'https://sandbox.google.com/checkout/api/checkout/v2/checkout/Merchant/';
        $options['action'] = $actions[$options['service']] . $options['merchant_id'];


        return $options;
    }

    public function callback($options) {
        $this->load->driver('cache', array('adapter' => 'file', 'backup' => 'apc'));

        require_once('library/googleresponse.php');
        require_once('library/googlemerchantcalculations.php');
        require_once('library/googleresult.php');
        require_once('library/googlerequest.php');

        $merchant_id = $options['merchant_id'];  // Your Merchant ID
        $merchant_key = $options['merchant_key'];  // Your Merchant Key
        $server_type = $options['service'];  // change this to go live
        $currency = Settings::get('store_currency_code');  // set to GBP if in the UK
        //$certificate_path = ""; // set your SSL CA cert path

        $log_folder = APPPATH . 'logs/';
        define('ERROR_LOG_FILE', $log_folder . 'googleerror' . date('-Y-m-d') . '.php');
        define('RESPONSE_LOG_FILE', $log_folder . 'googlemessage' . date('-Y-m-d') . '.php');


        $Gresponse = new GoogleResponse($merchant_id, $merchant_key);

        $Gresponse->SetLogFiles(ERROR_LOG_FILE, RESPONSE_LOG_FILE, L_ALL);

        $xml_response = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : file_get_contents("php://input");

        if (!strpos($xml_response, "xml")) {
            $serial_array = array();
            parse_str($xml_response, $serial_array);
            isset($serial_array["serial-number"]) OR die('No Serial Number');
            $serial_number = $serial_array["serial-number"];

            //Request XML notification
            $Grequest = new GoogleNotificationHistoryRequest($merchant_id, $merchant_key, $server_type);
            $raw_xml_array = $Grequest->SendNotificationHistoryRequest($serial_number);

            if ($raw_xml_array[0] != 200) {
                log_message('error', $raw_xml_array[0]);
                //Add code here to retry with exponential backoff
            } else {
                $raw_xml = $raw_xml_array[1];
            }
            $Gresponse->SendAck($serial_number, false);
        } else {
            $Gresponse->SetMerchantAuthentication($merchant_id, $merchant_key);
            $status = $Gresponse->HttpAuthentication();
            if (!$status) {
                log_message('error', 'Google Checkout Authentication Failed');
                die('authentication failed');
            }
            $raw_xml = $xml_response;
            log_message('debug', $raw_xml);
            $Gresponse->SendAck(null, false);
        }


        if (get_magic_quotes_gpc()) {
            $raw_xml = stripslashes($raw_xml);
        }

        //Parse XML to array
        list($root, $data) = $Gresponse->GetParsedXML($raw_xml);

        log_message('debug', $root);
        log_message('debug', print_r($data, TRUE));

        $store_order_id = 0;
        $google_order_status = 'pending';
        $google_order_amount = 0;

        $google_order_number = $data[$root]['google-order-number']['VALUE'];

        switch ($root) {
            case "new-order-notification": {
                    $store_order_id = $data[$root]['shopping-cart']['merchant-private-data']['VALUE'];
                    if (!$this->cache->save($google_order_number, $store_order_id, 604800)) {
                        log_message('error', 'cache not supported');
                    }
                    $google_order_status = 'pending';

//                    $new_financial_state = $data[$root]['new-financial-order-state']['VALUE'];
//                    //$new_fulfillment_order = $data[$root]['new-fulfillment-order-state']['VALUE'];
//                    $Grequest = new GoogleRequest($merchant_id, $merchant_key, $server_type, $currency);
//
//                    if ($new_financial_state == 'CHARGEABLE') {
//                        $Grequest->SendProcessOrder($data[$root]['google-order-number']['VALUE']);
//                        //$Grequest->SendChargeOrder($data[$root]['google-order-number']['VALUE'],'');
//                    }
                    break;
                }
            case "risk-information-notification": {
                    break;
                }
            case "charge-amount-notification": {
                    $google_order_amount = $data[$root]['total-charge-amount']['VALUE'];
                    $google_order_status = 'accepted';
                    $store_order_id = $this->cache->get($google_order_number);
                    if ($store_order_id) {
                        $this->cache->delete($google_order_number);
                    }

                    break;
                }
            case "authorization-amount-notification": {

                    break;
                }
            case "refund-amount-notification": {
                    break;
                }
            case "chargeback-amount-notification": {
                    break;
                }
            case "order-numbers": {
                    break;
                }
            case "invalid-order-numbers": {
                    break;
                }
            case "order-state-change-notification": {
                    $google_order_status = $data[$root]['new-financial-order-state']['VALUE'] == 'CHARGED' ? 'accepted' : 'pending';

                    $store_order_id = $this->cache->get($google_order_number);
                    break;
                }
            default: {
                    break;
                }
        }
        //$Gresponse->SendAck(null, false);
        $result = array(
            'order_id' => $store_order_id,
            'transaction_id' => $google_order_number,
            'status' => $google_order_status, //'pending', // status: accepted, rejected, pending
            'reason' => $root,
            'amount' => $google_order_amount,
            'gateway' => 'checkout',
            'data' => json_encode($data)
        );

        log_message('debug', $result);

        return $result;
    }

    function get_arr_result($child_node) {
        $result = array();
        if (isset($child_node)) {
            if (is_associative_array($child_node)) {
                $result[] = $child_node;
            } else {
                foreach ($child_node as $curr_node) {
                    $result[] = $curr_node;
                }
            }
        }
        return $result;
    }

    /* Returns true if a given variable represents an associative array */

    function is_associative_array($var) {
        return is_array($var) && !is_numeric(implode('', array_keys($var)));
    }

}

