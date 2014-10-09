<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * This is a store module for PyroCMS
 *
 * @author 		Blazej Adamczyk
 * @website		http://sein.com.pl
 * @package 	PyroStore
 * @subpackage 	Cart Controller
 */
class PaymentGateway {

    //put your code here
    public $image = '';
    public $title = '';
    public $description = '';
    public $author = '';
    public $website = '';
    public $version = '';
    public $fields = array();
    public $options = array();

    function __construct() {
    }

    public function __get($var) {
        if (isset(get_instance()->$var)) {
            return get_instance()->$var;
        }
    }

}
