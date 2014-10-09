<?php

if (!function_exists('get_option')) {

    function get_option($product_id, $name) {

        $this->db->where('name', $name);

        $result = $this->db->where('product_id', $product_id)->get('store_options')->row_array();

        $option = array('option_name' => '', 'option_values' => '');
        if (!empty($result)) {
            $option['option_name'] = $result['name'];
            $values = unserialize($result['values']);
            $option['option_values'] = is_array($values) ? implode(', ', $values) : '';
        }
        return implode(', ', $values);
    }
}

if (!function_exists('prepare_option')) {

    function prepare_option($item) {

        extract($item);
        $form = '';
        switch ($field_type) {
            case 'checkbox':
                foreach ($values as $value) {
                    $form .= form_checkbox('options[' . $slug . '][]', $value['value'], FALSE, 
                        'id="option_'.$slug.'_'.$value['value'].'" class="option opt-checkbox" data-price="'.$value['price'].'"').
                    form_label($value['label'], 'option_'.$slug.'_'.$value['value']);
                }
                return $form;
                break;
            case 'radio':
                foreach ($values as $value) {
                    $form .= form_radio('options[' . $slug . ']', $value['value'], FALSE, 
                        'id="option_'.$slug.'_'.$value['value'].'" class="option opt-radio" data-price="'.$value['price'].'"').
                    form_label($value['label'], 'option_'.$slug.'_'.$value['value']);
                }
                return $form;
                break;
            case 'select':
            default:
                $form = '<select name="options['.$slug.']" id="option_'.$slug.'" class="option opt-select">';
                foreach ($values as $value) {
                    $form .= '<option value="'.$value['value'].'" data-price="'.$value['price'].'">'.$value['label'].'</option>';
                };
                $form .= '</select>';
                return $form;
                break;
        }
    }

}

if (!function_exists('get_product_cover')) {

    function get_product_cover($product_id, $w = 100, $h = 100) {
        $ci = & get_instance();
        
        $product = $ci->db->select('cover_id')->where('id', $product_id)->limit(1)->get('store_products')->row();

        if ($product) {
            return img(site_url('files/thumb/'.$product->cover_id.'/'.$w.'/'.$h));
        } else {
            return '';
        }
    }
}

if (!function_exists('prepare_categories')) {

    function prepare_categories($nodes, $separator = ':') {
        $links = array();
        foreach ($nodes as $item) {
            $links[] = anchor('store/category/'.$item['slug'], $item['name']);
        }
        
        return implode($separator, $links);
    }
}

if (!function_exists('prepare_attributes')) {

    
    function prepare_attributes($array) {
        foreach ($array as $key => $link) {
            if ($link['label'] == '' OR $link['value'] == '') {
                unset($array[$key]);
            }
        }
        
        if (empty($array)) {
            return '';
        } else {
            return json_encode($array);
        }
    }
}
if (!function_exists('format_bytes')) {
    
    function format_bytes($bytes, $precision = 2) { 
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

        $bytes = max($bytes, 0); 
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 

        // Uncomment one of the following alternatives
        //$bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow)); 

        return round($bytes/1024, $precision); 
    } 
}
if (!function_exists('format_price')) {
    
    function format_price($value) {
        return sprintf('%01.2f', $value);
    }
}

if (!function_exists('load_subview')) {
    
    function load_subview($name, $data = array(), $module = null) {
        $ci = &get_instance();
        $module OR $module = $ci->module;
        $ext = pathinfo($name, PATHINFO_EXTENSION);
        $name = ($ext == '') ? $name.'.php' : $name;        
        
        $theme_path = FCPATH.$ci->template->get_views_path().'modules/'.$module.'/';
        if (file_exists($theme_path.$name)) {
            $view = $ci->load->file($theme_path.$name, $data, TRUE);
        } else {
            $view = $ci->load->view($name, $data, TRUE);
        }
        
        return $view;
    }
}

if (!function_exists('array_column')) {

    /**
     * Returns the values from a single column of the input array, identified by
     * the $columnKey.
     *
     * Optionally, you may provide an $indexKey to index the values in the returned
     * array by the values from the $indexKey column in the input array.
     *
     * @param array $input A multi-dimensional array (record set) from which to pull
     *                     a column of values.
     * @param mixed $columnKey The column of values to return. This value may be the
     *                         integer key of the column you wish to retrieve, or it
     *                         may be the string key name for an associative array.
     * @param mixed $indexKey (Optional.) The column to use as the index/keys for
     *                        the returned array. This value may be the integer key
     *                        of the column, or it may be the string key name.
     * @return array
     */
    function array_column($input = null, $columnKey = null, $indexKey = null)
    {
        // Using func_get_args() in order to check for proper number of
        // parameters and trigger errors exactly as the built-in array_column()
        // does in PHP 5.5.
        $argc = func_num_args();
        $params = func_get_args();

        if ($argc < 2) {
            trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
            return null;
        }

        if (!is_array($params[0])) {
            trigger_error('array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given', E_USER_WARNING);
            return null;
        }

        if (!is_int($params[1])
            && !is_float($params[1])
            && !is_string($params[1])
            && $params[1] !== null
            && !(is_object($params[1]) && method_exists($params[1], '__toString'))
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        if (isset($params[2])
            && !is_int($params[2])
            && !is_float($params[2])
            && !is_string($params[2])
            && !(is_object($params[2]) && method_exists($params[2], '__toString'))
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        $paramsInput = $params[0];
        $paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;

        $paramsIndexKey = null;
        if (isset($params[2])) {
            if (is_float($params[2]) || is_int($params[2])) {
                $paramsIndexKey = (int) $params[2];
            } else {
                $paramsIndexKey = (string) $params[2];
            }
        }

        $resultArray = array();

        foreach ($paramsInput as $row) {

            $key = $value = null;
            $keySet = $valueSet = false;

            if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
                $keySet = true;
                $key = (string) $row[$paramsIndexKey];
            }

            if ($paramsColumnKey === null) {
                $valueSet = true;
                $value = $row;
            } elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
                $valueSet = true;
                $value = $row[$paramsColumnKey];
            }

            if ($valueSet) {
                if ($keySet) {
                    $resultArray[$key] = $value;
                } else {
                    $resultArray[] = $value;
                }
            }

        }

        return $resultArray;
    }

}