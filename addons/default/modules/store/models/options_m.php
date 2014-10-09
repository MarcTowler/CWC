<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * This is a store module for PyroCMS
 *
 * @author 		Jerel Unruh - PyroCMS Dev Team
 * @website		http://unruhdesigns.com
 * @package 	PyroCMS
 * @subpackage 	store Module
 */
class Options_m extends MY_Model {

    public function __construct() {
        parent::__construct();

        $this->_table = 'store_options';
    }
    
    //create a new item
    public function create($input) {
        $to_insert = array(
            'title' => $input['title'],
            'slug' => $this->_check_slug($input['slug']),
            'field_type' => $input['field_type'],
        );
        $option_id = $this->insert($to_insert);
        $labels = $input['values']['label'];
        $amounts = $input['values']['value'];
        
        $values = array();
        for ($index = 0; $index < count($labels); $index++) {
            $values[] = array(
                'option_id' => $option_id,
                'label' => $labels[$index],
                'value' => $amounts[$index],
            );
        }
        $this->db->insert_batch('store_option_values', $values); 
        
        return $option_id;
    }
    
    public function update($primary_value, $data, $skip_validation = FALSE) {
        $update = array(
            'title' => $data['title'],
            'slug' => $this->_check_slug($data['slug']),
            'field_type' => $data['field_type'],
        );
        
        $result = parent::update($primary_value, $update, $skip_validation);
        $this->db->delete('store_option_values', array('option_id' => $primary_value)); 
        
        $labels = $data['values']['label'];
        $amounts = $data['values']['value'];
        
        $values = array();
        for ($index = 0; $index < count($labels); $index++) {
            $values[] = array(
                'option_id' => $primary_value,
                'label' => $labels[$index],
                'value' => $amounts[$index],
            );
        }
        $this->db->insert_batch('store_option_values', $values); 
        return $result;
    }

    public function get_by_product($prod) {
        $this->db->select('store_options.*, store_product_options.*, store_option_values.label, store_option_values.value');
        $this->db->where('store_product_options.product_id', $prod);
        $this->db->join('store_options','store_product_options.option_id = store_options.id');
        $this->db->join('store_option_values','store_product_options.value_id = store_option_values.id');
        $this->db->order_by('store_product_options.sort', 'ASC');

        $items = $this->db->get('store_product_options')->result();
        /*foreach ($items as &$item) {
            $item->values = $this->db->where('option_id', $item->id)->get('store_option_values')->result();
        }*/
        return $items;
    }

    public function count_price($prod_id, $options)
    {
        $this->db->select_sum($this->db->dbprefix('store_product_options').'.price');
        $this->db->where('store_product_options.product_id', $prod_id);
        $or = false;
        foreach ($options as $key => $value) {
            if ($or) 
                $this->db->or_where('store_options.slug', $key);
            else
                $this->db->where('store_options.slug', $key);
            $or = true;

            if (is_array($value))
                $this->db->where_in('store_option_values.value', $value);
            else
                $this->db->where('store_option_values.value', $value);
        }
        $this->db->join('store_options','store_product_options.option_id = store_options.id');
        $this->db->join('store_option_values','store_product_options.value_id = store_option_values.id');
        //$this->db->group_by('store_options.slug');
        $sum = $this->db->get('store_product_options')->row();
        return $sum->price;
    }

    public function parse_options($prod)
    {
        $options = $this->get_by_product($prod);
        $items = array();

        foreach ($options as $opt) {
            if (!array_key_exists($opt->option_id, $items)){
                $items[$opt->option_id] = array(
                    'id' => $opt->option_id, 
                    'title' => $opt->title, 
                    'slug' => $opt->slug, 
                    'field_type' => $opt->field_type, 
                    'values' => array()
                );
            }
            if (!array_key_exists($opt->value_id, $items[$opt->option_id]['values'])) {
                $items[$opt->option_id]['values'][$opt->value_id] = array(
                    'id' => $opt->value_id,
                    'label' => $opt->label,
                    'value' => $opt->value,
                    'price' => $opt->price
                );
            }
        }


        /*foreach ($items as &$item) {
            $item->values = $this->db->where('option_id', $item->id)->get('store_option_values')->result();
        }*/
        return $items;
    }

    
    public function get($id) {
        $item = parent::get($id);
        $item->values = $this->db->where('option_id', $id)->get('store_option_values')->result();
        return $item;
    }
    
    public function _check_slug($slug) {
        $slug = strtolower($slug);
        $slug = preg_replace('/\s+/', '-', $slug);

        return $slug;
    }
    
    public function get_all() {
        $items = parent::get_all();
        foreach ($items as &$item) {
            $item->values = $this->db->select('label')->where('option_id', $item->id)->get('store_option_values')->result();
        }
        return $items;
    }
    
    public function hook($product, $option) {
        $row = $this->db->where('product_id', $product)->where('option_id', $option)->get('store_product_options')->row();
        if ($row) {
            return false;
        }
        return $this->db->insert('store_product_options', array('product_id' => $product, 'option_id' => $option, 'sort' => 0));
    }
    
    public function unhook($product, $option) {
        return $this->db->delete('store_product_options', array('product_id' => $product, 'option_id' => $option));
    }
    
    public function delete($id) {
        $this->db->delete('store_option_values', array('option_id' => $id)); 
        return parent::delete($id);
    }
    
    public function sort($input) {
        $order = explode(',', $input['order']);
        foreach ($order as $key => $value) {
            $this->db->update('store_product_options', array('sort' => $key), array('product_id' => $input['product'], 'option_id' => $value));
        }
        return TRUE;
    }

}
