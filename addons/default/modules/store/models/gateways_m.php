<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Gateways_m extends MY_Model {

    private $path;
    private $addon_folder;

    public function __construct() {
        $this->_table = "store_gateways";
        parent::__construct();
        $this->load->library('PaymentGateway');
        
        $this->path = $this->module_details['path'] . '/gateways/';
        $this->addon_folder = strpos($this->module_details['path'], 'shared_addons') ? 'shared_addons' : SITE_REF;
    }

    
    public function install($slug) {
        $gateway_path = $this->path . $slug . '/' . $slug . '.php';

        if (is_file($gateway_path)) {
            include_once $gateway_path;
            $class = "Gateway_" . ucfirst(strtolower($slug));

            if (class_exists($class)) {
                $gateway = new $class;
                $installer = array(
                    'name' => $gateway->title, 
                    'desc' => $gateway->description, 
                    'slug' => $slug,
                    'enabled' => 0,
                    'options' => '',
                    );

                return $this->insert($installer);
            }
        }
        return FALSE;
    }

    public function edit($input) {
        $insert = array(
            'name' => $input['name'],
            'desc' => $input['desc'],
            'enabled' => isset($input['enabled']) ? 1 : 0,
            'options' => isset($input['options']) ? serialize($input['options']) : '',
        );

        return $this->update($input['id'], $insert);
    }

    //make sure the slug is valid
    private function _check_slug($slug) {
        $slug = strtolower($slug);
        $slug = preg_replace('/\s+/', '-', $slug);

        return $slug;
    }

    public function get_all() {
        $items = parent::get_all();
        foreach ($items as $item) {
            $filepath = $this->module_details['path'] . '/gateways/' . $item->slug . '/' . $item->slug . '.png';
            if (file_exists(FCPATH . '/' . $filepath)) {
                $item->image = base_url() . $filepath;
            } else {
                $item->image = '';
            }
        }
        return $items;
    }

    public function load($id) {
        if (!$id) {
            return NULL;
        }
        
        if (is_numeric($id)) {
            $item = $this->get($id);
        } else {
            $item = $this->get_by('slug', $id);
        }
        if (!$item) {
            return NULL;
        }

        $gateway_path = $this->path . $item->slug . '/' . $item->slug . '.php';
        include_once $gateway_path;

        $class = "Gateway_" . ucfirst(strtolower($item->slug));
        $object = new $class;

        foreach ($item as $key => $value) {
            if ($key != 'options')
                $object->{$key} = $value;
        }
        if (is_array(unserialize($item->options)))
            $object->options = array_merge($object->options, unserialize($item->options));

        if (method_exists($object, 'form')){
            $object->form($object->options);
        }

        if (file_exists($this->path . $item->slug . '/' . $item->slug . '.png')) {
            $object->image = base_url() . 'addons/' . $this->addon_folder . '/modules/store/gateways/' . $item->slug . '/' . $item->slug . '.png';
        }

        $object->form = $this->path . $item->slug . '/views/form.php';
        $object->display = $this->path . $item->slug . '/views/display.php';

        return $object;
    }

    public function get_uninstalled() {
        $map = directory_map($this->path, 1);

        $gateways = array();

        foreach ($map as $key => $folder) {
            if (!is_dir($this->path . $folder)) {
                unset($map[$key]);
            } else {
                $gateway_path = $this->path . $folder . '/' . $folder . '.php';
                if ($this->get_by('slug', $folder)) {
                    continue;
                }
                if (is_file($gateway_path)) {
                    include_once $gateway_path;
                    $class = "Gateway_" .  mb_convert_case(strtolower($folder), MB_CASE_TITLE, "UTF-8");
                    if (class_exists($class)) {
                        $item = new $class;
                        $item->slug = $folder;
                        if (file_exists($this->path . $item->slug . '/' . $item->slug . '.png')) {
                            $item->image = base_url() . 'addons/' . $this->addon_folder . '/modules/store/gateways/' . $item->slug . '/' . $item->slug . '.png';
                        }
                        $gateways[] = $item;
                    } else {
                        $gateways[] = (object) array(
                            'slug' => $folder,
                            'title' => 'BUG',
                            'description' => 'Invalid Class Name'
                        );
                    }
                } else {
                    $gateways[] = (object) array(
                        'slug' => $folder,
                        'title' => 'BUG',
                        'description' => 'Invalid File Name'
                    );
                }
            }
        }

        $slugs = array();
        foreach ($gateways as $key => $row) {
            $slugs[$key]  = $row->slug;
        }
        array_multisort($slugs, SORT_ASC, $gateways);

        return $gateways;
    }


}