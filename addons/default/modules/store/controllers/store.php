<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * This is a store module for PyroCMS
 *
 * @author 		Blazej Adamczyk
 * @website		http://sein.com.pl
 * @package 	PyroStore
 * @subpackage 	Store Controller
 */
class Store extends Public_Controller {

    public function __construct() {
        parent::__construct();
        // Load the required classes
        $this->load->model('products_m');
        $this->load->model('categories_m');
        $this->load->model('options_m');
        $this->load->helper('store');
        
        class_exists('Basket') OR $this->load->library('store/basket', '', 'cart');
        
        $this->lang->load('store');
        $this->limit = $this->settings->store_products_limit > 0 ? $this->settings->store_products_limit : $this->settings->records_per_page;
        $this->sorting_options = array(
            'created|asc' => lang('store:date_created').' '.lang('store:ascending'),
            'created|desc' => lang('store:date_created').' '.lang('store:descending'),
            'name|asc' => lang('store:name').' '.lang('store:ascending'),
            'name|desc' => lang('store:name').' '.lang('store:descending'),
            'price|asc' => lang('store:price').' '.lang('store:ascending'),
            'price|desc' => lang('store:price').' '.lang('store:descending'),
            );
        
        $this->sort_by = $this->input->cookie(SITE_REF.'_store_sort') ? $this->input->cookie(SITE_REF.'_store_sort') : 'created|asc';
        
        if ($this->input->get('sort') && array_key_exists($this->input->get('sort'), $this->sorting_options)) {
            $domain = $this->config->item('store_sort');
        
            $cookie = array(
                'name' => 'store_sort',
                'value' => $this->input->get('sort'),
                'domain' => $domain,
                'expire' => '86400',
                'prefix' => '',
                'path' => BASE_URI,
            );

            $this->input->set_cookie($cookie);
            $this->sort_by = $this->input->get('sort');
        }
        $this->template->set('sorting_options', $this->sorting_options)->set('sort', $this->sort_by);
    }

    /**
     * All items
     */
    public function index($offset = 0) {
        $data = new stdClass();
        
        //$data->items = $this->pyrocache->model('products_m', 'get_all_within', array($data->subcategories + array($category), $this->limit, $offset));
        $sort = explode('|', $this->sort_by);
        $data->items = $this->products_m->limit($this->limit)
                ->order_by($sort[0], $sort[1])
                ->offset($offset)
                ->get_all();
        $data->subcategories = $this->categories_m->get_many_by('parent_id', 0);
                
        if (count($data->items)) {
            $data->items_exist = TRUE;
        } else {
            $data->items_exist = FALSE;
        }
        
        $data->pagination = create_pagination('store', $this->products_m->count_all(), $this->limit, 2);
        $this->template->set_breadcrumb(lang('store:store_title'));
        $this->template->title($this->module_details['name'])
                ->build('home', $data);
    }

    public function category() {
        $total_segments = $this->uri->total_segments();
        $slug = is_numeric($this->uri->segment($total_segments)) ? $this->uri->segment($total_segments - 1) : $this->uri->segment($total_segments);
        $offset = is_numeric($this->uri->segment($total_segments)) ? $this->uri->segment($total_segments) : 0;
        $category = $this->pyrocache->model('categories_m', 'get_by', array('slug', $slug));
        if (!$category) {
            Modules::run('pages/_remap', '404');
            exit();
        }
        $sort = explode('|', $this->sort_by);
        $data = new stdClass();
        $data->category = $category;
        $data->subcategories = $this->pyrocache->model('categories_m', 'get_many_by', array('parent_id', $category->id));
        $node = $this->pyrocache->model('categories_m', 'get_category_node', array($category->id));
        $data->nodes = $node;
        $data->title = $category->name;
        $data->items = $this->pyrocache->model('products_m', 'get_all_within', array($data->subcategories + array($category), $this->limit, $offset, $sort));

        
        if (count($data->items)) {
            $data->items_exist = TRUE;
        } else {
            $data->items_exist = FALSE;
        }
        
        
        $pagination_segment = $offset ? $total_segments : $total_segments + 1;
        $data->pagination = create_pagination('store/category/' . $category->uri, $this->products_m->count_all_within($data->subcategories + array($category)), $this->limit, $pagination_segment);
        $this->template->set_breadcrumb(lang('store:store_title'), 'store');
        
        foreach ($node as $cat) {
            $this->template->set_breadcrumb($cat['name'], $slug == $cat['slug'] ? '' : 'store/category/' . $cat['uri']);
        }
        
        
        $this->template->title($this->module_details['name'], $category->name)
                ->set_metadata('description', strip_tags($category->description))
                ->build('category', $data);
    }

