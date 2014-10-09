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
class Products extends Admin_Controller {

    protected $section = 'products';

    public function __construct() {
        parent::__construct();

        // Load all the required classes
        $this->load->model('products_m');
        $this->load->model('categories_m');
        $this->load->library('form_validation');
        $this->load->library('keywords/keywords');
        $this->lang->load('store');

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
            array(
                'field' => 'category_id',
                'label' => 'lang:store:category',
                'rules' => 'trim|numeric|is_natural_no_zero|required'
            ),
            array(
                'field' => 'cover_id',
                'label' => 'lang:store:cover',
                'rules' => 'trim|alpha_num|required'
            ),
            array(
                'field' => 'short',
                'label' => 'lang:store:short',
                'rules' => 'trim'
            ),
            array(
                'field' => 'description',
                'label' => 'lang:store:desc',
                'rules' => 'trim'
            ),
            array(
                'field' => 'status',
                'label' => 'lang:store:status',
                'rules' => 'trim'
            ),
            array(
                'field' => 'related[]',
                'label' => 'lang:store:related',
                'rules' => ''
            ),
            array(
                'field' => 'price',
                'label' => 'lang:store:price_notax',
                'rules' => 'trim|decimal|required'
            ),
            array(
                'field' => 'price_tax',
                'label' => 'lang:store:price_tax',
                'rules' => 'trim|decimal|required'
            ),
        );

        $this->load->library('files/files');
        $this->load->model('files/file_folders_m');
        $this->load->model('options_m');

        $folders = array(0 => lang('global:select-pick'));
        $tree = $this->file_folders_m->order_by('parent_id', 'ASC')->order_by('id', 'ASC')->get_all();
        foreach ($tree as $folder) {
            $id = $folder->id;
            if ($folder->parent_id != 0) {
                $folders[$id] = $folders[$folder->parent_id] . ' &raquo; ' . $folder->name;
            } else {
                $folders[$id] = $folder->name;
            }
        }

        $products_rel = $this->db->select('name, id')->get('store_products')->result();
        $options = $this->options_m->get_all();


        $this->data = new stdClass();
        $this->template->set('folders', $folders);
        $this->template->set('options_list', array_for_select($options, 'id', 'title'));
        $this->template->set('related_list', array_for_select($products_rel, 'id', 'name'));
        $this->template->append_js('module::admin.js');
        $this->template->append_css('module::admin.css');

        $this->load->driver('Streams');

