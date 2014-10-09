<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Show RSS feeds in your site
 * 
 * @package 	PyroCMS\Core\Modules\Blog\Widgets
 * @author		Phil Sturgeon
 * @author		PyroCMS Development Team
 */
class Widget_Bestsellers extends Widgets {

    public $title = array(
        'en' => 'Bestsellers',
    );
    public $description = array(
        'en' => 'Display best selling items',
    );
    public $author = 'Błażej Adamczyk';
    public $website = 'http://sein.com.pl/';
    public $version = '1.0';
    public $fields = array(
        array(
            'field' => 'limit',
            'label' => 'Limit',
            'rules' => 'required'
        )
    );

    public function run($options) {
        
        $this->load->model('store/products_m');
        $this->lang->load('store/store');
        if ($options['limit'] > 0) {
            $this->products_m->limit($options['limit']);
        } else {
            $this->products_m->limit(1);
        }
        $this->products_m->select('store_products.*, SUM(`default_store_order_contents`.`qty`) AS sold');
        $this->products_m->join('store_order_contents', 'store_order_contents.product_id = store_products.id' );
        $this->products_m->group_by('store_products.id');
        $items = $this->products_m->order_by('sold', 'desc')->get_all();

        return array(
            'items' => $items,
        );
    }

}
