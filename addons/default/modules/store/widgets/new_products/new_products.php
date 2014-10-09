<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Show RSS feeds in your site
 * 
 * @package 	PyroCMS\Core\Modules\Blog\Widgets
 * @author		Phil Sturgeon
 * @author		PyroCMS Development Team
 */
class Widget_New_Products extends Widgets {

    public $title = array(
        'en' => 'New Products',
    );
    public $description = array(
        'en' => 'Display recently added products',
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
        
        $duration = Settings::get('store_new_duration');
        if ($duration) {
            $duration = $duration*86400;
            $from_date = date('Y-m-d H:i:s',  time() - $duration);
            $this->products_m->where('date_created > ', $from_date);
        }
        
        if ($options['limit'] > 0) {
            $this->products_m->limit($options['limit']);
        } else {
            $this->products_m->limit(1);
        }
        
        $new = $this->products_m->order_by('date_created', 'desc')->get_all();
        
        return count($new) ? array('items' => $new) : false;
    }

}
