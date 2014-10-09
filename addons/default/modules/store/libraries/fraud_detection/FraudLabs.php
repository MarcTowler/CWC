<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of FraudLabs
 *
 * @author user
 */
class FraudLabs {

    private $licence = '';

    public function __get($var) {
        if (isset(get_instance()->$var)) {
            return get_instance()->$var;
        }
    }

    public function __construct($licence = '') {
        $this->licence = $licence;
        if (is_null($licence)) {
            throw new Exception('FraudLabs: Licence number cannot be NULL Object');
        }
        if (!is_string($licence)) {
            throw new Exception('FraudLabs: Licence number has to be string');
        }
        if (empty($licence)) {
            throw new Exception('FraudLabs: Licence number cannot be empty');
        }
    }

    public function check($data = array()) {
        $defaults = array(
            'ip' => $this->input->ip_address(),
            'city' => '',
            'region' => '',
            'postal' => '',
            'country' => '',
            'emaildomain' => '',
            'phone' => '',
            'bin' => '',
            'binname' => '',
            'binphone' => '',
            'shipadd' => '',
            'shipcity' => '',
            'shipregion' => '',
            'shippostal' => '',
            'shipcountry' => '',
            'queryid' => '00000',
        );
        
        $test_data = array_merge($defaults, $data);
        
        extract($test_data);

        $wsdl = "http://v1.fraudlabs.com/fraudlabswebservice.asmx?wsdl";


        $client = new SoapClient($wsdl);
        $parms = array(
            "IP" => $ip, 
            "CITY" => $city, 
            "REGION" => $region, 
            "POSTAL" => $postal, 
            "COUNTRY" => $country, 
            "DOMAIN" => $emaildomain,
            "PHONE" => $phone, 
            "BIN" => $bin, 
            "BINNAME" => $binname, 
            "BINPHONE" => $binphone, 
            "LICENSE" => $this->licence, 
            "SHIPADDR" => $shipadd,
            "SHIPCITY" => $shipcity, 
            "SHIPREGION" => $shipregion, 
            "SHIPPOSTAL" => $shippostal, 
            "SHIPCOUNTRY" => $shipcountry, 
            "QUERYID" => $queryid
            );

        $result = $client->FraudLabs($parms);
        log_message('error', print_r(array('request'=> $parms,  'response' =>$result), TRUE));
        return array('score' => isset($result->FRAUDSCORE) ? $result->FRAUDSCORE : 0, 'msg' => isset($result->MESSAGE) ? $result->MESSAGE : 0, 'data' => $result);
    }

}

