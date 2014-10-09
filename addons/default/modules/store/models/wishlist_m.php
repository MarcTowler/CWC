<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Wishlist_m extends MY_Model {

    public function __construct() {
        parent::__construct();

        $this->_table = 'store_wishlist';
        $this->primary_key = 'product_id';
    }


    public function get_all() {
        $this->db->select('store_products.*');
        $this->db->select('store_categories.name as category_name, store_categories.slug as category_slug, store_categories.uri as category_uri');
        $this->db->select('store_sales.sale_type, store_sales.new_price_tax, store_sales.new_price, store_sales.reduction'); 
        $this->db->join('store_products', 'store_products.id = store_wishlist.product_id', 'inner');
        $this->db->join('store_sales', 'store_products.id = store_sales.product_id', 'left');
        $this->db->join('store_categories', 'store_products.category_id = store_categories.id', 'left');
        return parent::get_all();
    }
    
   

}
