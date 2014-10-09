<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Stats_m extends MY_Model {
    public $fields = array();


    public function __construct() {
        $this->_table = "";
        parent::__construct();
        
        $this->fields = array(
            array('name' => 'Total Orders', 'function' => 'total_orders'),
            array('name' => 'This Year Orders', 'function' => 'this_year_orders'),
            array('name' => 'This Month Orders', 'function' => 'this_month_orders'),
            array('name' => 'Total Customers', 'function' => 'total_customers'),
            array('name' => 'Items sold', 'function' => 'items_sold'),
            array('name' => 'Total income', 'function' => 'total_income'),
        );
    }
    
    function total_income() {
        $this->db->select_sum('total');
        $income = $this->db->get('store_orders')->row_array();
        return $income['total'];
    }
    
    function total_orders() {
        return $this->db->count_all('store_orders');
    }
    
    function this_year_orders() {
        $this->db->where('order_date >', mktime(0, 0, 0, 1, 1, date('Y')));
        return $this->db->count_all_results('store_orders');
    }
    function this_month_orders() {
        $this->db->where('order_date >', mktime(0, 0, 0, date('n'), 1, date('Y')));
        return $this->db->count_all_results('store_orders');
    }
    
    function total_customers() {
        $this->db->where('group_id !=', 1);
        return $this->db->count_all_results('users');
    }
    
    function items_sold() {
        $this->db->select_sum('qty');
        $sold = $this->db->get('store_order_contents')->row_array();
        
        return $sold['qty'];
    }
    
    function awaiting_orders() {
        $this->db->where('status !=', 'sent');
        return $this->db->count_all_results('store_orders');
    }
    
    public function get_all() {
        foreach ($this->fields as &$item) {
            $item['result'] = call_user_func(array(&$this, $item['function']));
        }
        return $this->fields;
    }
            
    public function get_period($days = 7, $limit = 'all') {
        $stats = array();
        
        $income = $this->_get_income($days);
        $orders = $this->_get_orders($days);
        $sold = $this->_get_items_sold($days);
        
        switch ($limit) {
            case 'income':
                $stats[] = array('label' => 'Income', 'data' => $income);
                break;
            case 'orders':
                $stats[] = array('label' => 'Orders', 'data' => $orders);
                break;
            case 'items':
                $stats[] = array('label' => 'Items sold', 'data' => $sold);
                break;
            case 'all':
            default:
                $stats[] = array('label' => 'Income', 'data' => $income);
                $stats[] = array('label' => 'Orders', 'data' => $orders);
                $stats[] = array('label' => 'Items sold', 'data' => $sold);
                break;
        }
        return $stats;
    }
    
    private function _get_items_sold($days) {
        $dates = array();
        
        $day_seconds = 86400;
        $period = $days * $day_seconds;
        
        $this->db->select('COUNT(*) AS total', FALSE);
        $this->db->select("FROM_UNIXTIME(`order_date`, '%Y-%m-%d') AS date", FALSE);
        $this->db->where('order_date >', time()-$period);
        $this->db->join('store_order_contents', 'store_order_contents.order_id = store_orders.id');
        //$this->db->group_by('store_orders.id');
        $this->db->group_by('date', FALSE);
        $result = $this->db->get('store_orders')->result();
        
        $stats = array();
        
        for ($index = 0; $index < $days; $index++) {
            $timestamp = date('Y-m-d', time() - ($index * $day_seconds));
            $dates[$timestamp] = 0;
        }
        
        foreach ($result as $item) {
            $dates[$item->date] = $item->total;
        }
        $dates = array_reverse($dates);
        
        foreach ($dates as $key => $value) {
            $stats[] = array(strtotime($key)*1000, $value);
        }
        return $stats;
    }
    
    private function _get_orders($days) {
        $dates = array();
        
        $day_seconds = 86400;
        $period = $days * $day_seconds;
        
        $this->db->select('COUNT(*) AS total', FALSE);
        $this->db->select("FROM_UNIXTIME(`order_date`, '%Y-%m-%d') AS date", FALSE);
        $this->db->where('order_date >', time()-$period);
        $this->db->group_by('date', FALSE);
        $result = $this->db->get('store_orders')->result();
        
        $stats = array();
        
        for ($index = 0; $index < $days; $index++) {
            $timestamp = date('Y-m-d', time() - ($index * $day_seconds));
            $dates[$timestamp] = 0;
        }
        
        foreach ($result as $item) {
            $dates[$item->date] = $item->total;
        }
        $dates = array_reverse($dates);
        
        foreach ($dates as $key => $value) {
            $stats[] = array(strtotime($key)*1000, $value);
        }
        return $stats;
    }
    
    private function _get_income($days) {
        $dates = array();
        
        $day_seconds = 86400;
        $period = $days * $day_seconds;
        
        $this->db->select('SUM(`total`) AS total', FALSE);
        $this->db->select("FROM_UNIXTIME(`order_date`, '%Y-%m-%d') AS date", FALSE);
        $this->db->where('order_date >', time()-$period);
        $this->db->group_by('date', FALSE);
        $result = $this->db->get('store_orders')->result();
        
        $income_stats = array();
        
        for ($index = 0; $index < $days; $index++) {
            $timestamp = date('Y-m-d', time() - ($index * $day_seconds));
            $dates[$timestamp] = 0;
        }
        
        foreach ($result as $item) {
            $dates[$item->date] = $item->total;
        }
        $dates = array_reverse($dates);
        
        foreach ($dates as $key => $value) {
            $income_stats[] = array(strtotime($key)*1000, format_price($value));
        }
        return $income_stats;
    }

}