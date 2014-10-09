<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Orders_m extends MY_Model {

    public function __construct() {
        parent::__construct();

        $this->_table = 'store_orders';
    }
    
    public function get($id) {
        
        $this->db->select('store_orders.*, ci_sessions.ip_address, ci_sessions.user_agent, ci_sessions.last_activity');
        $this->db->select('store_shipments.name as shipment_method_name, store_shipments.url as shipment_method_url, store_gateways.name as payment_method_name');
        $this->db->join('ci_sessions', 'store_orders.session_id = ci_sessions.session_id', 'left');
        $this->db->join('store_shipments', 'store_orders.shipment_method_id = store_shipments.id', 'left');
        $this->db->join('store_gateways', 'store_orders.payment_method_id = store_gateways.id', 'left');
        
        $order = parent::get_by('store_orders.id', $id);
        if ($order->shipment_method_url && $order->shipping_code) {
            $order->tracking_url = $this->parser->parse_string($order->shipment_method_url, array('code' => $order->shipping_code), true);
        }
        
        return $order;
    }


    public function get_all() {
        $this->db->select('store_orders.*, inv.email as customer_email');
        $this->db->select('trans.amount as payment, trans.status as payment_status, history.status as status', FALSE);
        $this->db->select('CONCAT(inv.first_name, " ", inv.last_name) as customer_name', FALSE);
        $this->db->select('CONCAT(ship.zip, ", ", ship.city, ", ", ship.address1, " ", ship.address2) AS shipping_address', FALSE);
        $this->db->select('CONCAT(inv.zip, ", ", inv.city, ", ", inv.address1, " ", inv.address2) AS billing_address', FALSE);
        $this->db->join('(SELECT `status`, `amount`, `order_id` FROM `'.$this->db->dbprefix('store_transactions').'` ORDER BY `timestamp` DESC) trans', $this->db->dbprefix('store_orders').'.id = trans.`order_id`', 'left', FALSE);
        $this->db->join('(SELECT `status`, `date_added`, `order_id` FROM `'.$this->db->dbprefix('store_order_history').'` ORDER BY `date_added` DESC) history', $this->db->dbprefix('store_orders').'.id = history.`order_id`', 'left', FALSE);
        $this->db->join('store_addresses ship', 'store_orders.delivery_address_id = ship.id', 'left');
        $this->db->join('store_addresses inv', 'store_orders.billing_address_id = inv.id', 'left');
        $this->db->group_by('store_orders.id');
        return parent::get_all();
    }
    
    public function get_address($id) {
        return $this->db->where('id', $id)->get('store_addresses')->row();
    }
    
    public function get_contents($id) {
        $items = $this->db
                        ->select('store_products.name, store_products.slug, store_products.cover_id, store_order_contents.*')
                        ->join('store_products', 'store_order_contents.product_id = store_products.id', 'left')
                        ->where('order_id', $id)->get('store_order_contents')->result();
        foreach ($items as &$item) {
            $item->options = unserialize($item->options);
        }
        return $items;
    }

    public function customer_comment($id, $content) {
        $data = array(
            'status' => 'reply',
            'notify' => 0,
            'order_id' => $id,
            'comment' => strip_tags($content),
            'date_added' => time()
            );
        return $this->db->insert('store_order_history', $data);
    }
    
    public function update_history($input) {
        $data = array(
            'status' => $input['status'],
            'notify' => $input['notify'],
            'order_id' => $input['order_id'],
            'comment' => strip_tags($input['comment']),
            'date_added' => time()
            );
        
        if ($input['notify']) {
            $order = $this->get($input['order_id']);
            $billing = $this->get_address($order->billing_address_id);

            $user = $this->db->select('email, username')->from('users')->where('id', $order->user_id)->get()->row();
            
            
            $user_notification['slug'] = 'store_notify';
            $user_notification['order_id'] = $input['order_id'];
            $user_notification['status'] = $input['status'];
            $user_notification['comment'] = $input['comment'];
            $user_notification['to'] = $user ? $user->email : $billing->email;
            
            Events::trigger('email', $user_notification, 'array');
        }
        return $this->db->insert('store_order_history', $data);
    }
    
    public function delete($id) {
        $this->db->delete('store_order_contents', array('order_id' => $id)); 
        return parent::delete($id);
    }
    
    public function delete_many($primary_values) {
        $this->db->where_in('order_id', $primary_values);
        $this->db->delete('store_order_contents'); 
        parent::delete_many($primary_values);
    }

}
