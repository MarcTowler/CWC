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
class Affiliate extends Admin_Controller {

    protected $section = 'affiliate';

    public function __construct() {
        parent::__construct();

        // Load all the required classes
        $this->load->model('affiliate_groups_m');
        $this->load->model('affiliate_users_m');

        $this->load->library('form_validation');
        $this->load->library('users/ion_auth');
        $this->load->model('groups/group_m');
        $this->lang->load('store');

        // We'll set the partials and metadata here since they're used everywhere
        $this->template->append_js('module::admin.js')
                ->append_css('module::admin.css');

        $this->data = new stdClass();

        $this->item_validation_rules = array(
            array(
                'field' => 'user_id',
                'label' => 'lang:store:sale_type',
                'rules' => 'trim'
            ),
            array(
                'field' => 'group_id',
                'label' => 'lang:store:sale_type',
                'rules' => 'trim'
            ),
            array(
                'field' => 'discount',
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
        $this->data->users = $this->affiliate_users_m->get_all();
        $this->data->groups = $this->affiliate_groups_m->get_all();

        $this->template->title($this->module_details['name'])
                ->build('admin/affiliate/items', $this->data);
    }

    public function create($type = 'user') {

        // Check for post data
        $this->form_validation->set_rules($this->item_validation_rules);
        $this->data->type = $type;
        if ($type == 'user') {
            $this->data->items = array_for_select($this->ion_auth->get_users(),'id', 'display_name');
        } else {
            $this->data->items = array_for_select($this->group_m->get_all(),'id', 'description');
        }
        
        foreach ($this->item_validation_rules as $key => $value) {
            $this->data->{$value['field']} = '';
        }
        
        if ($this->form_validation->run()) {
            $input = $this->input->post();
            unset($input['btnAction']);
            $this->{'affiliate_' . $type . 's_m'}->insert($input);

            $this->session->set_flashdata('success', lang('store:success'));
            redirect('admin/store/affiliate');
        }

        // Build page
        $this->template->title($this->module_details['name'])
                ->build('admin/affiliate/form', $this->data);
    }

    public function edit($type = 'user',$id) {

        $row = $this->{'affiliate_' . $type . 's_m'}->get_by($type.'_id', $id);

        // Get row
        if (!$row) {
            $this->session->set_flashdata('error', lang('store:error'));
            redirect('admin/store/affiliate');
        }
        $this->data = (object) $row;

        $this->data->type = $type;
        if ($type == 'user') {
            $this->data->items = array_for_select($this->ion_auth->get_users(),'id', 'display_name');
        } else {
            $this->data->items = array_for_select($this->group_m->get_all(),'id', 'description');
        }
        
        $this->form_validation->set_rules($this->item_validation_rules);

        if ($this->form_validation->run()) {
            $input = $this->input->post();
            $this->{'affiliate_' . $type . 's_m'}->update($id, $input);
            
            $this->session->set_flashdata('success', lang('store:success'));
            redirect('admin/store/affiliate');
        }

        // Build page
        $this->template->title($this->module_details['name'])
                ->set('type',$type)
                ->build('admin/affiliate/form', $this->data);
    }

    public function delete($type, $id = null) {

        $delete = true;
        $options = $this->input->post('action_to');

        if ($this->input->post('btnAction') == 'delete') {

            for ($i = 0; $i < count($options); $i++) {

                if (!$this->{'affiliate_' . $type . 's_m'}->delete_by($type.'_id',$options[$i])) {
                    $delete = false;
                }
            }
        } else if ($id !== null) {

            if (!$this->{'affiliate_' . $type . 's_m'}->delete_by($type.'_id',$id)) {
                $delete = false;
            }
        }

        if ($delete) {
            $this->session->set_flashdata('success', lang('firesale:prod_delete_success'));
        } else {
            $this->session->set_flashdata('error', lang('firesale:prod_delete_error'));
        }
        redirect('admin/store/affiliate');
    }


}
