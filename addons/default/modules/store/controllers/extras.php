<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * This is a store module for PyroCMS
 *
 * @author 		Blazej Adamczyk
 * @website		http://sein.com.pl
 * @package 	PyroStore
 * @subpackage 	Specials Controller
 */
class Extras extends Public_Controller {

    public function __construct() {
        parent::__construct();
        // Load the required classes
        $this->load->model('sales_m');
        $this->load->helper('store');
        class_exists('Basket') OR $this->load->library('store/basket', '', 'cart');
        $this->lang->load('store');
        $this->limit = Settings::get('records_per_page');
    }

    public function index() {
        $this->page();
    }

    /**
     * All items
     */
    private function page($type = NULL) {
        $offset = $this->uri->segment(3);
        
        $this->template->set_breadcrumb(lang('store:store_title'), 'store');
        if ($type) {
            $this->sales_m->where('sale_type', $type);
            $data->title = lang('store:option_'.$type);
            $this->template->set_breadcrumb(lang('store:sales'), 'store/extras');
            $this->template->set_breadcrumb(lang('store:option_'.$type));
        } else {
            $this->template->set_breadcrumb(lang('store:sales'));
        }
        
        
        $data->items = $this->sales_m->limit($this->limit)
                ->offset($offset)
                ->get_all();
        
        $data->items_exist = count($data->items) ? TRUE : FALSE;
        
        // we're using the pagination helper to do the pagination for us. Params are: (module/method, total count, limit, uri segment)
        $data->pagination = create_pagination('store/extras/'.$this->uri->segment(3), $this->sales_m->count_by('sale_type', $type), $this->limit);
        
        $this->template->title($this->module_details['name'])
                ->build('sales', $data);
    }
    public function sales() {
        $this->page('sale');
    }
    public function reduces() {
        $this->page('reduce');
    }
    public function promotions() {
        $this->page('promo');
    }
    public function featured() {
        $this->page('featured');
    }


}