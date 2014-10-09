<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Import_m extends MY_Model {

    public $add_categories = TRUE;
    public $erase_existing = FALSE;
    public $force_ids = FALSE;
    public $folder_id = 0;

    public function __construct() {
        $this->_table = "";
        parent::__construct();
    }

    public function csv_to_array($filename = '', $delimiter = ',', $enclosure = '"', $escape = '\\', $has_header = TRUE) {
        if (!file_exists($filename) || !is_readable($filename))
            return FALSE;

        $header = NULL;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 1024, $delimiter, $enclosure, $escape)) !== FALSE) {
                $data[] = $row;
            }
            fclose($handle);
        }
        return $data;
    }

    public function get_folders() {
        class_exists('file_folders_m') OR $this->load->model('files/file_folders_m');

        $folders = array(0 => lang('global:select-pick'));
        $tree = $this->file_folders_m->order_by('parent_id', 'ASC')->order_by('id', 'ASC')->get_all();
        foreach ($tree as $folder) {
            $id = $folder->id;
            if ($folder->parent_id != 0) {
                $folders[$id] = $folders[$folder->parent_id] . ' &raquo; ' . $folder->name;
            } else {
                $folders[$id] = $folder->name;
            }
        };
        return $folders;
    }

    public function get_fields() {
        $items = array(
            '' => lang('global:select-any'),
            'name' => lang('store:name'),
            'slug' => lang('store:slug'),
            'category' => lang('store:category'),
            'sku' => lang('store:sku'),
            'desc' => lang('store:desc'),
            'short' => lang('store:short'),
            'price' => lang('store:price_notax'),
            'price_tax' => lang('store:price_tax'),
            'image' => lang('store:image'),
            'status' => lang('store:status'),
            'stock' => lang('store:stock'),
        );

        return form_dropdown('fields[]', $items, array(), 'class="skip"');
    }

    public function array_for_table($content) {

        foreach ($content as $key => &$value) {
            array_unshift($value, form_checkbox('ignore[]', $key));
        }

        return $content;
    }

    public function update_values($fields, $data, $options = array()) {

        class_exists('products_m') or $this->load->model('products_m');

        if (isset($options['add_categories'])) {
            $this->add_categories = (bool) $options['add_categories'];
        }
        if (isset($options['erase_existing'])) {
            $this->erase_existing = (bool) $options['erase_existing'];
            if ($this->erase_existing) {
                $this->db->truncate('store_products');
                $this->db->truncate('store_categories');
                $this->db->truncate('store_sales');
                $this->db->truncate('store_related');
                $this->db->truncate('store_product_options');
                $this->db->truncate('store_images');
            }
        }
        if (isset($options['force_ids'])) {
            $this->force_ids = (bool) $options['force_ids'];
        }
        
        if (isset($options['folder'])) {
            $this->folder_id = $options['folder'];
        }

        $items = array();

        foreach ($data as $item) {
            $row_fields = $fields;
            foreach ($fields as $key => $value) {
                if ($value == '') {
                    unset($item[$key]);
                    unset($row_fields[$key]);
                }
            }
            $test = $this->insert_data(array_combine($row_fields, $item));
            if (!$test)
                return FALSE;
        }

        return TRUE;
    }

    private function insert_data($item) {
        $keys = array(
            'name' => '',
            'sku' => '',
            'slug' => '',
            'short' => '',
            'category_id' => '',
            'description' => '',
            'price' => '',
            'price_tax' => '',
            'cover_id' => '',
            'keywords' => '',
            'attributes' => '',
            'status' => '',
            'created' => '',
            'updated' => '',
        );

        if (isset($item['id']) && $this->force_ids) {
            $keys['id'] = '';
            $item['id'] = intval($item['id']);
        }

        if (isset($item['name']) && !isset($item['slug'])) {
            $item['slug'] = slugify($item['name']);
        }

        if (!isset($item['short']))
            $item['short'] = '';
        if (!isset($item['description']))
            $item['description'] = '';
        if (isset($item['desc'])) {
            $item['description'] = $item['desc'];
            unset($item['desc']);
        }
        if (!isset($item['created']))
            $item['created'] = date('Y-m-d H:i:s', time());
        if (!isset($item['updated']))
            $item['updated'] = date('Y-m-d H:i:s', time());
        if (!isset($item['keywords']))
            $item['keywords'] = '';
        if (!isset($item['sku']))
            $item['sku'] = '';
        if (!isset($item['attributes']))
            $item['attributes'] = '';
        if (!isset($item['status']))
            $item['status'] = 'in_stock';
        if (!isset($item['cover_id']))
            $item['cover_id'] = '';
        if (!isset($item['category_id']))
            $item['category_id'] = 0;

        if (isset($item['category']) && is_numeric($item['category'])) {
            $cat = $this->db->get_where('store_categories', array('id' => $item['category']), 1)->row();
            $item['category_id'] = $cat ? $item['category'] : 0;
        } elseif (isset($item['category']) && is_string($item['category'])) {
            $cat = $this->db->get_where('store_categories', array('name' => $item['category']), 1)->row();

            if ($cat) {
                $item['category_id'] = $cat->id;
            } elseif ($this->add_categories) {
                $new_category = array(
                    'name' => $item['category'],
                    'slug' => slugify($item['category']),
                    'uri' => slugify($item['category']),
                    'description' => '',
                    'parent_id' => 0,
                    'image_id' => 0,
                    'order' => 0,
                    'status' => 1,
                );
                $this->db->insert('store_categories', $new_category);
                $item['category_id'] = $this->db->insert_id();
            } else {
                $item['category_id'] = 0;
            }
        }

        if (isset($item['image'])) {
            if (preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $item['image'])) {
                $item['cover_id'] = $this->save_image($item['image']);
            } elseif (strlen($item['image']) == 15 && $this->db->get_where('files', array('id' => $item['image']), 1)->row()) {
                $item['cover_id'] = $item['image'];
            }
        }


        $item['price'] = format_price($item['price']);
        $item['price_tax'] = format_price($item['price_tax']);

        $item = array_intersect_key($item, $keys);


        $result = $this->db->insert('store_products', $item);

        return $result ? $this->db->insert_id() : FALSE;
    }

    public function save_image($source) {
        $parts = parse_url($source);
        $file = basename($parts['path']);
        $finfo = explode('.', $file);
        $ext = $finfo[1];

        $filename = md5($finfo[0] . random_string());
        $path = UPLOAD_PATH . 'files/';
        $upload_file = $path . $filename . '.' . $ext;

        if (function_exists('curl_version')) {
            $ch = curl_init($source);
            $fp = fopen($upload_file, 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
        } elseif (ini_get('allow_url_fopen')) {
            file_put_contents($upload_file, file_get_contents($source));
        } else {
            log_message('error', 'Cannot import image, neighter cURL nor allow_url_fopen is not enabled');
            return '';
        }
        
        class_exists('file_m') OR $this->load->model('files/file_m');
        $imagedata = getimagesize($upload_file);
        
        $data = array(
            'id' => substr(md5(microtime()+$filename), 0, 15),
            'folder_id' => $this->folder_id,
            'user_id' => (int) ci()->current_user->id,
            'type' => 'i',
            'name' => $file,
            'path' => '{{ url:site }}files/large/' . $filename . '.' . $ext,
            'description' => '',
            'alt_attribute' => '',
            'filename' => $filename . '.' . $ext,
            'extension' => '.' .$ext,
            'mimetype' => $imagedata['mime'],
            'filesize' => filesize($upload_file),
            'width' => (int) $imagedata[0],
            'height' => (int) $imagedata[1],
            'date_added' => now()
        );
        $this->file_m->insert($data);
        return $data['id'];
    }

}