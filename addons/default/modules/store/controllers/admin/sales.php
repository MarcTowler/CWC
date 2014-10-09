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
class Sales extends Admin_Controller {

    protected $section = 'sales';

    public function __construct() {
        parent::__construct();

        // Load all the required classes
        $this->load->model('sales_m');
        $this->load->model('products_m');
        $this->load->model('categories_m');
        $this->load->library('form_validation');
        $this->lang->load('store');

        // We'll set the partials and metadata here since they're used everywhere
        $this->template->append_js('module::admin.js')
                ->append_css('module::admin.css');
        $this->template->append_metadata('<script type="text/javascript">' .
                "\n  var currency = '" . $this->settings->get('currency') . "';" .
                "\n  var tax_rate = " . $this->settings->get('store_tax_value') . ";" .
                "\n</script>");

        $this->data = new stdClass();

        $this->item_validation_rules = array(
            array(
                'field' => 'sale_type',
                'label' => 'lang:store:sale_type',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'category_id',
                'label' => 'lang:store:category',
                'rules' => 'trim|numeric'
            ),
            array(
                'field' => 'product_id',
                'label' => 'lang:store:product',
                'rules' => 'trim|is_natural_no_zero|numeric|required'
            ),
            array(
                'field' => 'new_price',
                'label' => 'lang:store:price',
                'rules' => 'trim|decimal|required'
            ),
            array(
                'field' => 'new_price_tax',
                'label' => 'lang:store:price_tax',
                'rules' => 'trim|decimal|required'
            ),
            array(
                'field' => 'reduction',
                'label' => 'lang:store:reduction',
                'rules' => 'trim'
            ),
        );
    }

    /**
     * List all items
     */
    public function index() {
        // here we use MY_Model's get_all() method to fetch everything
        $items = $this->sales_m->get_all();

        // Build the view with store/views/admin/items.php
        $this->data->products = & $items;
        $this->template->title($this->module_details['name'])
                ->build('admin/sales/items', $this->data);
    }

    public function create() {

        // Check for post data
        $this->form_validation->set_rules($this->item_validation_rules);

        if ($this->form_validation->run()) {
            $input = $this->input->post();
            $this->sales_m->create($input);
            $this->session->set_flashdata('success', lang('store:success'));
            redirect('admin/store/sales');
        } else {

            if ($this->input->post('category_id')) {
                $this->data->products = array_for_select($this->products_m->get_many_by('category_id', $this->input->post('category_id')), 'id', 'name');
            } else {
                $this->data->products = array_for_select($this->products_m->get_all(), 'id', 'name');
            }
            $this->data->categories = $this->categories_m->build_tree_select(array('current_parent' => set_value('category_id', 0)));

            foreach ($this->item_validation_rules as $key => $value) {
                $this->data->{$value['field']} = '';
            }
        }

        // Build page
        $this->template->title($this->module_details['name'])
                ->build('admin/sales/create', $this->data);
    }

    public function group() {

        $rules = array(
            array(
                'field' => 'sale_type',
                'label' => 'lang:store:sale_type',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'category_id',
                'label' => 'lang:store:category',
                'rules' => 'trim|numeric'
            ),
            array(
                'field' => 'reduction',
                'label' => 'lang:store:reduction',
                'rules' => 'trim'
            ),
        );
        // Check for post data
        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run()) {
            $input = $this->input->post();
            if ($this->sales_m->group($input)) {
                $this->pyrocache->delete_all('products_m');
                $this->session->set_flashdata('success', lang('store:success'));
            } else {
                $this->session->set_flashdata('error', lang('store:error'));
            }
            redirect('admin/store/sales');
        } else {
            $this->data->categories = $this->categories_m->build_tree_select(array('current_parent' => set_value('category_id', 0)));

            foreach ($rules as $key => $value) {
                $this->data->{$value['field']} = '';
            }
        }

        // Build page
        $this->template->title($this->module_details['name'])
                ->build('admin/sales/group', $this->data);
    }

    public function edit($id) {

        $row = $this->sales_m->get($id);

        // Get row
        if (!$row) {
            $this->session->set_flashdata('error', lang('store:error'));
            redirect('admin/store/sales');
        }
        $product = $this->products_m->get($row->product_id);
        $this->data = (object) $row;
        $this->data->title = $product->name;
        $this->data->price_tax = $product->price_tax;
        $this->data->price = $product->price;

        $this->form_validation->set_rules($this->item_validation_rules);

        if ($this->form_validation->run()) {
            $input = $this->input->post();
            $this->sales_m->edit($input);
            $this->session->set_flashdata('success', lang('store:success'));
            redirect('admin/store/sales');
        } else {

            if ($this->input->post('category_id')) {
                $this->data->products = array_for_select($this->products_m->get_many_by('category_id', $this->input->post('category_id')), 'id', 'name');
            } else {
                $this->data->products = array_for_select($this->products_m->get_all(), 'id', 'name');
                $this->data->category_id = '';
            }
            $this->data->categories = array_for_select($this->categories_m->get_all(), 'id', 'name');
        }

        // Build page
        $this->template->title($this->module_details['name'])
                ->build('admin/sales/create', $this->data);
    }

    public function delete($id = null) {
        if (is_array($this->input->post('action_to'))) {
            if ($this->sales_m->delete_many($this->input->post('action_to'))) {
                $this->session->set_flashdata('success', lang('firesale:prod_delete_success'));
            } else {
                $this->session->set_flashdata('error', lang('firesale:prod_delete_error'));
            }
        } else if (is_numeric($id)) {

            if (!$this->sales_m->delete($id)) {
                $this->session->set_flashdata('success', lang('firesale:prod_delete_success'));
            } else {
                $this->session->set_flashdata('error', lang('firesale:prod_delete_error'));
            }
        }

        redirect('admin/store/sales');
    }

    public function ajax() {
        if ($this->input->is_ajax_request()) {
            if ($this->input->get('product_id')) {
                $id = $this->input->get('product_id');
                $product = $this->db->select('id, name, price, price_tax')->from('store_products')->where('id', $id)->get()->row();

                echo json_encode($product);
            } else if ($this->input->get('category_id')) {
                $cat_id = $this->input->get('category_id');
                $this->db->where('category_id', $cat_id);
                $products = $this->db->select('id, name')->from('store_products')->get()->result();
                array_unshift($products, array('id' => 0, 'name' => lang('global:select-any')));

                echo json_encode($products);
            } else if ($this->input->get('category_id') == 0) {
                $products = $this->db->select('id, name')->from('store_products')->get()->result();
                array_unshift($products, array('id' => 0, 'name' => lang('global:select-any')));

                echo json_encode($products);
            }
        }
    }

}
