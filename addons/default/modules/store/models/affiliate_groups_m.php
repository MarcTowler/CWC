<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Affiliate_groups_m extends MY_Model {

    public function __construct() {
        $this->_table = "store_affiliate_groups";
        parent::__construct();
    }
    
    public function get_all() {
        $this->db->select('store_affiliate_groups.*, groups.description AS name');
        $this->db->join('groups', 'groups.id = store_affiliate_groups.group_id');
        return parent::get_all();
    }

}