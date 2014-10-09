<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Transactions_m extends MY_Model {

    public function __construct() {
        parent::__construct();

        $this->_table = 'store_transactions';
    }

}
