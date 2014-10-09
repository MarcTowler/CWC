<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 
 * @author		PyroCMS Dev Team
 * @package		PyroCMS\Core\Modules\Blog\Controllers
 */
class Sitemap extends Public_Controller
{
	/**
	 * XML
	 * 
	 * @return void
	 */
	public function xml() {

        $doc = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" />');

        $products = $this->pyrocache->model('products_m', 'get_all', array(), 86400);

        // send em to XML!
        foreach ($products as $prod) {
            $node = $doc->addChild('url');

            $loc = site_url('store/product/'  . $prod->slug);

            $node->addChild('loc', $loc);

            if ($prod->date_created) {
                $node->addChild('lastmod', date(DATE_W3C, strtotime($prod->date_created)));
            }
        }
        
        $categories = $this->pyrocache->model('categories_m', 'get_all', array(), 86400);
        foreach ($categories as $cat) {
            $node = $doc->addChild('url');

            $loc = site_url('store/category/'  . $cat->uri);

            $node->addChild('loc', $loc);
        }

        $this->output
                ->set_content_type('application/xml')
                ->set_output($doc->asXML());
    }
}
