<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Coupons_m extends MY_Model {

    public $validate = array();

    public function __construct() {
        parent::__construct();
        $this->_table = "store_coupons";
        $this->validate = array(
            array(
                'field' => 'name',
                'label' => 'lang:store:name',
                'rules' => 'required|trim|max_length[100]'
            ),
            array(
                'field' => 'code',
                'label' => 'lang:store:code',
                'rules' => 'required|trim|required|trim|max_length[16]'
            ),
            array(
                'field' => 'type',
                'label' => 'lang:store:type',
                'rules' => 'required|trim'
            ),
            array(
                'field' => 'amount',
                'label' => 'lang:store:value',
                'rules' => 'required|numeric|trim|floatval'
            ),
            array(
                'field' => 'uses_limit',
                'label' => 'lang:store:uses_limit',
                'rules' => 'required|trim|numeric|is_natural'
            ),
            array(
                'field' => 'uses_order',
                'label' => 'lang:store:uses_limit',
                'rules' => 'required|trim|numeric|is_natural'
            ),
            array(
                'field' => 'uses_user',
                'label' => 'lang:store:uses_limit',
                'rules' => 'required|trim|numeric|is_natural'
            ),
            array(
                'field' => 'uses_limit',
                'label' => 'lang:store:uses_limit',
                'rules' => 'required|trim|numeric|is_natural'
            ),
            array(
                'field' => 'date_start',
                'label' => 'lang:store:date_start',
                'rules' => 'required|trim'
            ),
            array(
                'field' => 'date_end',
                'label' => 'lang:store:date_end',
                'rules' => 'required|trim'
            ),
        );

        $this->before_create = array('format_values');
    }

    public function format_values($data) {
        $data['date_start'] = strtotime($data['date_start']);
        $data['date_end'] = strtotime($data['date_end']);
        unset($data['btnAction']);
        return $data;
    }

    public function update($primary_value, $data, $skip_validation = FALSE) {
        $data = $this->format_values($data);
        return parent::update($primary_value, $data, $skip_validation);
    }

    public function get_all() {
        $this->db->select('store_coupons.*, COUNT(*) AS used');
        $this->db->join('store_coupon_uses', 'store_coupon_uses.coupon_id = store_coupons.id', 'left');
        $this->db->group_by('store_coupons.id');
        return parent::get_all();
    }

    public function validate($code) {
//        $coupons_prefixed = $this->db->dbprefix('store_coupons');

        $this->db->select('store_coupons.*');
//        $this->db->join('store_coupon_uses', 'store_coupon_uses.coupon_id = store_coupons.id', 'left');
//        $this->db->group_by('store_coupons.id');
        $this->db->where('date_start <', time());
        $this->db->where('date_end >', time());

        $coupon = $this->coupons_m->get_by('code', $code);

        if (!$coupon) {
            return FALSE;
        }

        $coupon->total_uses = $this->db->from('store_coupon_uses')->where('coupon_id', $coupon->id)->count_all_results();

        if ($coupon->total_uses >= $coupon->uses_limit) {
            return FALSE;
        }

        if ($this->current_user) {
            $coupon->user_uses = $this->db->from('store_coupon_uses')->where('coupon_id', $coupon->id)->where('user_id', $this->current_user->id)->count_all_results();
            if ($coupon->user_uses >= $coupon->uses_user) {
                return FALSE;
            }
        }

        return $coupon;
    }

    public function used($id, $user, $order) {
        $input = array();

        $input['coupon_id'] = $id;
        $input['user_id'] = $user;
        $input['order_id'] = $order;
        $input['date'] = time();

        return $this->db->insert('store_coupon_uses', $input);
    }

}