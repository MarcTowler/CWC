<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of FraudLabs
 *
 * @author user
 */
class Fraud {

    private $mode = '0';
    private $licence;
    private $risk_limit = 70;
    private $status_fail = FALSE;

    public function __get($var) {
        if (isset(get_instance()->$var)) {
            return get_instance()->$var;
        }
    }

    public function __construct($config) {
        if (isset($config['mode']))
            $this->mode = $config['mode'];
        if (isset($config['licence']))
            $this->licence = $config['licence'];
    }

    public function test($order) {
        if (strval($this->mode) == '0') {
            log_message('debug', 'Fraud Detection disabled');
            return $this->test_result(TRUE, 0, 'Fraud Detection disabled');
        }
        
        if (empty($this->licence)) {
            log_message('error', 'Licence key not provided');
            return $this->test_result($this->status_fail, 0, 'Licence key not provided');
        }
        $lib_path = dirname(__FILE__).DIRECTORY_SEPARATOR.'fraud_detection'.DIRECTORY_SEPARATOR.'FraudLabs.php';
        if (!file_exists($lib_path)) {
            log_message('error', 'FraudLabs Library does not exist in location: '.$lib_path);
            return $this->test_result($this->status_fail, 0, 'FraudLabs Library does not exist');
        }
        
        require_once 'fraud_detection/FraudLabs.php';
        
        $library = new FraudLabs($this->licence);
        $data = $this->_prep_data($order);
        
        
        $result = $library->check($data);
        $accept = intval($result['score']) > $this->risk_limit ? FALSE : TRUE;
        return $this->test_result($accept, intval($result['score']), $result['msg']);
    }

    private function test_result($result = TRUE, $score = 0, $msg = '') {
        return array('result' => $result, 'score' => $score, $msg = '');
    }
    
    private function _prep_data($order) {
        class_exists('address_m') OR $this->load->model('address_m');
        $bill_to = $this->address_m->get($order['billing_address_id']);
        $ship_to = $this->address_m->get($order['delivery_address_id']);
        
        
        $data = array(
            'ip' => $this->input->ip_address(),
            'city' => $bill_to->city,
            'region' => $bill_to->state,
            'postal' => $bill_to->zip,
            'country' => $bill_to->country,
            'emaildomain' => substr($bill_to->email, strpos($bill_to->email, '@')),
            'phone' => $bill_to->phone,
            'bin' => '',
            'binname' => '',
            'binphone' => '',
            'shipadd' => $ship_to->address1.' '.$ship_to->address2,
            'shipcity' => $ship_to->city,
            'shipregion' => $ship_to->state,
            'shippostal' => $ship_to->zip,
            'shipcountry' => $ship_to->country,
            'queryid' => '00000',
        );
        
        return $data;
    }

}