    public function product($slug = NULL) {
        $slug OR show_404();
        
        $this->lang->load('comments/comments');

        $data = new stdClass();

        if (is_numeric($slug)) {
            
            $data->product = $this->pyrocache->model('products_m', 'get', array($slug), 3600);
        } else {
            $data->product = $this->pyrocache->model('products_m', 'get_by_slug', array($slug), 3600);
            //$data->product = $this->products_m->get_by_slug($slug);
        }
        
        if (!$data->product) {
            Modules::run('pages/_remap', '404');
            exit();
        }
        if (Settings::get('enable_comments'))
        {
                // Load Comments so we can work out what to do with them
                $this->load->library('comments/comments', array(
                        'entry_id' => $data->product->id,
                        'entry_title' => $data->product->name,
                        'module' => 'store',
                        'singular' => 'comments:counter_singular_label',
                        'plural' => 'comments:counter_plural_label',
                ));

        }
        
        $this->load->library('watch_history');
        $this->watch_history->add($data->product->id);
        
        $data->node = $this->categories_m->get_category_node($data->product->category_id);
        $data->images = $this->products_m->get_images($data->product->id);
        //$data->options = $this->pyrocache->model('options_m', 'get_by_product', array($data->product->id), 3600);
        $data->options = $this->options_m->parse_options($data->product->id);

        $meta_keywords = Keywords::get_string($data->product->keywords);
        $data->product->keywords = Keywords::get($data->product->keywords, 'store/tagged');
        $data->items = $this->products_m->get_related($data->product->id);
        
        
        if (count($data->options) > 0) {
            $data->has_options = true;
        } else {
            $data->has_options = false;
        }
        
        $this->template->set_breadcrumb(lang('store:store_title'), 'store');
        foreach ($data->node as $cat) {
            $this->template->set_breadcrumb($cat['name'], 'store/category/' . $cat['slug']);
        }
        
        $this->template->set_breadcrumb($data->product->name);
        $this->template->title($this->module_details['name'], $data->product->name)
                ->set_metadata('keywords', $meta_keywords)
                ->set_metadata('description', strip_tags($data->product->short))
                ->build('product', $data);
    }

    public function search() {
        $this->db->like('store_products.name', $this->input->post('phrase'));
        $this->db->or_like('store_products.description', $this->input->post('phrase'));

        $data = new stdClass();
        $data->items = $this->products_m->get_all();
        
        if (count($data->items)) {
            $data->items_exist = TRUE;
        } else {
            $data->items_exist = FALSE;
        }
        
        $this->template->set_breadcrumb(lang('store:store_title'), 'store')
                ->set_breadcrumb(lang('store:search_title'), 'store/search')
                ->set_breadcrumb(humanize($this->input->post('phrase')));
        
        $this->template->title($this->module_details['name'], lang('store:search_title'))
                ->build('search', $data);
    }

    public function tagged($tag = '') {
        
        $tag = rawurldecode($tag) OR redirect('store');
        $data = new stdClass();
        $data->subcategories = array();

        
        $pagination = create_pagination('store/tagged/' . $tag, $this->products_m->count_tagged_by($tag), NULL, 4);

        
        $data->items = $this->products_m
                ->limit($pagination['limit'])
                ->get_tagged_by($tag);

        foreach ($data->items AS &$product) {
            $product->keywords = Keywords::get($product->keywords, 'store/tagged');
        }
        $data->items_exist = count($data->items) ? TRUE : FALSE;

        $name = str_replace('-', ' ', $tag);
        $data->title = lang('global:keywords') . ': ' . $name;

        // Build the page
        $this->template
                ->title($this->module_details['name'], humanize($name))
                ->set_metadata('keywords', $tag)
                ->set_breadcrumb(lang('store:store_title'), 'store')
                ->set_breadcrumb(lang('global:keywords'), 'store/tagged')
                ->set_breadcrumb($name)
                ->set('tag', $tag)
                ->set('pagination', $pagination)
                ->build('category', $data);
    }

}