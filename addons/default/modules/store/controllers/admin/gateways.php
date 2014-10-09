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
class Gateways extends Admin_Controller {

    protected $section = 'gateways';

    public function __construct() {
        parent::__construct();

        // Load all the required classes
        $this->load->model('gateways_m');
        $this->load->library('form_validation');
        $this->lang->load('store');
    }

    /**
     * List all items
     */
    public function index() {
        // here we use MY_Model's get_all() method to fetch everything
        $data = new stdClass();
        $data->installed = $this->gateways_m->get_all();
        $data->uninstalled = $this->gateways_m->get_uninstalled();

        $this->template->title($this->module_details['name'])
                ->build('admin/gateways/items', $data);
    }

    public function settings($id) {

        $data = new stdClass();
        $data->gateway = $this->gateways_m->load($id);
        $data->options = $data->gateway->options;

        $rules = array(
            array(
                'field' => 'name',
                'label' => 'lang:store:name',
                'rules' => 'trim|max_length[100]|required'
            ),
            array(
                'field' => 'desc',
                'label' => 'lang:store:desc',
                'rules' => 'trim|required|strip_tags'
            ),
            array(
                'field' => 'enabled',
                'label' => 'lang:store:status',
                'rules' => 'trim|intval'
            ),
        );

        $rules = array_merge_recursive($rules, $data->gateway->fields);
        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run()) {

            if ($this->gateways_m->edit($this->input->post())) {

                $this->session->set_flashdata('success', lang('store:success'));
                redirect('admin/store/gateways/');
            } else {
                $this->session->set_flashdata('error', lang('store:error'));
                redirect('admin/store/gateways/settings/' . $id);
            }
        }

        $this->template->title($this->module_details['name'], lang('store:create'))
                ->append_css('module::admin.css')
                ->build('admin/gateways/form', $data);
    }

    public function install($slug) {

        if (!$this->gateways_m->get_by('slug', $slug)) {
            if ($this->gateways_m->install($slug)) {
                $this->session->set_flashdata('success', lang('store:success'));
                redirect('admin/store/gateways/');
            } else {
                $this->session->set_flashdata('error', lang('store:error'));
                redirect('admin/store/gateways/');
            }
        } else {
            $this->session->set_flashdata('error', lang('store:error'));
            redirect('admin/store/gateways/');
        }
    }

    public function uninstall($id = 0) {

        if (is_numeric($id)) {
            $this->gateways_m->delete($id);
        }
        redirect('admin/store/gateways');
    }

    public function enable($id) {
        $gate = $this->gateways_m->load($id);
        if (count($gate->fields) && empty($gate->options)) {
            $this->session->set_flashdata('error', lang('store:error'));
        } else {
            $this->gateways_m->update($id, array('enabled' => 1));
        }
        redirect('admin/store/gateways/');
    }

    public function disable($id) {
        $this->gateways_m->update($id, array('enabled' => 0));
        redirect('admin/store/gateways/');
    }

}
