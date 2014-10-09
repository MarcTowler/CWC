<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Show RSS feeds in your site
 * 
 * @package 	PyroCMS\Core\Modules\Blog\Widgets
 * @author		Phil Sturgeon
 * @author		PyroCMS Development Team
 */

class Widget_Simple_Cart extends Widgets
{
	public $title		= array(
		'en' => 'Simple Shoping Cart',
	);
	public $description	= array(
		'en' => 'Display a list of items user added to cart',
	);
	public $author		= 'Błażej Adamczyk';
	public $website		= 'http://sein.com.pl/';
	public $version		= '1.0';
	
	public function run($options)
	{
		
                $this->load->library('store/basket', '', 'cart');
		$this->lang->load('store/store');

		return array(
                        'total_cost' => $this->cart->total(),
                        'total_items' => $this->cart->total_items()
		);
	}	
}
