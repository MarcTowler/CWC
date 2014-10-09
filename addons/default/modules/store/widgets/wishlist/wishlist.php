<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Show RSS feeds in your site
 * 
 * @package 	PyroCMS\Core\Modules\Blog\Widgets
 * @author		Phil Sturgeon
 * @author		PyroCMS Development Team
 */
class Widget_Wishlist extends Widgets {

    public $title = array(
        'en' => 'Wishlist',
    );
    public $description = array(
        'en' => 'Display users wishlist',
    );
    public $author = 'Błażej Adamczyk';
    public $website = 'http://sein.com.pl/';
    public $version = '1.0';
    public $fields = array(
        array(
            'field' => 'display',
            'label' => 'Display as',
            'rules' => 'required'
        ),
        array(
            'field' => 'limit',
            'label' => 'Limit',
            'rules' => 'required'
        )
    );

    public function run($options) {
        if (!$this->current_user) {
            return FALSE;
        }
        $this->load->model('store/wishlist_m');
        if ($options['limit'] > 0) {
            $this->wishlist_m->limit($options['limit']);
        } else {
            $this->wishlist_m->limit(5);
        }
        $items = $this->wishlist_m->order_by('added_on', 'desc')->get_many_by('store_wishlist.user_id', $this->current_user->id);

        return array(
            'items' => $items,
            'options' => $options,
        );
    }

}
