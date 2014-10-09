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
class Stats extends Admin_Controller {

    protected $section = 'stats';

    public function __construct() {
        parent::__construct();

        // Load all the required classes
        $this->load->model('orders_m');
        $this->load->model('stats_m');
        $this->load->library('form_validation');
        $this->load->helper('store');
        $this->lang->load('store');
        $this->data = new stdClass();

        // We'll set the partials and metadata here since they're used everywhere
        $this->template->append_js('module::admin.js')                
                ->append_css('module::admin.css');
    }

    /**
     * List all items
     */
    public function index($offset = 0) {
        
    }
    
    public function ajax($days = 7, $limit = 'all') {
        if ($this->input->is_ajax_request()) {
            $data = $this->stats_m->get_period($days, $limit);

            echo json_encode($data);
        } else {
            redirect('admin/store');
        }
    }
    
}
