<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Show RSS feeds in your site
 * 
 * @package 	PyroCMS\Core\Modules\Blog\Widgets
 * @author		Phil Sturgeon
 * @author		PyroCMS Development Team
 */
class Widget_Customer extends Widgets {

    public $title = array(
        'en' => 'Customer\'s Widget',
    );
    public $description = array(
        'en' => 'Display a list of links that shows up in customer\'s area',
    );
    public $author = 'Błażej Adamczyk';
    public $website = 'http://sein.com.pl/';
    public $version = '1.0';

    public function run($options) {

        $this->lang->load('store/store');
        return $options;
    }

}
