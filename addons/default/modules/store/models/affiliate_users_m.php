<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Affiliate_users_m extends MY_Model {

    public function __construct() {
        $this->_table = "store_affiliate_users";
        parent::__construct();
    }
    
    public function get_all() {
        $this->db->select('store_affiliate_users.*, profiles.display_name AS name');
        $this->db->join('profiles', 'profiles.user_id = store_affiliate_users.user_id');
        return parent::get_all();
    }

}