        $this->template->append_metadata('<script type="text/javascript">' .
                "\n  var currency = '" . $this->settings->get('currency') . "';" .
                "\n  var tax_rate = " . $this->settings->get('store_tax_value') . ";" .
                "\n</script>");
    }

    /**
     * List all items
     */
    public function index($offset = 0) {
        // here we use MY_Model's get_all() method to fetch everything
//        
        $this->data->categories = $this->categories_m->build_tree_array(array('current_parent' => 0));

        $this->data->pagination = create_pagination('admin/store/products/index', $this->products_m->count_all(), Settings::get('records_per_page'), 5);

        $this->data->products = $this->products_m
                        ->limit(Settings::get('records_per_page'))
                        ->offset($this->data->pagination['offset'])
                        ->order_by('category_name', 'ASC')
                        ->order_by('name', 'ASC')
                        ->get_all();

        // Build the view with store/views/admin/items.php
        $this->template->title($this->module_details['name'])
                ->append_js('admin/filter.js');

        if ($this->input->is_ajax_request()) {
            $this->template->set_layout(FALSE);
            $this->template->build('admin/products/table', $this->data);
        } else {
            $this->template->build('admin/products/list', $this->data);
        }
    }

    public function create() {

        $stream = $this->streams->streams->get_stream('store_products', 'store');
        $stream_fields = $this->streams_m->get_stream_fields($stream->id, $stream->stream_namespace);

        $stream_validation = $this->streams->streams->validation_array($stream->stream_slug, $stream->stream_namespace, 'new');

        $rules = array_merge($this->item_validation_rules, $stream_validation);
        $rules[] = array('field' => 'slug', 'label' => 'lang:store:slug', 'rules' => 'trim|max_length[100]|required|is_unique[store_products.slug]');
        $rules[] = array('field' => 'sku', 'label' => 'lang:store:sku', 'rules' => 'trim|max_length[100]|required|is_unique[store_products.sku]');

        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run()) {
            $input = $this->input->post();
            $input['keywords_hash'] = Keywords::process($input['keywords']);
            if ($product_id = $this->products_m->create($input)) {
                Events::trigger('store_product_created', $product_id);
                $this->pyrocache->delete_all('products_m');
                $this->session->set_flashdata('success', lang('store:success'));
                redirect('admin/store/products/');
            } else {
                $this->session->set_flashdata('error', lang('store:error'));
                redirect('admin/store/products/create');
            }
        }

        $this->template->tree_select = $this->categories_m->build_tree_select(array('current_parent' => set_value('category_id', 0)));

        foreach ($rules AS $rule) {
            $this->data->{$rule['field']} = $this->input->post($rule['field']);
        }
        $values = $this->fields->set_values($stream_fields, null, 'new');

        $this->fields->run_field_events($stream_fields, array(), $values);

        $this->template->title($this->module_details['name'], lang('store:create'))
                ->append_metadata($this->load->view('fragments/wysiwyg', $this->data, TRUE))
                ->append_js('jquery/jquery.tagsinput.js')
                ->append_js('module::dropzone.min.js')
                ->append_js('module::admin_product_form.js')
                ->append_css('module::dropzone.css')
                ->append_css('jquery/jquery.tagsinput.css')
                ->set('stream_fields', $this->streams->fields->get_stream_fields($stream->stream_slug, $stream->stream_namespace, $values))
                ->build('admin/products/form', $this->data);
    }

    public function duplicate($id = 0)
    {
        $id OR redirect('admin/store/products');
        $product = (array) $this->products_m->get($id);
        $product OR redirect('admin/store/products');

        $product['keywords_hash'] = $product['keywords'];
        $product['slug'] = $product['slug'].'-'.random_string();

        if ($product_id = $this->products_m->create($product)) {
            Events::trigger('store_product_created', $product_id);
            $this->pyrocache->delete_all('products_m');
            $this->session->set_flashdata('success', lang('store:success'));
            redirect('admin/store/products/');
        } else {
            $this->session->set_flashdata('error', lang('store:error'));
            redirect('admin/store/products/');
        }

    }

    public function edit($id = 0) {
        $this->data = $this->products_m->get($id);
        $this->data->images = $this->products_m->get_images($id);
        $old_keywords_hash = (trim($this->data->keywords) != '') ?  $this->data->keywords : null;

        $this->data->keywords = Keywords::get_string($this->data->keywords);
        $this->data->related = array_for_select($this->data->related, 'id');
        $this->data->options = $this->options_m->get_by_product($id);

        $stream = $this->streams->streams->get_stream('store_products', 'store');
        $stream_fields = $this->streams_m->get_stream_fields($stream->id, $stream->stream_namespace);

        $stream_validation = $this->streams->streams->validation_array($stream->stream_slug, $stream->stream_namespace, 'edit');

        $rules = array_merge($this->item_validation_rules, $stream_validation);

        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run()) {

            $input = $this->input->post();
            unset($input['btnAction']);

            $input['keywords_hash'] = Keywords::process($input['keywords'], $old_keywords_hash);

            if ($this->products_m->edit($id, $input)) {
                Events::trigger('store_product_updated', $id);
                $this->pyrocache->delete_all('products_m');
                $this->session->set_flashdata('success', lang('store:success'));
                redirect('admin/store/products');
            } else {
                $this->session->set_flashdata('error', lang('store:error'));
                redirect('admin/store/products/edit/' . $id);
            }
        }

        $this->template->tree_select = $this->categories_m->build_tree_select(array('current_parent' => $this->data->category_id));


        $values = $this->fields->set_values($stream_fields,  $this->data, 'edit');

        // Run stream field events
        $this->fields->run_field_events($stream_fields, array(), $values);

        $this->template->title($this->module_details['name'], lang('store:edit'))
                ->append_metadata($this->load->view('fragments/wysiwyg', $this->data, TRUE))
                ->append_js('jquery/jquery.tagsinput.js')
                ->append_js('module::dropzone.min.js')
                ->append_js('module::admin_product_form.js')
                ->append_css('module::dropzone.css')
                ->set('stream_fields', $this->streams->fields->get_stream_fields($stream->stream_slug, $stream->stream_namespace, $values, $id))
                ->append_css('jquery/jquery.tagsinput.css')
                ->build('admin/products/form', $this->data);
    }

    public function delete($id = 0) {

        if (isset($_POST['btnAction']) AND is_array($_POST['action_to'])) {
            Events::trigger('store_product_deleted', $this->input->post('action_to'));
            $this->products_m->delete_many($this->input->post('action_to'));
        } elseif (is_numeric($id)) {
            Events::trigger('store_product_deleted', $id);
            $this->products_m->delete($id);
        }
        $this->pyrocache->delete_all('products_m');

        redirect('admin/store/products');
    }

    public function status($id = NULL, $status = NULL) {
        if ($this->input->is_ajax_request()) {
            $input = $this->input->post();
            if ($this->products_m->update($input['product_id'], array('status' => $input['status']))) {
                echo $this->load->view('admin/partials/notices', array('messages' => array('success' => lang('store:success'))));
            } else {
                echo $this->load->view('admin/partials/notices', array('messages' => array('error' => lang('store:error'))));
            }
        } else if($id && $status) {
            if ($this->products_m->update($id, array('status' => $status))) {
                $this->session->set_flashdata('success', lang('store:success'));
            } else {
                $this->session->set_flashdata('error', lang('store:error'));
            }
            redirect('admin/store/products');
        }
    }

    public function ajax_filter() {
        $category = $this->input->post('f_category');
        //$stock = $this->input->post('f_stock');
        $order = $this->input->post('f_order');
        $keywords = $this->input->post('f_keywords');

        $post_data = array();

        if ($category != 0) {
            $post_data['category_id'] = $category;
        }

        if ($keywords) {
            $post_data['keywords'] = $keywords;
        }

        $this->products_m->filter($post_data);

        $pagination = create_pagination('admin/store/products/ajax_filter', $this->products_m->count_all_results('store_products'), NULL, 5);

        $this->products_m->limit(Settings::get('records_per_page'));
                // ->order_by('category_name', 'ASC')

        if ($order) {
            $this->products_m->order_by($order, 'ASC');
        }

        $this->products_m->order_by('name', 'ASC')
            ->offset($pagination['offset']);

        $this->products_m->filter($post_data);
        //$prods = $this->products_m->get_all();
        $results = $this->products_m->get_all();

        //set the layout to false and load the view
        $this->template
                ->set_layout(FALSE)
                ->set('products', $results)
                ->set('pagination', $pagination)
                ->build('admin/products/table');
    }

}
