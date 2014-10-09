<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * This is a store module for PyroCMS
 *
 * @author 		Jerel Unruh - PyroCMS Dev Team
 * @website		http://unruhdesigns.com
 * @package 	PyroCMS
 * @subpackage 	store Module
 */
class Options extends Admin_Controller {

    protected $section = 'options';

    public function __construct() {
        parent::__construct();

        // Load all the required classes
        $this->load->helper('store');
        $this->load->model('products_m');
        $this->load->model('options_m');
        $this->load->library('form_validation');
        $this->lang->load('store');
        $this->data = new stdClass();

        // Set the validation rules
        $this->item_validation_rules = array(
            array(
                'field' => 'title',
                'label' => 'Tytuł',
                'rules' => 'trim|max_length[100]|required'
            ),
            array(
                'field' => 'slug',
                'label' => 'Slug',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'field_type',
                'label' => 'Rodzaj pola',
                'rules' => 'trim|required'
            ),
        );
        $this->template->append_css('module::admin.css');
        $this->template->append_js('module::options.js');
    }

    /**
     * List all items
     */
    public function index() {
        $all = $this->options_m->get_all();

        $this->data->items = & $all;
        $this->template->title($this->module_details['name'])
                ->build('admin/options/items', $this->data);
    }

    public function product($product_id) {
        $product_id OR redirect('admin/store/products');
        $current = $this->options_m->get_by_product($product_id);
        $options = $this->options_m->get_all();

        $this->data->items = & $current;
        $this->data->options = & $options;
        $this->data->product_id = & $product_id;
        $this->template->title($this->module_details['name'])
                ->build('admin/options/product', $this->data);
    }

    public function append($product_id = 0, $option_id = 0) {
        $product_id OR redirect('admin/store/options');
        $option_id OR redirect('admin/store/options');
        if ($this->options_m->hook($product_id, $option_id)) {
            $this->pyrocache->delete_all('options_m');
            $this->session->set_flashdata('success', lang('store:success'));
        } else {
            $this->session->set_flashdata('error', lang('store:error'));
        }
        redirect('admin/store/options/product/' . $product_id);
    }

    public function create() {

        $this->form_validation->set_rules($this->item_validation_rules);

        if ($this->form_validation->run()) {

            if ($this->options_m->create($this->input->post())) {
                $this->session->set_flashdata('success', lang('store:success'));
                $this->pyrocache->delete_all('options_m');
                redirect('admin/store/options/');
            } else {
                $this->session->set_flashdata('error', lang('store:error'));
                redirect('admin/store/options/create/');
            }
        }

        foreach ($this->item_validation_rules AS $rule) {
            $this->data->{$rule['field']} = $this->input->post($rule['field']);
        }

        $this->template->title($this->module_details['name'], lang('store:create'))
                ->build('admin/options/form', $this->data);
    }

    public function edit($id = 0) {
        $id OR redirect('admin/store/products');
        $this->data = $this->options_m->get($id);

        $this->form_validation->set_rules($this->item_validation_rules);

        if ($this->form_validation->run()) {
            $update = $this->input->post();

            if ($this->options_m->update($id, $update)) {
                $this->pyrocache->delete_all('options_m');
                $this->session->set_flashdata('success', lang('store:success'));
                redirect('admin/store/options/');
            } else {
                $this->session->set_flashdata('error', lang('store:error'));
                redirect('admin/store/options/edit/' . $id);
            }
        }

        $this->template->title($this->module_details['name'], lang('store:edit'))
                ->build('admin/options/form', $this->data);
    }

    public function delete($id = 0) {

        if (isset($_POST['btnAction']) AND is_array($_POST['action_to'])) {
            $this->pyrocache->delete_all('options_m');
            $this->options_m->delete_many($this->input->post('action_to'));
        } elseif (is_numeric($id)) {
            $this->pyrocache->delete_all('options_m');
            $this->options_m->delete($id);
        }
        redirect('admin/store/options');
    }

    public function remove($product_id = 0, $option_id = 0) {
        $product_id OR redirect('admin/store/options');
         if (isset($_POST['btnAction']) AND is_array($_POST['action_to'])) {
             foreach ($_POST['action_to'] as $value) {
                 $this->options_m->unhook($product_id, $value);
             }
         } elseif ($option_id && $this->options_m->unhook($product_id, $option_id)) {
            $this->pyrocache->delete_all('options_m');
            $this->session->set_flashdata('success', lang('store:success'));
        } else {
            $this->pyrocache->delete_all('options_m');
            $this->session->set_flashdata('error', lang('store:error'));
        }
        redirect('admin/store/options/product/' . $product_id);
    }

    public function ajax()
    {
        if($this->input->is_ajax_request()) {
            $id = $this->input->post('id');
            $option = $this->options_m->get($id);
            echo json_encode($option);
        } else {
            redirect('admin/store/options');
        }
    }

    public function sort() {
        if ($this->input->is_ajax_request()) {
            $this->pyrocache->delete_all('options_m');
            if ($this->options_m->sort($this->input->post())) {
                echo 1;
            } else {
                echo 0;
            }
        } else {
            redirect('admin/store/options');
        }
    }

}
