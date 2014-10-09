<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Manages image selection and insertion for Store Module
 *
 * @author 		Blazej Adamczyk
 * @website		http://sein.com.pl
 * @package 	PyroStore
 * @subpackage 	Ajax Files Manger
 */
class Ajax extends MY_Controller {

	public function __construct()
	{
            parent::__construct();

		// Not logged in or not an admin and don't have permission to see files
		if ( ! $this->current_user OR
			($this->current_user->group !== 'admin' AND 
			( ! isset($this->permissions['files']) OR
			  ! isset($this->permissions['files']['wysiwyg']))))
		{
			$this->load->language('files/files');
			show_error(lang('files:no_permissions'));
		}

		ci()->admin_theme = $this->theme_m->get_admin();

		// Using a bad slug? Weak
		if (empty($this->admin_theme->slug))
		{
			show_error('This site has been set to use an admin theme that does not exist.');
		}

		// Make a constant as this is used in a lot of places
		defined('ADMIN_THEME') or define('ADMIN_THEME', $this->admin_theme->slug);

		// Set the location of assets
		Asset::add_path('module', $this->module_details['path'].'/');
		Asset::add_path('theme', $this->admin_theme->web_path.'/');
		Asset::set_path('theme');

		$this->load->library('files/files');
		$this->lang->load('files/files');
		$this->lang->load('wysiwyg/wysiwyg');
		$this->lang->load('buttons');

		$this->template
			->set_theme(ADMIN_THEME)
			->set_layout('wysiwyg', 'admin')
			->enable_parser(FALSE)
			->append_css('jquery/ui-lightness/jquery-ui.css')
			->append_js('jquery/jquery.js')
			->append_js('jquery/jquery-ui.min.js')
			->append_js('plugins.js')
			->append_css('module::wysiwyg.css')
			->append_js('module::wysiwyg.js');
	}
	
	public function image($id = 0)
	{
		$data->folders			= $this->file_folders_m->get_folders();
		$data->subfolders		= array();
		$data->current_folder	= $id && isset($data->folders[$id])
								? $data->folders[$id]
								: ($data->folders ? current($data->folders) : array());

		if ($data->current_folder)
		{
			$data->current_folder->items = $this->file_m
				->select('files.*, file_folders.location')
				->join('file_folders', 'file_folders.id = files.folder_id')
				->order_by('files.date_added', 'DESC')
				->where('files.type', 'i')
				->get_many_by('files.folder_id', $data->current_folder->id);

			$subfolders = $this->file_folders_m->folder_tree($data->current_folder->id);

			foreach ($subfolders as $subfolder)
			{
				$data->subfolders[$subfolder->id] = repeater('&raquo; ', $subfolder->depth) . $subfolder->name;
			}

			// Set a default label
			$data->subfolders = $data->subfolders
				? array($data->current_folder->id => lang('files:root')) + $data->subfolders
				: array($data->current_folder->id => lang('files:no_subfolders'));
		}

		// Array for select
		$data->folders_tree = array();
		foreach ($data->folders as $folder)
		{
			$data->folders_tree[$folder->id] = repeater('&raquo; ', $folder->depth) . $folder->name;
		}

		$this->template
			->title('Images')
			->append_css('admin/basic_layout.css')
			->build('admin/ajax/image', $data);
	}
        
        
        public function upload()
	{
            
                $this->config->load('files/files');
                $this->_path = FCPATH . '/' . $this->config->item('files:path') . '/';

                        // If the folder hasn't been created by the files module create it now
                is_dir($this->_path) OR mkdir($this->_path, 0777, TRUE);

		$this->load->library('form_validation');

		$rules = array(
			array(
				'field'   => 'name',
				'label'   => 'lang:files:name',
				'rules'   => 'trim'
			),
			array(
				'field'   => 'description',
				'label'   => 'lang:files:description',
				'rules'   => ''
			),
			array(
				'field'   => 'folder_id',
				'label'   => 'lang:files:folder',
				'rules'   => 'required'
			),
		);

		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run())
		{
			$input = $this->input->post();

			$results = Files::upload($input['folder_id'], $input['name'], 'userfile');

			// if the upload was successful then we'll add the description too
			if ($results['status'])
			{
				$data = $results['data'];
				$this->file_m->update($data->id, array('description' => $input['description']));
			}

			// upload has a message to share... good or bad?
			$this->session->set_flashdata($results['status'] ? 'success' : 'notice', $results['message']);
		}
		else
		{
			$this->session->set_flashdata('error', validation_errors());
		}

		redirect("admin/store/ajax/image/{$this->input->post('folder_id')}");
	}
        
        public function subfolder()
	{
            
                $this->config->load('files/files');
                $this->_path = FCPATH . '/' . $this->config->item('files:path') . '/';

                        // If the folder hasn't been created by the files module create it now
                is_dir($this->_path) OR mkdir($this->_path, 0777, TRUE);

		$this->load->library('form_validation');

		$rules = array(
			array(
				'field'   => 'name',
				'label'   => 'lang:files:name',
				'rules'   => 'trim'
			),
			array(
				'field'   => 'folder_id',
				'label'   => 'lang:files:folder',
				'rules'   => 'required'
			),
		);

		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run())
		{
			$input = $this->input->post();

			$results = Files::create_folder($input['folder_id'], $input['name']);

			// if the upload was successful then we'll add the description too
			if ($results['status'])
			{
				$data = $results['data'];
			}

			// upload has a message to share... good or bad?
			$this->session->set_flashdata($results['status'] ? 'success' : 'notice', $results['message']);
		}
		else
		{
			$this->session->set_flashdata('error', validation_errors());
		}

		redirect("admin/store/ajax/image/{$data['id']}");
	}

}