<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Affiliate_m extends MY_Model {

    var $_discount = 0;
    public function __construct() {
        $this->_table = "";
        parent::__construct();
    }
    
    public function get_all() {
        $this->db->select('store_affiliate_groups.*, groups.description AS name');
        $this->db->join('groups', 'groups.id = store_affiliate_groups.group_id');
        return parent::get_all();
    }
    
    public function is_affiliate() {
        if ($this->current_user) {
            $user = $this->db->where('user_id', $this->current_user->id)->get('store_affiliate_users')->row();
            if ($user) {
                $this->_discount = $user->discount;
                return TRUE;
            }
            $group = $this->db->where('group_id', $this->current_user->group_id)->get('store_affiliate_groups')->row();
            if ($group) {
                $this->_discount = $group->discount;
                return TRUE;
            }
        }
        return FALSE;
    }
    

    public function get_discount() {
        if ($this->is_affiliate()) {
            return $this->_discount;
        }
        return 0;
    }
    
    public function get_multiplier() {
        if ($this->is_affiliate()) {
            $multi = (100 - $this->_discount) / 100;
            return $multi;
        }
        return 1;
    }

}