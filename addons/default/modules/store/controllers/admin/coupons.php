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
class Coupons extends Admin_Controller {

    protected $section = 'coupons';

    public function __construct() {
        parent::__construct();

        // Load all the required classes
        $this->load->model('coupons_m');
        $this->load->library('form_validation');
        $this->lang->load('store');

        // We'll set the partials and metadata here since they're used everywhere
        $this->template->append_js('module::admin.js')
                ->append_css('module::admin.css');

        $this->data = new stdClass();
    }

    /**
     * List all items
     */
    public function index() {
        // here we use MY_Model's get_all() method to fetch everything
        $items = $this->coupons_m->get_all();

        $this->template->title($this->module_details['name'])
                ->set('items', $items)
                ->build('admin/coupons/items', $this->data);
    }

    public function create() {

        // Check for post data
        foreach ($this->coupons_m->validate as $item) {
            $this->data->{$item['field']} = '';
        }

        $input = $this->input->post();
        if ($input) {
            if ($this->coupons_m->insert($input)) {
                $this->session->set_flashdata('success', lang('store:success'));
                redirect('admin/store/coupons');
            }
        }

        // Build page
        $this->template->title($this->module_details['name'])
                ->build('admin/coupons/form', $this->data);
    }

    public function edit($id) {

        $row = $this->coupons_m->get($id);

        // Get row
        if (!$row) {
            $this->session->set_flashdata('error', lang('store:error'));
            redirect('admin/store/coupons');
        }
        $this->data = (object) $row;
        $input = $this->input->post();

        if ($input) {
            if ($this->coupons_m->update($id, $input)) {
                $this->session->set_flashdata('success', lang('store:success'));
                redirect('admin/store/coupons');
            }
        }

        // Build page
        $this->template->title($this->module_details['name'])
                ->build('admin/coupons/form', $this->data);
    }

    public function delete($id = null) {

        $delete = true;
        $options = $this->input->post('action_to');

        if ($this->input->post('btnAction') == 'delete') {

            for ($i = 0; $i < count($options); $i++) {

                if (!$this->coupons_m->delete($options[$i])) {
                    $delete = false;
                }
            }
        } else if ($id !== null) {

            if (!$this->coupons_m->delete($id)) {
                $delete = false;
            }
        }

        if ($delete) {
            $this->session->set_flashdata('success', lang('firesale:prod_delete_success'));
        } else {
            $this->session->set_flashdata('error', lang('firesale:prod_delete_error'));
        }
        redirect('admin/store/coupons');
    }

}
