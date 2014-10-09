<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Uploader extends Admin_Controller {

    public function __construct() {
        
        parent::__construct();
    }


    public function index() {
        $this->load->library('files/files');
        $folder = $this->input->post('folder');
        if (!$folder) {
            $this->output->set_status_header('400');
            echo 'Upload folder not selected';
        }

        if (!empty($_FILES) && $folder) {
            $request = (object) Files::upload($folder, FALSE, 'userfile');
            if ($request->status) {
                echo json_encode($request->data);
            } else {
                $this->output->set_status_header('400');
                echo json_encode($request);
            }
        }
    }

    public function delete($id) {
        if (!$this->db->where('file_id', $id)->from('store_images')->count_all_results()) {
            echo 'No such file';
        } else if (Files::delete_file($id)) {
            echo 'success';
        }
    }

}