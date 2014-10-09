<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Products_m extends MY_Model {

    public function __construct() {
        parent::__construct();

        $this->_table = 'store_products';
        $this->load->helper('store/store');
        //$this->load->model('search/search_index_m'); // Search indexes sux, manual implementation needed for the time being
        
        $this->load->driver('Streams');
    }

    //create a new item
    public function create($input) {

        $extra = array(
            'name' => $input['name'],
            'created' => date('Y-m-d H:i:s', time()),
            'short' => substr(strip_tags($input['short']),0, 254) || "",
            'sku' => $input['sku'],
            'slug' => $this->_check_slug($input['slug']),
            'keywords' => $input['keywords_hash'],
            'attributes' => prepare_attributes($input['attributes']),
            'price' => $input['price'],
            'price_tax' => $input['price_tax'],
            'status' => $input['status'],
            'cover_id' => $input['cover_id'],
            'category_id' => $input['category_id'],
            'description' => $input['description']
        );

        
        $product_id = $this->streams->entries->insert_entry($input, 'store_products', 'store', array('created'), $extra);
        
        if(count($input['images'])) {
            foreach ($input['images'] as $image) {
                $this->db->insert('store_images', array(
                    'product_id' => $product_id,
                    'file_id' => $image
                ));
            }
        }
        
        if (count($input['related'])) {
            foreach ($input['related'] as $rel) {
                $this->db->insert('store_related', array(
                    'product_id' => $product_id,
                    'rel_id' => $rel['id']
                ));
            }
        }

        if (count($input['options'])) {
            foreach ($input['options'] as $opt) {
                $this->db->insert('store_product_options', array(
                    'product_id' => $product_id,
                    'option_id' => intval($opt['option_id']),
                    'value_id' => intval($opt['value_id']),
                    'price' => floatval($opt['price']),
                    'sort' => 0
                ));
            }
        }

        // add search index
        if ($product_id) {
            $search_result = $this->db->insert('search_index',array(
                'title' => $input['name'],
                'description' => $input['short'],
                'keywords' => $input['keywords'],
                'keyword_hash' => $input['keywords_hash'],
                'module' => 'store',
                'entry_key' => 'store:product',
                'entry_plural' => 'store:products',
                'entry_id' => $product_id,
                'uri' => 'store/product/'.$this->_check_slug($input['slug']),
                'cp_edit_uri' => 'admin/store/product/edit/'.$product_id,
                'cp_delete_uri' => 'admin/store/product/delete/'.$product_id,
            ));
            if (!$search_result) {
                log_message('error', 'STORE: Search entry for product '.$input['name'].':'.$product_id.' was not created.');
            }
        }

        return $product_id;
    }

    public function edit($id, $input) {

        // fields added expicetely not handled by streams
        $extra = array(
            'name' => $input['name'],
            'short' => substr(strip_tags($input['short']),0, 254) || "",
            'slug' => $this->_check_slug($input['slug']),
            'sku' => $input['sku'],
            'updated' => date('Y-m-d H:i:s', time()),
            'keywords' => $input['keywords_hash'],
            'attributes' => prepare_attributes($input['attributes']),
            'price' => $input['price'],
            'price_tax' => $input['price_tax'],
            'status' => $input['status'],
            'cover_id' => $input['cover_id'],
            'category_id' => $input['category_id'],
            'description' => $input['description'],
        );
        
        //$result = $this->update($id, $to_insert);  // deprecated since we switched to streams
        $result = $this->streams->entries->update_entry($id, $input, 'store_products', 'store', array('updated'), $extra);
        
        // remove images anyway, since if they stays they will be passed again, 
        // if not, they won't be passed so we have to remove them in the first place
        $this->db->where('product_id', $id)->delete('store_images');
        
        if(count($input['images'])) {
            foreach ($input['images'] as $image) {
                $this->db->insert('store_images', array(
                    'product_id' => $id,
                    'file_id' => $image
                ));
            }
        }
        
        // same goes for related products
        $this->db->where('product_id', $id)->delete('store_related');
        
        if (count($input['related'])) {
            foreach ($input['related'] as $rel) {
                $this->db->insert('store_related', array(
                    'product_id' => $id,
                    'rel_id' => $rel
                ));
            }
        }

        // and for product options
        $this->db->where('product_id', $id)->delete('store_product_options');

        if (count($input['options'])) {
            foreach ($input['options'] as $opt) {
                $this->db->insert('store_product_options', array(
                    'product_id' => $id,
                    'option_id' => intval($opt['option_id']),
                    'value_id' => intval($opt['value_id']),
                    'price' => floatval($opt['price']),
                    'sort' => 0
                ));
            }
        }

        // update search index
        if ($id){
            $this->db->where(array('module' => 'store', 'entry_id' => $id));
            $search_id = $this->db->update('search_index', array(
                'title' => $input['name'],
                'description' => $input['short'],
                'keywords' => $input['keywords'],
                'keyword_hash' => $input['keywords_hash'],
                'uri' => 'store/product/'.$this->_check_slug($input['slug']),
            ));
            if (!$search_id) {
                log_message('error', 'STORE: Search entry for product '.$input['name'].':'.$id.' was not updated.');
            }
        }

        return $result;
    }

    public function delete($id)
    {
        if (!class_exists('Search_index_m')) {
            $this->load->model('search/search_index_m');
        }
        $this->search_index_m->delete_by(array('module' => 'store', 'entry_id' => $id));
        return parent::delete($id);
    }

    public function delete_many($primary_values)
    {
        if (!class_exists('Search_index_m')) {
            $this->load->model('search/search_index_m');
        }
        $this->db->where_in('id', $primary_values);
        $this->db->delete('search_index', array('module' => 'store'));
        //$this->search_index_m->delete_many(array('module' => 'store', 'entry_id' => $id));
        return parent::delete_many($primary_values);
    }

    public function get_related($id) {
        // TODO: create related link
        $this->db->select('store_products.*');
        $this->db->select('store_categories.name as category_name, store_categories.slug as category_slug, store_categories.uri as category_uri');
        $this->db->select('store_sales.sale_type, store_sales.new_price_tax, store_sales.new_price, store_sales.reduction');
        //$this->db->from('');
        $this->db->join('store_products', 'store_related.rel_id = store_products.id', 'inner');
        $this->db->join('store_sales', 'store_products.id = store_sales.product_id', 'left');
        $this->db->join('store_categories', 'store_products.category_id = store_categories.id', 'left');
        $this->db->where('store_related.product_id', $id);
        $result = $this->db->get('store_related')->result();
        //print_r($result);

        return $result;
    }

    public function get_all_within($categories, $limit, $offset = 0, $sort = array('created', 'asc')) {
        $this->limit($limit)
                ->order_by($sort[0], $sort[1])
                ->offset($offset);

        foreach ($categories as $category) {
            $this->or_where('category_id', $category->id);
        }
        return $this->get_all();
    }

    public function count_all_within($categories) {
        foreach ($categories as $category) {
            $this->or_where('category_id', $category->id);
        }
        return $this->db->count_all_results($this->_table);
    }

    public function get_images($product_id) {
        $this->db->select('files.*, store_images.file_id');
        $this->db->join('files', 'files.id = store_images.file_id');
        return $this->db->where('product_id', $product_id)->get('store_images')->result();
    }

    public function get_options($product_id) {
        $options = $this->db->where('product_id', $product_id)->get('store_options')->result();
        foreach ($options as &$item) {
            $values = unserialize($item->values);
            $item->values = array();
            foreach ($values as $val) {
                $item->values[$val] = $val;
            }
        }
        return $options;
    }
    

    public function get_all() {
        $this->db->select('store_products.*');
        $this->db->select('store_categories.name as category_name, store_categories.slug as category_slug, store_categories.uri as category_uri');
        $this->db->select('store_sales.sale_type, store_sales.new_price_tax, store_sales.new_price, store_sales.reduction');
        $this->db->join('store_sales', 'store_products.id = store_sales.product_id', 'left');
        $this->db->join('store_categories', 'store_products.category_id = store_categories.id', 'left');
        return parent::get_all();
    }

    public function get($id) {
        $this->db->select('store_products.*, store_categories.name as category_name, store_categories.slug as category_slug, store_categories.uri as category_uri, store_categories.id as category_id');
        $this->db->select('store_sales.sale_type, store_sales.new_price_tax, store_sales.new_price, store_sales.reduction');
        $this->db->join('store_categories', 'store_products.category_id = store_categories.id', 'left');
        $this->db->join('store_sales', 'store_products.id = store_sales.product_id', 'left');

        $item = parent::get_by('store_products.id', $id);
        
        $this->db->join('store_related', 'store_products.id = store_related.rel_id', 'inner');
        $this->db->where('store_related.product_id', $id);
        //$this->db->group_by('store_products.id');
        
        $related = $this->get_all();
        $item->related = $related;

        return $item;
    }

    public function get_by_slug($slug) {
        $this->db->select('store_products.*,  store_categories.name as category_name, store_categories.slug as category_slug, store_categories.uri as category_uri, store_categories.id as category_id');
        $this->db->join('store_categories', 'store_products.category_id = store_categories.id', 'left');
        $this->db->select('store_sales.sale_type, store_sales.new_price_tax, store_sales.new_price, store_sales.reduction');
        $this->db->join('store_sales', 'store_products.id = store_sales.product_id', 'left');

        $item = parent::get_by('store_products.slug', $slug);
        
        $this->db->join('store_related', 'store_products.id = store_related.rel_id', 'inner');
        $this->db->where('store_related.product_id', $item->id);
        //$this->db->group_by('store_products.id');
        
        $related = $this->get_all();
        $item->related = $related;

        return $item;
    }

    //make sure the slug is valid
    public function _check_slug($slug) {
        $slug = strtolower($slug);
        $slug = preg_replace('/\s+/', '-', $slug);

        return $slug;
    }

    public function filter($data = array()) {
        if (array_key_exists('category_id', $data)) {
            $this->db->where('category_id', $data['category_id']);
        }

        if (array_key_exists('stock', $data)) {
            $this->db->where('stock <', $data['stock']);
        }

        if (array_key_exists('keywords', $data)) {
            $matches = array();
            if (strstr($data['keywords'], '%')) {
                preg_match_all('/%.*?%/i', $data['keywords'], $matches);
            }

            if (!empty($matches[0])) {
                foreach ($matches[0] as $match) {
                    $phrases[] = str_replace('%', '', $match);
                }
            } else {
                $temp_phrases = explode(' ', $data['keywords']);
                foreach ($temp_phrases as $phrase) {
                    $phrases[] = str_replace('%', '', $phrase);
                }
            }

            $counter = 0;
            foreach ($phrases as $key) {
                if ($counter == 0) {
                    $this->db->like('store_products.name', $key);
                    $this->db->or_like('store_products.description', $key);
                } else {
                    $this->db->or_like('store_products.name', $key);
                    $this->db->or_like('store_products.description', $key);
                }

                $counter++;
            }
        }
        //return $this->get_all();
    }

    public function count_tagged_by($tag) {
        return $this->select('*')
                        ->from('store_products')
                        ->join('keywords_applied', 'keywords_applied.hash = store_products.keywords')
                        ->join('keywords', 'keywords.id = keywords_applied.keyword_id')
                        ->where('keywords.name', str_replace('-', ' ', $tag))
                        ->count_all_results();
    }

    public function get_tagged_by($tag) {
        $this->db->select('store_products.*');
        $this->db->select('store_categories.name as category_name, store_categories.slug as category_slug, store_categories.uri as category_uri');
        $this->db->select('store_sales.sale_type, store_sales.reduction');
        $this->db->join('store_sales', 'store_products.id = store_sales.product_id', 'left');
        $this->db->join('store_categories', 'store_products.category_id = store_categories.id', 'left');

        return $this->db->from('store_products')
                        ->join('keywords_applied', 'keywords_applied.hash = store_products.keywords')
                        ->join('keywords', 'keywords.id = keywords_applied.keyword_id')
                        ->where('keywords.name', str_replace('-', ' ', $tag))
                        ->get()
                        ->result();
    }

}
