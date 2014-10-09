<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Address_m extends MY_Model {

    public $validate = array();
    
    public function __construct() {
        parent::__construct();

        $this->_table = "store_addresses";
        $this->lang->load('store');
        $this->lang->load('checkout');

        $this->validate = array(
            array(
                'field' => 'first_name',
                'label' => lang('store:first_name_field'),
                'rules' => 'required|trim'
            ),
            array(
                'field' => 'last_name',
                'label' => lang('store:last_name_field'),
                'rules' => 'required|trim'
            ),
            array(
                'field' => 'company',
                'label' => lang('store:company_field'),
                'rules' => 'trim'
            ),
            array(
                'field' => 'nip',
                'label' => lang('store:nip_field'),
                'rules' => 'trim'
            ),
            array(
                'field' => 'phone',
                'label' => lang('store:phone_field'),
                'rules' => 'required|trim|callback__valid_phone'
            ),
            array(
                'field' => 'email',
                'label' => lang('store:email_field'),
                'rules' => 'required|trim|valid_email'
            ),
            array(
                'field' => 'address1',
                'label' => lang('store:address1_field'),
                'rules' => 'required|trim'
            ),
            array(
                'field' => 'address2',
                'label' => lang('store:address2_field'),
                'rules' => 'trim'
            ),
            array(
                'field' => 'city',
                'label' => lang('store:city_field'),
                'rules' => 'required|trim'
            ),
            array(
                'field' => 'state',
                'label' => lang('store:state_field'),
                'rules' => 'trim'
            ),
            array(
                'field' => 'country',
                'label' => lang('store:country_field'),
                'rules' => 'trim'
            ),
            array(
                'field' => 'zip',
                'label' => lang('store:zip_field'),
                'rules' => 'required|trim|callback__valid_zip'
            ),
        );
        if ($this->input->post('agreement')) {
            unset($_POST['submit']);
            unset($_POST['agreement']);
        } else {
            $this->validate[] = array(
                'field' => 'agreement',
                'label' => lang('store:agreement_field'),
                'rules' => 'required|trim'
            );
        }

        $this->load->library('form_validation');
    }

    public function _valid_phone($str) {
        if (!preg_match('/^\(?\+?\d*\)?[-\s.]?\d*[-\s.]?\d*[-\s.]?\d*[-\s.]?\d*$/x', $str)) {
            $this->form_validation->set_message('_valid_phone', lang('store:valid_phone'));
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function _valid_nip($str) {
        if (!preg_match('/^\+?[d- ]*$/i', $str)) {
            $this->form_validation->set_message('_valid_nip', lang('store:valid_nip'));
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function _valid_zip($str) {
        if (!preg_match('/^([0-9]{2,5})([-\s]?[0-9]{3,4})?$/i', $str)) {
            $this->form_validation->set_message('_valid_zip', lang('store:valid_zip'));
            return FALSE;
        } else {
            return TRUE;
        }
    }

}