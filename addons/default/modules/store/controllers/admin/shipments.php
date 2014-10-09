<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * This is a store module for PyroCMS
 *
 * @author 		Blazej Adamczyk
 * @website		http://sein.com.pl
 * @package 	PyroStore
 * @subpackage 	Store Module
 */
class Shipments extends Admin_Controller {

    protected $section = 'shipments';

    public function __construct() {
        parent::__construct();

        // Load all the required classes
        $this->load->model('shipments_m');
        $this->load->library('form_validation');
        $this->lang->load('store');

        // Set the validation rules
        $this->item_validation_rules = array(
            array(
                'field' => 'name',
                'label' => 'lang:store:name',
                'rules' => 'trim|max_length[100]|required'
            ),
            array(
                'field' => 'desc',
                'label' => 'lang:store:desc',
                'rules' => 'trim'
            ),
            array(
                'field' => 'price',
                'label' => 'lang:store:price',
                'rules' => 'required|trim|numeric'
            ),
            array(
                'field' => 'url',
                'label' => 'lang:store:tracking_url',
                'rules' => 'trim|url'
            ),
            array(
                'field' => 'min',
                'label' => 'lang:store:min_amount',
                'rules' => 'trim'
            ),
            array(
                'field' => 'max',
                'label' => 'lang:store:max_amount',
                'rules' => 'trim'
            ),
            array(
                'field' => 'status',
                'label' => 'lang:store:status',
                'rules' => 'required|trim'
            ),
            array(
                'field' => 'type',
                'label' => 'lang:store:type',
                'rules' => 'required|trim'
            ),
            array(
                'field' => 'options[]',
                'label' => 'lang:store:options',
                'rules' => ''
            ),
            array(
                'field' => 'payment[]',
                'label' => 'lang:store:payment',
                'rules' => 'trim'
            ),
        );
        
    }

    /**
     * List all items
     */
    public function index() {
        // here we use MY_Model's get_all() method to fetch everything
        $items = $this->shipments_m->get_all();

        $this->template->title($this->module_details['name'])
                ->build('admin/shipments/items', array('items' => $items));
    }

    public function create() {

        $this->form_validation->set_rules($this->item_validation_rules);
        $this->data = (object) array();

        if ($this->form_validation->run()) {

            if ($this->shipments_m->create($this->input->post())) {

                $this->session->set_flashdata('success', lang('store:success'));
                redirect('admin/store/shipments/');
            } else {
                $this->session->set_flashdata('error', lang('store:error'));
                redirect('admin/store/shipments/create');
            }
        }

       foreach ($this->item_validation_rules AS $rule) {
            $this->data->{$rule['field']} = $this->input->post($rule['field']);
        }
        $this->data->payment = array();

        $this->template->title($this->module_details['name'], lang('store:create'))
                ->append_css('module::admin.css')
                ->append_js('jquery/jquery.ui.nestedSortable.js')
                ->build('admin/shipments/form', $this->data);
    }

    public function edit($id = 0) {
        $this->data = $this->shipments_m->get($id);
        $this->data->options = unserialize($this->data->options);
        //$this->data->payment = isset($this->data->payment) ? explode(',', $this->data->payment) : '';
        $this->form_validation->set_rules($this->item_validation_rules);

        if ($this->form_validation->run()) {

            $input = $this->input->post();
            unset($input['btnAction']);

            if ($this->shipments_m->update($id, $input)) {

                $this->session->set_flashdata('success', lang('store:success'));
                redirect('admin/store/shipments');
            } else {
                $this->session->set_flashdata('error', lang('store:error'));
                redirect('admin/store/shipments/edit/' . $id);
            }
        }
        
        $this->template->title($this->module_details['name'], lang('store:edit'))
                ->append_js('jquery/jquery.ui.nestedSortable.js')
                ->append_css('module::admin.css')
                ->build('admin/shipments/form', $this->data);
    }

    public function delete($id = 0) {

        if (isset($_POST['btnAction']) AND is_array($_POST['action_to'])) {

            $this->shipments_m->delete_many($this->input->post('action_to'));
        } elseif (is_numeric($id)) {
            $this->shipments_m->delete($id);
        }
        redirect('admin/store/shipments');
    }
    
}
