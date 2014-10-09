<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * This is a editor module for PyroCMS
 *
 * @author 		Jerel Unruh - PyroCMS Dev Team
 * @website		http://unruhdesigns.com
 * @package 	PyroCMS
 * @subpackage 	editor Module
 */
class Custom_Fields extends Admin_Controller {

    protected $section = 'fields';

    public function __construct() {
        parent::__construct();

        // Load all the required classes
        $this->load->driver('streams');
        $this->lang->load('store');
    }

    /**
     * List all items
     */
    public function index() {

        $stream_uri = 'admin/store/products/fields/';
        
        $buttons = array(
            array(
                'url' => $stream_uri . 'edit/-assign_id-',
                'label' => $this->lang->line('global:edit')
            ),
            array(
                'url' => $stream_uri . 'delete/-assign_id-',
                'label' => $this->lang->line('global:delete'),
                'confirm' => true
            )
        );

        $this->template->title(lang('global:custom_fields'));

        $this->streams->cp->assignments_table('store_products', 'store', Settings::get('records_per_page'), $stream_uri . 'index', true, array('buttons' => $buttons));
    }

    public function create() {
        // Set the validation rules from the array above
        $stream_uri = 'admin/store/products/fields/';

        $extra['title'] = lang('streams:add_field');
        $extra['show_cancel'] = true;
        $extra['cancel_uri'] = $stream_uri;

        $this->streams->cp->field_form('store_products', 'store', 'new', $stream_uri, null, array(), true, $extra);
    }

    public function edit($id = 0) {
        if (!$id) {
            show_error(lang('streams:cannot_find_assign'));
        }
        
        $stream_uri = 'admin/store/products/fields/';

        $extra = array(
            'title' => lang('streams:edit_field'),
            'show_cancel' => true,
            'cancel_uri' => $stream_uri
        );

        $this->streams->cp->field_form('store_products', 'store', 'edit', $stream_uri, $id, array(), true, $extra);
    }

    public function delete($id = 0) {
        if (!$id) {
            show_error(lang('streams:cannot_find_assign'));
        }

        // Tear down the assignment
        if (!$this->streams->cp->teardown_assignment_field($id)) {
            $this->session->set_flashdata('notice', lang('streams:field_delete_error'));
        } else {
            $this->session->set_flashdata('success', lang('streams:field_delete_success'));
        }
        $stream_uri = 'admin/store/products/fields/';
        redirect($stream_uri);
    }

}
