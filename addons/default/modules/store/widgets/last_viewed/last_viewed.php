<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Show RSS feeds in your site
 * 
 * @package 	PyroCMS\Core\Modules\Blog\Widgets
 * @author		Phil Sturgeon
 * @author		PyroCMS Development Team
 */
class Widget_Last_Viewed extends Widgets {

    public $title = array(
        'en' => 'Last viewed',
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
        
        class_exists('watch_history') OR $this->load->library('store/watch_history');
        
        $this->lang->load('store/store');

        return array(
            'items' => $this->watch_history->get_list($options['limit']),
        );
    }

}
