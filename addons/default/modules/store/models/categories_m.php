<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Categories_m extends MY_Model {

    public function __construct() {
        parent::__construct();
        /**
         * If the store module's table was named "stores"
         * then MY_Model would find it automatically. Since
         * I named it "store" then we just set the name here.
         */
        $this->_table = 'store_categories';
    }

    //create a new item
    public function create($input) {

        $this->db->trans_start();
        $to_insert = array(
            'name' => $input['name'],
            'description' => strip_tags($input['description']),
            'slug' => $this->_check_slug($input['slug']),
            'uri' => '',
            'image_id' => $input['image_id'],
            'parent_id' => $input['parent_id'],
        );

        $input['id'] = $this->insert($to_insert);
        $this->build_lookup($input['id']);
        $this->db->trans_complete();
//        return $cat_id;
        return ($this->db->trans_status() === false) ? false : $input['id'];
    }

    public function edit($id, $input) {

        $this->db->trans_start();
        $result = $this->update($id, $input);
        if (!$result)
            return false;

        $input['id'] = $id;
//        return $result;
        $this->build_lookup($input['id']);
        $this->db->trans_complete();

        return ($this->db->trans_status() === false) ? false : $result;
    }

    public function build_lookup($id) {
        $current_id = $id;
        $segments = array();

        do {
            $category = $this->db
                    ->select('slug, parent_id')
                    ->where('id', $current_id)
                    ->get($this->_table)
                    ->row();
            $current_id = $category->parent_id;
            array_unshift($segments, $category->slug);
        } while ($category->parent_id > 0);
        return $this->update($id, array('uri' => implode('/', $segments)));
    }

    public function update_order($data = array()) {
        $this->db->set($data)->update($this->_table);
//        $this->db->trans_start();
//        $this->db->set($data)->update($this->_table);
//        
//        $this->reindex_lookup($data);
//        $this->db->trans_complete();
//        
//        return ($this->db->trans_status() === false) ? false : true;
    }
    
    public function reindex_lookup($data) {
        foreach ($data as $value) {
            if (isset($value['children'])){
                $this->reindex_lookup($value);
            } else if(isset ($value['id'])) {
                $this->build_lookup($value['id']);
            }
        }
    }

    //make sure the slug is valid
    public function _check_slug($slug) {
        $slug = strtolower($slug);
        $slug = preg_replace('/\s+/', '-', $slug);
        return $slug;
    }

    public function get_category($id) {
        $category = $this->get($id);
        $parent = $this->get($category->parent_id);
        if ($parent) {
            $category->parent_name = $parent->name;
            $category->parent_slug = $parent->uri;
//            $category->uri = 'store/category/'.$parent->slug.'/'.$category->slug;
        } else {
            $category->parent_name = '';
            $category->parent_slug = '';
//            $category->uri = 'store/category/'.$category->slug;
        }


        return $category;
    }

    public function get_category_node($node_id, $type = 'array', $source = array()) {
        $all_links = !empty($source) ? $source : $this->db
                        ->get($this->_table)
                        ->result_array();
        $links = array();
        $node_segments = array();

        foreach ($all_links AS $row) {
            $links[$row['id']] = $row;
            $links[$row['id']]['last'] = false;
        }

        $links[$node_id]['last'] = true;

        while ($links[$node_id]['parent_id'] > 0) {
            array_unshift($node_segments, $links[$node_id]);
            $node_id = $links[$node_id]['parent_id'];
            if (!$links[$node_id]['parent_id']) {
                break;
            }
        }
        array_unshift($node_segments, $links[$node_id]);

        $path = '';
        foreach ($node_segments as &$segment) {
            $path .= $segment['slug'] . '/';
            $segment['node'] = $path;
        }

        return $type == 'string' ? $path : $node_segments;
    }

    public function get_link_tree($params = array()) {
        if (!empty($params['order'])) {
            $this->db->order_by($params['order']);
        } else {
            $this->db->order_by('order');
        }

        if (!empty($params['parent'])) {
            $this->db->where('parent_id', $params['parent']);
        }

        if (isset($params['front_end']) AND $params['front_end']) {
            $front_end = TRUE;
        } else {
            $front_end = FALSE;
        }
        
        $all_links = $this->db
                ->get($this->_table)
                ->result_array();
        
        $this->load->helper('url');
        $links = array();
        
        // we must reindex the array first and build urls
        $all_links = $this->make_url_array($all_links);
        foreach ($all_links AS $row) {
            $links[$row['id']] = $row;
        }
        unset($all_links);
        $link_array = array();
        // build a multidimensional array of parent > children
        foreach ($links AS $row) {
            if (array_key_exists($row['parent_id'], $links)) {
                // add this link to the children array of the parent link
                $links[$row['parent_id']]['children'][] = & $links[$row['id']];
            }
            if (!isset($links[$row['id']]['children'])) {
                $links[$row['id']]['children'] = array();
            }
            // this is a root link
            if ($row['parent_id'] == 0) {
                $link_array[] = & $links[$row['id']];
            }
        }
        return $link_array;
    }

    /**
     * Set the parent > child relations and child order
     *
     * @author Jerel Unruh - PyroCMS Dev Team
     * @param array $link
     * @return void
     */
    public function _set_children($link) {
        if (isset($link['children'])) {
            foreach ($link['children'] as $i => $child) {
                $child_id =  str_replace('link_', '', $child['id']);
                $this->db->where('id', $child_id);
                $this->db->update($this->_table, array('parent_id' => str_replace('link_', '', $link['id']), 'order' => $i));
                $this->build_lookup($child_id);
                //repeat as long as there are children
                if (isset($child['children'])) {
                    $this->_set_children($child);
                }
            }
        }
    }

    public function make_url_array($links) {
        foreach ($links as $key => &$row) {
            $row['link'] = site_url('store/category/' . $row['uri']);
            unset($row['slug']);
        }
        return $links;
    }

    public function build_parents() {
        $categories = $this->categories_m->where('parent_id', 0)->get_all();
        $result = array(0 => lang('global:select-none'));
        foreach ($categories as &$item) {
            $result[$item->id] = $item->name;
        }
        return $result;
    }

    public function build_tree_array($params) {
        $params = array_merge(array(
            'tree' => array(),
            'parent_id' => 0,
            'current_parent' => 0,
            'current_id' => 0,
            'level' => 0
                ), $params);
        extract($params);
        if (!$tree) {
            if ($pages = $this->db->order_by('order')->select('id, parent_id, name')->get('store_categories')->result()) {
                foreach ($pages as $page) {
                    $tree[$page->parent_id][] = $page;
                }
            }
        }
        if (!isset($tree[$parent_id])) {
            return;
        }
        $items = array();
        foreach ($tree[$parent_id] as $item) {
            if ($current_id == $item->id) {
                continue;
            }

            $prefix = '';
            if ($level > 0) {
                for ($i = 0; $i < ($level * 2); $i++) {
                    $prefix .= '&nbsp;';
                }
                $prefix .= '-&nbsp;';
            }
            $items[$item->id] = $prefix . $item->name;
            $items = $items + (array) $this->build_tree_array(array(
                        'tree' => $tree,
                        'parent_id' => (int) $item->id,
                        'current_parent' => $current_parent,
                        'current_id' => $current_id,
                        'level' => $level + 1
                    ));
        }
        return $items;
    }

    public function build_tree_select($params) {
        $params = array_merge(array(
            'tree' => array(),
            'parent_id' => 0,
            'current_parent' => 0,
            'current_id' => 0,
            'level' => 0
                ), $params);
        extract($params);
        if (!$tree) {
            if ($pages = $this->db->order_by('order')->select('id, parent_id, name')->get('store_categories')->result()) {
                foreach ($pages as $page) {
                    $tree[$page->parent_id][] = $page;
                }
            }
        }
        if (!isset($tree[$parent_id])) {
            return;
        }
        $html = '';
        foreach ($tree[$parent_id] as $item) {
            if ($current_id == $item->id) {
                continue;
            }
            $html .= '<option value="' . $item->id . '"';
            $html .= $current_parent == $item->id ? ' selected="selected">' : '>';
            if ($level > 0) {
                for ($i = 0; $i < ($level * 2); $i++) {
                    $html .= '&nbsp;';
                }
                $html .= '-&nbsp;';
            }
            $html .= $item->name . '</option>';
            $html .= $this->build_tree_select(array(
                'tree' => $tree,
                'parent_id' => (int) $item->id,
                'current_parent' => $current_parent,
                'current_id' => $current_id,
                'level' => $level + 1
                    ));
        }
        return $html;
    }

}