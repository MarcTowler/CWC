<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * This is a store module for PyroCMS
 *
 * @author 		Blazej Adamczyk
 * @website		http://sein.com.pl
 * @package 	PyroStore
 * @subpackage 	Checkout Controller
 */
class Checkout extends Public_Controller {

    var $customer_validation = array();
    var $address_validation = array();

    public function __construct() {
        parent::__construct();

        if ($this->settings->store_ssl_required and strtolower(substr(current_url(), 4, 1)) != 's') {
            redirect(str_replace('http:', 'https:', current_url()) . '?session=' . session_id());
            exit();
        }
        
        if ($this->settings->store_opc && $this->input->is_ajax_request()) {
             $this->template->set_layout(FALSE);
        }
        
        if ($this->input->get('session')) {
            session_id($this->input->get('session'));
            session_regenerate_id();
        }

        $this->lang->load('store');
        $this->lang->load('checkout');

        $this->address_validation = array(
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
            array(
                'field' => 'agreement',
                'label' => lang('store:agreement_field'),
                'rules' => 'required|trim'
            ),
        );

        $this->load->library('form_validation');
        $this->load->library('store/basket', '', 'cart');
        $this->load->model('payments_m');
        $this->load->model('affiliate_m');

        if (!$this->cart->total_items()) {
            redirect('store/cart');
        }
    }

    /**
     * STEP 1
     */
    public function index() {

        if ($this->current_user) {
            redirect('store/checkout/address');
        }

        if ($this->input->post()) {
            $input = $this->input->post();
            if ($input['customer'] == 'register') {
                $this->session->set_userdata('checkout_proccess', true);
                redirect('users/register');
            }
            if ($input['customer'] == 'guest') {
                $this->session->set_userdata('user_id', 0);
                redirect('store/checkout/address');
            }

            $this->session->set_flashdata('success', lang('store:success'));
            redirect('store/checkout/address');
        }


        $this->template->title($this->module_details['name'], lang('store:customer_title'))
                ->build('checkout/customer');
    }

    /**
     * STEP 2
     */
    public function address($type = "billing") {

        $this->form_validation->set_rules($this->address_validation);

        in_array($type, array('billing', 'delivery')) OR $type = 'billing';

        if ($this->current_user) {
            $data = $this->current_user;
            foreach ($this->address_validation AS $rule) {
                $data->{$rule['field']} = isset($this->current_user->{$rule['field']}) ? $this->current_user->{$rule['field']} : $this->input->post($rule['field']);
            }
            $data->address1 = isset($this->current_user->address_line1) ? $this->current_user->address_line1 : '';
            $data->address2 = isset($this->current_user->address_line2) ? $this->current_user->address_line2 : '';
            $data->city = isset($this->current_user->address_line3) ? $this->current_user->address_line3 : '';
            $data->zip = isset($this->current_user->postcode) ? $this->current_user->postcode : '';
            $data->addresses = $this->db->where('user_id', $this->current_user->id)->get('store_addresses')->result();
        } else {
            $data = new stdClass();
            foreach ($this->address_validation AS $rule) {
                $data->{$rule['field']} = $this->input->post($rule['field']);
            }
            $data->addresses = array();
        }

        if ($this->input->post('address_id')) {
            $this->session->set_userdata($type, $this->input->post('address_id'));
            if ($this->input->post('delivery')) {
                $this->session->set_userdata('delivery', $this->input->post('address_id'));
                redirect('store/checkout/shipping');
            } else if ($type == "delivery") {
                redirect('store/checkout/shipping');
            } else {
                redirect('store/checkout/address/delivery');
            }
        }

        if ($this->form_validation->run()) {

            $input = $this->input->post();

            $adr_id = $this->payments_m->set_address($input);

            $this->session->set_userdata($type, $adr_id);

            if ($this->input->post('delivery')) {
                $this->session->set_userdata('delivery', $adr_id);
                redirect('store/checkout/shipping');
            } else if ($type == "delivery") {
                redirect('store/checkout/shipping');
            } else {
                redirect('store/checkout/address/delivery');
            }
        }

        $this->template->title($this->module_details['name'], lang('store:customer_title'))
                ->build('checkout/address', $data);
    }

    /**
     * STEP 3
     */
    public function shipping() {

        $this->load->model('shipments_m');
        
        $data = new stdClass();
        $data->shipments = $this->shipments_m->get_carriers();

        $this->form_validation->set_rules('shipment_id', lang('store:shipment_field'), 'required|numeric|trim');

        if ($this->form_validation->run()) {
            $this->session->set_userdata(array(
                'shipment_id' => $this->input->post('shipment_id'),
                'shipping_cost' => $this->input->post('price_' . $this->input->post('shipment_id'))
            ));

            $this->session->set_flashdata('success', lang('store:success'));
            redirect('store/checkout/payment');
        }

        $this->template->title($this->module_details['name'], lang('store:shipment_title'))
                ->build('checkout/shipment', $data);
    }

    /**
     * STEP 4
     */
    public function payment() {

        //$this->load->config('store/payments');
        $this->load->model('gateways_m');
        $this->load->model('shipments_m');
        $this->load->model('coupons_m');
        $this->load->library('fraud', array(
            'mode' => $this->settings->store_fraudlabs_mode,
            'licence' => $this->settings->store_fraudlabs_licence
                ));
        
        $data = new stdClass();
        $data->payments = $this->gateways_m->get_many_by('enabled', 1);
        $data->shipping = $this->shipments_m->calculate($this->session->userdata('shipment_id'));

        $this->form_validation->set_rules('payment_method', lang('store:payment_method'), 'required|trim');

        if ($this->form_validation->run()) {
            $input = $this->input->post();
            $input['coupon'] = $this->cart->coupon() ? $this->cart->coupon('id') : 0;
            $fraud_test = $this->fraud->test($input);
            
            $input['fraud_score'] = $fraud_test['score'];
            $input['token'] = random_string('unique');
            
            $order_id = $fraud_test['result'] ? $this->payments_m->place_order($input, $this->cart->contents()) : FALSE;

            if ($order_id) {
                if ($input['coupon']) {
                    $this->coupons_m->used($this->cart->coupon('id'), $this->current_user ? $this->current_user->id : 0, $order_id);
                }
                
                $this->session->set_flashdata('success', lang('store:success'));

                Events::trigger('order_placed', $order_id);
                
                $clear_items = array('shipment_id' => '', 'shipping_cost' => '','billing' => '', 'delivery' => '');
                $this->session->unset_userdata($clear_items);
                $this->cart->destroy();
                //redirect('store/payments/process/' . $order_id);
                redirect('store/payments/process/' . $input['token']);
            } else {
                $this->session->set_flashdata('error', !empty($fraud_test['msg']) ? $fraud_test['msg'] : lang('store:error'));
                redirect(uri_string());
            }
        }

        $this->template->title($this->module_details['name'], lang('store:shipment_title'))
                ->build('checkout/payment', $data);
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
        if (!preg_match('/^([a-z0-9]{2,5})([-\s]?[a-z0-9]{3,4})?$/i', $str)) {
            $this->form_validation->set_message('_valid_zip', lang('store:valid_zip'));
            return FALSE;
        } else {
            return TRUE;
        }
    }

}