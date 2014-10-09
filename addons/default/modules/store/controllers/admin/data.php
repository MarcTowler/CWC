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
class Data extends Admin_Controller {

    protected $section = 'data';

    public function __construct() {
        parent::__construct();

        // Load all the required classes
        $this->lang->load('store');
        $this->load->library('format');
        $this->load->helper('download');

        // Set the validation rules
        $this->item_validation_rules = array(
            array(
                'field' => 'name',
                'label' => 'lang:store:name',
                'rules' => 'trim|max_length[100]|required'
            ),
            array(
                'field' => 'slug',
                'label' => 'lang:store:slug',
                'rules' => 'trim|max_length[100]|required'
            ),
            array(
                'field' => 'sku',
                'label' => 'lang:store:sku',
                'rules' => 'trim|max_length[100]|required'
            ),
        );
    }

    /**
     * List all items
     */
    public function index() {
        // here we use MY_Model's get_all() method to fetch everything

        $this->template->title($this->module_details['name'])
                ->build('admin/data');
    }

    public function import() {
        $this->load->model('import_m');
        $content = '';

        if ($this->input->post()) {
            //$content = file_get_contents($_FILES['userfile']['tmp_name']);
            $delimiter = $this->input->post('delimiter', ',');
            $enclosure = $this->input->post('enclosure', '"');
            $escape = $this->input->post('escape', '\\');
            $headers = $this->input->post('no_headers', 0) ? FALSE : TRUE;
            $this->session->set_userdata('options', $this->input->post('options'));
            print_r($this->input->post('options'));

            $content = $this->import_m->csv_to_array($_FILES['userfile']['tmp_name'], $delimiter, $enclosure, $escape, $headers);

            $this->pyrocache->write($content, 'parsed_csv');
            
            $heading = array_keys($content[0]);
            $select = $this->import_m->get_fields();
            
            $this->load->library('table');
            //$this->table->set_heading($heading);
            $heading = array_fill(0, count($heading), $select);
            array_unshift($heading, 'Ignore');
            $this->table->set_heading($heading);
            
            $content = $this->import_m->array_for_table($content);
            $table = $this->table->generate($content);
            
            $this->template->set('table', $table);
            $this->template->title($this->module_details['name'])
                ->set('content', $content)
                ->build('admin/assignment');
            return;
        }
        
        $folders = $this->import_m->get_folders();

        $this->template->title($this->module_details['name'])
                ->set('folders', $folders)
                ->set('content', $content)
                ->build('admin/import');
    }
    
    public function assign() {
        $this->load->model('import_m');
        $data = $this->pyrocache->get('parsed_csv');
        $options = $this->session->userdata('options');
        
         if ($this->input->post() && $data) {
             $ignored = $this->input->post('ignore');             
             $fields = $this->input->post('fields');             
             
             if (is_array($ignored)) {
                 foreach ($ignored as $k) {
                     unset($data[$k]);
                 }
             }
             
             $result = $this->import_m->update_values($fields, $data, $options);
             if ($result) {
                 $this->session->set_flashdata('success', lang('store:success'));
                 $this->session->unset_userdata('options');
             } else {
                 $this->session->set_flashdata('error', lang('store:error'));
             }
             redirect('admin/store/data');
         }
    }

    public function export($source, $type) {
        in_array($source, array('products', 'orders', 'categories')) or redirect('admin/store/data');

        $model = strtolower($source . '_m');
        $this->load->model($model);

        $data_array = $this->{$model}->get_all();
        $data_array = $this->format->to_array($data_array);
        force_download($source . '_' . date('Y_m_d') . '.' . $type, $this->format->factory($data_array)
                        ->{'to_' . $type}());
    }

    

}
