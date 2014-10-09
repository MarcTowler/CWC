<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Show RSS feeds in your site
 * 
 * @package 	PyroCMS\Core\Modules\Blog\Widgets
 * @author		Phil Sturgeon
 * @author		PyroCMS Development Team
 */
class Widget_Store_categories extends Widgets {

    public $title = array(
        'en' => 'Store Categories',
    );
    public $description = array(
        'en' => 'Display a list of categories in your store',
    );
    public $author = 'Błażej Adamczyk';
    public $website = 'http://sein.com.pl/';
    public $version = '1.0';
    public $fields = array(
        array(
            'field' => 'order',
            'label' => 'Order by',
            'rules' => 'required'
        )
    );

    public function run($options) {
        $this->load->model('store/categories_m');
        $this->lang->load('store/store');

        $categories = $this->categories_m->get_link_tree($options);
        //$current = $this->module == 'store' ? $this->uri->segment(3) : '';

        return array(
            'store_categories' => $categories,
            'build_categories' => $this->_build_links($categories, false)
        );
    }

    private function _build_links($links = array(), $return_arr = true) {
        static $current_link = false;
        static $level = 0;

        $top = false;
        $separator = '';
        //deprecated
        $link_class = '';
        //deprecated
        $more_class = 'has_children';
        $current_class = 'current';
        $first_class = 'first';
        $last_class = 'last';
        $output = $return_arr ? array() : '';
        $wrap = '';
        $i = 1;
        $total = sizeof($links);

        if (!$return_arr) {
            $tag = 'li';
            //deprecated
            $list_tag = 'ul';
            $indent = false;


            if ($indent) {
                $ident_a = repeater($indent, $level);
                $ident_b = $ident_a . $indent;
                $ident_c = $ident_b . $indent;
            }
        }

        foreach ($links as $link) {
            $item = array();
            $wrapper = array();
            $link['url'] = site_url('store/category/'.$link['uri']);

            // attributes of anchor
            $item['url'] = $link['url'];
            $item['title'] = $link['name'];
            if ($wrap) {
                $item['title'] = '<' . $wrap . '>' . $item['title'] . '</' . $wrap . '>';
            }

            $item['attributes']['class'] = $link_class ? 'class="' . $link_class . '"' : '';

            // attributes of anchor wrapper
            $wrapper['children'] = $return_arr ? array() : null;
            $wrapper['separator'] = $separator;
            $wrapper['class'] = array();

            // is single ?
            if ($total === 1) {
                $wrapper['class'][] = 'single';
            }

            // is first ?
            elseif ($i === 1) {
                $wrapper['class'][] = $first_class;
            }

            // is last ?
            elseif ($i === $total) {
                $wrapper['class'][] = $last_class;
                $wrapper['separator'] = '';
            }

            // has children ? build children
            if ($link['children']) {
                ++$level;
                $wrapper['class'][] = $more_class;
                $wrapper['children'] = $this->_build_links($link['children'], $return_arr);
                --$level;
            }

            // is this the link to the page that we're on?
            if (preg_match('@^' . current_url() . '/?$@', $link['url'])) {
                $current_link = $link['url'];
                $wrapper['class'][] = $current_class;
            }

            // is the link we're currently working with found inside the children html?
            if (!in_array($current_class, $wrapper['class']) AND
                    isset($wrapper['children']) AND
                    $current_link AND
                    ((is_array($wrapper['children']) AND in_array($current_link, $wrapper['children'])) OR
                    (is_string($wrapper['children']) AND strpos($wrapper['children'], $current_link)))) {
                // that means that this link is a parent
                $wrapper['class'][] = 'has_' . $current_class;
            }
            ++$i;

            if ($return_arr) {
                $item['target'] = & $item['attributes']['target'];
                $item['class'] = & $item['attributes']['class'];
                $item['children'] = $wrapper['children'];

                if ($wrapper['class'] && $item['class']) {
                    $item['class'] = implode(' ', $wrapper['class']) . ' ' . substr($item['class'], 7, -1);
                } elseif ($wrapper['class']) {
                    $item['class'] = implode(' ', $wrapper['class']);
                }

                if ($item['target']) {
                    $item['target'] = substr($item['target'], 8, -1);
                }

                // assign attributes to level family
                $output[] = $item;
            } else {
                //deprecated
                $add_first_tag = FALSE;

                // render and indent or only render inline?
                if ($indent) {
                    // remove all empty values so we don't have an empty class attribute
                    $classes = implode(' ', array_filter($wrapper['class']));

                    $output .= $add_first_tag ? "<{$list_tag}>" . PHP_EOL : '';
                    $output .= $ident_b . '<' . $tag . ($classes > '' ? ' class="' . $classes . '">' : '>') . PHP_EOL;
                    $output .= $ident_c . ((($level == 0) AND $top == 'text' AND $wrapper['children']) ? $item['title'] : anchor($item['url'], $item['title'], trim(implode(' ', $item['attributes'])))) . PHP_EOL;

                    if ($wrapper['children']) {
                        $output .= $ident_c . "<{$list_tag}>" . PHP_EOL;
                        $output .= $ident_c . $indent . str_replace(PHP_EOL, (PHP_EOL . $indent), trim($ident_c . $wrapper['children'])) . PHP_EOL;
                        $output .= $ident_c . "</{$list_tag}>" . PHP_EOL;
                    }

                    $output .= $wrapper['separator'] ? $ident_c . $wrapper['separator'] . PHP_EOL : '';
                    $output .= $ident_b . "</{$tag}>" . PHP_EOL;
                    $output .= $add_first_tag ? $ident_a . "</{$list_tag}>" . PHP_EOL : '';
                } else {
                    // remove all empty values so we don't have an empty class attribute
                    $classes = implode(' ', array_filter($wrapper['class']));

                    $output .= $add_first_tag ? "<{$list_tag}>" : '';
                    $output .= '<' . $tag . ($classes > '' ? ' class="' . $classes . '">' : '>');
                    $output .= (($level == 0) AND $top == 'text' AND $wrapper['children']) ? $item['title'] : anchor($item['url'], $item['title'], trim(implode(' ', $item['attributes'])));

                    if ($wrapper['children']) {
                        $output .= "<{$list_tag}>";
                        $output .= $wrapper['children'];
                        $output .= "</{$list_tag}>";
                    }

                    $output .= $wrapper['separator'];
                    $output .= "</{$tag}>";
                    $output .= $add_first_tag ? "</{$list_tag}>" : '';
                }
            }
        }

        return $output;
    }

}
