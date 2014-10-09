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
class Categories extends Admin_Controller {
    protected $section = 'categories';
    
    public function __construct() {
        parent::__construct();
        
        // Load all the required classes
        $this->load->model('categories_m');
        $this->load->library('form_validation');
        $this->lang->load('store');
        // Set the validation rules
        $this->item_validation_rules = array(
            array(
                'field' => 'name',
                'label' => 'Name',
                'rules' => 'trim|max_length[100]|required'
            ),
            array(
                'field' => 'slug',
                'label' => 'Slug',
                'rules' => 'trim|max_length[100]|required'
            ),
            array(
                'field' => 'description',
                'label' => 'Description',
                'rules' => 'trim|xss_clean|prep_for_form'
            ),
            array(
                'field' => 'image_id',
                'label' => 'Image',
                'rules' => 'trim|alpha_num'
            ),
            array(
                'field' => 'parent_id',
                'label' => 'Parent',
                'rules' => 'trim|numeric'
            )
        );
        $this->data = (object) array();
        $this->data->parents = array(0 => lang('global:select-none')) + array_for_select($this->categories_m->get_all(), 'id', 'name');
        // We'll set the partials and metadata here since they're used everywhere
        $this->template
                ->append_js('jquery/jquery.ui.nestedSortable.js')
                ->append_metadata($this->load->view('fragments/wysiwyg', $this->data, TRUE))
                ->append_js('module::admin.js')
                ->append_js('module::category.js')
                ->append_css('module::admin.css');
    }
    /**
     * List all items
     */
    public function index() {
        // here we use MY_Model's get_all() method to fetch everything
        $categories = $this->categories_m->get_link_tree();
        // Build the view with store/views/admin/items.php
        $this->data->categories = & $categories;
        $this->template->title($this->module_details['name'])
                ->build('admin/categories/list', $this->data);
    }
    public function create($parent = 0) {
        $this->form_validation->set_rules($this->item_validation_rules);
        if ($this->form_validation->run()) {
            if ($id = $this->categories_m->create($this->input->post())) {
                $this->pyrocache->delete_all('categories_m');
                $this->session->set_flashdata('success', lang('store:success'));
                Events::trigger('store_category_created', $id);
                echo 'success';
                return;
            } else {
                $this->session->set_flashdata('error', lang('store:error'));
                $this->template->messages['error'] = lang('store:error');
                echo $this->load->view('admin/partials/notices', $this->template);
                return;
                //redirect('admin/store/categories/create');
            }
        }
        if (validation_errors()) {
            echo $this->load->view('admin/partials/notices');
            return;
        }
        $category = (object) array();
        foreach ($this->item_validation_rules as $rule) {
            $category->{$rule['field']} = set_value($rule['field']);
        }
        $category->parent_id = $parent;
        $this->template
                ->set('category', $category);
        $this->template
                ->set('parents', $this->data->parents);
        
        $this->template
                ->set_layout(false)
                ->build('admin/categories/form');
    }
    public function edit($id = 0) {
        // Set the validation rules from the array above
        $this->form_validation->set_rules($this->item_validation_rules);
        $category = $this->categories_m->get($id);
        // check if the form validation passed
        if ($this->form_validation->run()) {
            
            $this->pyrocache->delete_all('categories_m');
            
            $update = $this->input->post();
            $id = $update['category_id'];
            unset($update['category_id']);
            $result = $this->categories_m->edit($id, $update);
            
            if ($result) {
                $this->pyrocache->delete_all('categories_m');
                $this->session->set_flashdata('success', lang('store:success'));
                Events::trigger('store_category_updated', $id);
                echo 'success';
                return;
            } else {
                $this->session->set_flashdata('error', lang('store:error'));
                $this->template->messages['error'] = lang('store:error');
                echo $this->load->view('admin/partials/notices', $this->template);
                return;
            }
        } else if ($_POST){
            $category = (object) array();
            foreach ($this->item_validation_rules as $rule) {
                $category->{$rule['field']} = set_value($rule['field']);
            }
        }
        if (validation_errors()) {
            echo $this->load->view('admin/partials/notices');
            return;
        }
        $this->template
                ->set('category', $category);
        $this->template
                ->set('parents', $this->data->parents);
        // Get Pages and create pages tree
        //$this->template->tree_select = $this->_build_tree_select(array('current_parent' => 0));
        $this->template
                ->set_layout(false)
                ->build('admin/categories/form');
    }
    public function delete($id = 0) {
        // make sure the button was clicked and that there is an array of ids
        if (isset($_POST['btnAction']) AND is_array($_POST['action_to'])) {
            // pass the ids and let MY_Model delete the items
            Events::trigger('store_category_deleted', $this->input->post('action_to'));
            $this->categories_m->delete_many($this->input->post('action_to'));
        } elseif (is_numeric($id)) {
            // they just clicked the link so we'll delete that one
            Events::trigger('store_category_deleted', $id);
            $this->categories_m->delete($id);
        }
        $this->pyrocache->delete_all('categories_m');
        redirect('admin/store/categories');
    }
    public function order() {
        $order = $this->input->post('order');
        //$data = $this->input->post('data');
        if (is_array($order)) {
            //reset all parent > child relations
            $this->db->trans_start();
            $this->categories_m->update_order(array('parent_id' => 0));
            foreach ($order as $i => $link) {
                //set the order of the root links
                $this->categories_m->update_by('id', str_replace('link_', '', $link['id']), array('order' => $i));
                //iterate through children and set their order and parent
                $this->categories_m->_set_children($link);
            }
            $this->db->trans_complete();
            $this->pyrocache->delete_all('categories_m');
        }
    }
    public function ajax_link_details($link_id) {
        $link = $this->categories_m->get_category($link_id);
        $this->load->view('admin/categories/link_details', array('link' => $link));
    }
}