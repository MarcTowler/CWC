<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Plugin_Store extends Plugin {

    /**
     * Item List
     * Usage:
     * 
     * {{ store:items limit="5" order-by="name" order-dir="asc" category-id="2" }}
     *      {{ id }} {{ name }} {{ slug }}
     * {{ /store:items }}
     *
     * @return	array
     */
    function items() {
        
        $ci = &get_instance();
        class_exists('products_m') OR $ci->load->model('store/products_m');
        
        $limit = intval($this->attribute('limit', 0));
        $order_by = $this->attribute('order-by', 'date_created');
        $order_dir = $this->attribute('order-dir', 'asc');
        $category = intval($this->attribute('category-id', $this->attribute('category_id', 0)));
        
        if (is_numeric($this->attribute('category')) && $this->attribute('category') > 0) {
            $category = intval($this->attribute('category'));
            $ci->products_m->where('category_id', $category);
        } else if(is_string($this->attribute('category')) && strlen($this->attribute('category')) > 0) {
            $ci->products_m->where('store_categories.slug', $this->attribute('category'));
        } else if (is_numeric($category) && $category > 0) {
            $ci->products_m->where('category_id', $category);
        }
        if (is_numeric($limit) && $limit > 0) {
            $ci->products_m->limit($limit);
        }

        $products = $ci->products_m->order_by($order_by, $order_dir)
                ->get_all();
        
        return $products;
    }
    /**
     * Item List
     * Usage:
     * 
     * {{ store:product id="2" slug="product-slug" return="string|array"  }}
     *      {{ id }} {{ name }} {{ slug }}
     * {{ /store:product }}
     *
     * @return	array
     */
    function product() {
        
        $ci = &get_instance();
        class_exists('products_m') OR $ci->load->model('store/products_m');
        
        $return_as = $this->attribute('return', 'array');
        $product_id = $this->attribute('id');
        $product_slug = $this->attribute('slug');
        $get_by = 'id';
        $get_value = 0;
        if (!$product_id && !$product_slug) {
            return false;
        }
        if (is_numeric($product_id) && $product_id > 0) {
            $get_by = 'id';
            $get_value = $product_id;
        } else if(is_string($product_slug)) {
            $get_by = 'slug';
            $get_value = $product_slug;
        } else {
            return false;
        }
        $product = $ci->products_m->get_by($get_by, $get_value);
        
        if ($return_as == "array" || !isset($product->{$return_as})) {
            return $product;
        } else {
            return $product->{$return_as};
        }
        return $product;
    }

    function extras() {
        $limit = intval($this->attribute('limit', 0));
        $type = $this->attribute('type', 'featured');

        $ci = &get_instance();
        class_exists('products_m') OR $ci->load->model('store/products_m');

        $ci->products_m->where('sale_type', $type);

        if (is_numeric($limit) && $limit > 0) {
            $ci->products_m->limit($limit);
        }

        return $ci->products_m
                        ->get_all();
    }

    function categories() {
        $order = $this->attribute('order-by', 'order');
        $parent = $this->attribute('parent', false);

        $options = array('order' => $order, 'parent_id' => $parent);

        class_exists('categories_m') OR $this->load->model('store/categories_m');

        $categories = $this->categories_m->get_link_tree($options);

        return $categories;
    }

    function option_value() {

        $prod_id = $this->attribute('product');
        $optname = $this->attribute('option');

        $this->db->where('name', $optname);

        $result = $this->db->where('product_id', $prod_id)->get('store_options')->row_array();

        if (!empty($result)) {
            $option['option_name'] = $result['name'];
            $values = unserialize($result['values']);
            return implode(', ', $values);
        }
        return '';
    }

    /**
     * Returns a number of items in customer's cart
     * @return string
     */
    function cart_items() {
        return $this->cart->total_items() . nbs();
    }

    /**
     * Return a total cost of items in customer's cart
     * @return string
     */
    function cart_total() {
        return $this->cart->total() . nbs();
    }

    /**
     * Returns an array of items in customer's cart
     * @return mixed
     */
    function cart_contents() {
        return $this->cart->contents();
    }

}

/* End of file plugin.php */