<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * This is a store module for PyroCMS
 *
 * @author 		Blazej Adamczyk
 * @website		http://sein.com.pl
 * @package 	PyroStore
 * @subpackage 	Cart Controller
 */
class Watch_History {
    
    private $watchlist = array();
    private $limit = 5;
    private $expiration = 86400;

    function __construct() {
        
        $cookie = $this->input->cookie(SITE_REF.'_store_watchlist');
        $this->watchlist = $cookie ? unserialize($cookie) : array();
        
    }

    public function add($id) {
        
        if (!in_array($id, $this->watchlist)) {
            array_unshift($this->watchlist, $id);
        } else {
            
        }
        
        if (count($this->watchlist) > $this->limit) {
            $this->watchlist = array_slice($this->watchlist, 0, $this->limit);
        }
        $domain = $this->config->item('cookie_domain');
        
        $cookie = array(
            'name' => 'store_watchlist',
            'value' => serialize($this->watchlist),
            'expire' => strval($this->expiration),
            'domain' => $domain,
            'prefix' => '',
            'path' => BASE_URI,
        );

        $this->input->set_cookie($cookie);
    }

    public function get_list($limit = NULL) {
        if (!$limit || $limit > $this->limit) $limit == $this->limit;
                        
        class_exists('products_m') OR $this->load->model('store/products_m');
        if (count($this->watchlist)) {
            $this->products_m->where_in('store_products.id', $this->watchlist)->limit($limit);
            $items = $this->products_m->get_all();
            return $items;
        }
        return array();
    }

    public function __get($var) {
        if (isset(get_instance()->$var)) {
            return get_instance()->$var;
        }
    }

}
