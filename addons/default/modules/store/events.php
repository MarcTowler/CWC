<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Email Template Events Class
 *
 * @author      Stephen Cozart
 * @author		PyroCMS Dev Team
 * @package		PyroCMS\Core\Modules\Templates
 */
class Events_Store {

    protected $fallbacks = array();

    public function __construct() {

        //register the email event
        Events::register('public_controller', array($this, 'load_cart'));
        Events::register('post_user_register', array($this, 'continue_checkout'));

        Events::register('order_placed', array($this, 'order_notification'));
        Events::register('payment_callback', array($this, 'transaction_notification'));
    }

    public function load_cart($data = array()) {

        class_exists('Basket') OR $this->load->library('store/basket', '', 'cart');
    }

    public function continue_checkout($id) {

        if ($this->session->userdata('checkout_proccess')) {
            $this->session->set_userdata('user_id', $id);
            $this->session->set_flashdata('success', lang('account_creation_successful'));
            redirect('store/checkout/address');
        }
    }

    public function order_notification($order_id) {

        $this->load->model('orders_m');
        $this->load->model('gateways_m');
        $this->load->model('shipments_m');
        $this->load->model('address_m');

        $order = $this->orders_m->get($order_id);

        $payment = $this->gateways_m->get($order->payment_method_id);
        $shipping = $this->shipments_m->get($order->shipment_method_id);
        $deliver_to = $this->address_m->get($order->delivery_address_id);
        $bill_to = $this->address_m->get($order->billing_address_id);

        $user = $order->user_id > 0  ? $this->db->select('email, username')->from('users')->where('id', $order->user_id)->get()->row() : NULL;
        $contents = $this->orders_m->get_contents($order_id);

        $admin_notification['slug'] = 'store_new_order';
        $admin_notification['order_id'] = $order_id;
        $admin_notification['date_created'] = date('Y/m/d');
        $admin_notification['payment_method'] = $payment->name;
        $admin_notification['shipping_method'] = $shipping->name;
        $admin_notification['email'] = $user ? $user->email : $bill_to->email;
        $admin_notification['phone'] = $bill_to->phone;
        $admin_notification['sender_ip'] = $this->input->ip_address();
        $admin_notification['shipment_address'] = $deliver_to->zip . ' ' . $deliver_to->city . ', ' . $deliver_to->address1;
        $admin_notification['payment_address'] = $bill_to->zip . ' ' . $bill_to->city . ', ' . $bill_to->address1;

        $admin_notification['sender_ip'] = $this->input->ip_address();

        $admin_notification['total_amount'] = ($order->total + $order->shipping);
        $admin_notification['total_items'] = count($contents);

        Events::trigger('email', $admin_notification, 'array');


        $user_notification = $admin_notification;

        $user_notification['slug'] = 'store_made_order';
        $user_notification['to'] = $user ? $user->email : $bill_to->email;
        $user_notification['user_exists'] = $user ? true : false;
       
        foreach ($contents as &$item) {
            $item->price = $item->price_per;
            $item->subtotal = $item->qty * $item->price_per;
            $item->link = anchor('store/product/'.$item->slug,$item->name);
            //$user_notification['order_contents'] .= '<li><a href="{{ url:site }}store/product/' . $item->id . '">' . $item->title . '</a></li>';
        }
        $user_notification['order_contents'] = $contents;

        Events::trigger('email', $user_notification, 'array');
    }

    public function transaction_notification($transaction) {

        if ($transaction['status'] == 'accepted') {
            $this->load->model('orders_m');
            $this->load->model('address_m');
            $order = $this->orders_m->get($transaction['order_id']);
            if(!$order) return;
            
            $this->db->insert('store_order_history', array(
                'status' => 'processed',
                'notify' => 0,
                'order_id' => $order->id,
                'comment' => "Order has been paid",
                'date_added' => time()
            ));
            
            $bill_to = $this->address_m->get($order->billing_address_id);
            $user = $order->user_id > 0 ? $this->db->select('email, username')->from('users')->where('id', $order->user_id)->get()->row() : false;


            $user_notification['slug'] = 'store_transaction';
            $user_notification['to'] = $user ? $user->email : $bill_to->email;
            $user_notification['order_id'] = $order->id;
            $user_notification['date_created'] = date('Y/m/d', $order->order_date);
            $user_notification['total_amount'] = ($order->total + $order->shipping) . ' ' . Settings::get('currency');
            $user_notification['total_items'] = $this->db->where('order_id', $order->id)->from('store_order_contents')->count_all_results();

            Events::trigger('email', $user_notification, 'array');
        }
    }

    public function __get($var) {
        if (isset(get_instance()->$var)) {
            return get_instance()->$var;
        }
    }

}

/* End of file events.php */