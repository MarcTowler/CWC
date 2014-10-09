<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sales_m extends MY_Model {

    public function __construct() {
        $this->_table = "store_sales";
        parent::__construct();
    }

    public function create($input) {
        
        $insert = array(
            'product_id' => $input['product_id'],
            'new_price' => $input['new_price'],
            'new_price_tax' => $input['new_price_tax'],
            'reduction' => intval($input['reduction']),
            'sale_type' => $input['sale_type'],
        );
        
        
        return $this->insert($insert);
    }
    
     public function group($input) {
        
        $items = $this->db->where('category_id', $input['category_id'])->get('store_products')->result();
        
        $insert = array(
            'reduction' => $input['reduction'],
            'sale_type' => $input['sale_type'],
        );
        
        if (count($items)) {
            $multiplier = (100 - $input['reduction']) / 100;
            foreach ($items as $itm) {
                $insert['product_id'] = $itm->id;
                $insert['new_price'] = $multiplier * $itm->price;
                $insert['new_price_tax'] = $multiplier * $itm->price_tax;
                $this->insert($insert);
            }
            return TRUE;
        }
        
        return FALSE;
    }
    
    public function edit($input) {
        $insert = array(
            'product_id' => $input['product_id'],
            'new_price' => $input['new_price'],
            'new_price_tax' => $input['new_price_tax'],
            'reduction' => intval($input['reduction']),
            'sale_type' => $input['sale_type'],
        );
        
        return $this->update($input['id'], $insert);
    }
    
    public function get_all() {
        $this->db->select('store_sales.id AS sale_id');
//        $this->db->select('store_products.id, store_products.price, store_products.price_tax, store_products.slug, store_products.short, store_products.name, store_products.cover_id');
//        $this->db->join('store_products', 'store_sales.product_id = store_products.id', 'inner');
        $this->db->select('store_products.*');
        $this->db->select('store_categories.name as category_name, store_categories.slug as category_slug, store_categories.uri as category_uri');
        $this->db->select('store_sales.sale_type, store_sales.new_price_tax, store_sales.new_price, store_sales.reduction');
        $this->db->join('store_sales', 'store_products.id = store_sales.product_id', 'inner');
        $this->db->join('store_categories', 'store_products.category_id = store_categories.id', 'left');
        
        return $this->db->get('store_products')->result();
    }
    
    

}