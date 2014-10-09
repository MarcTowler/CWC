<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Gateway_Klarna extends PaymentGateway {

    public $title = 'Klarna GateWay';
    public $description = 'Process Payments via Klarna';
    public $author = 'Błażej Adamczyk';
    public $website = 'http://sein.com.pl/';
    public $version = '1.0';
    public $fields = array(
        array(
            'field' => 'options[mode]',
            'label' => 'Mode',
            'rules' => 'trim|max_length[100]|required'
        ),
        array(
            'field' => 'options[eid]',
            'label' => 'EID',
            'rules' => 'trim|max_length[100]|numeric|required'
        ),
        array(
            'field' => 'options[secret]',
            'label' => 'Secret',
            'rules' => 'trim|max_length[100]|required'
        ),
        array(
            'field' => 'options[locale]',
            'label' => 'Locale',
            'rules' => 'trim|max_length[2]|required'
        ),
        array(
            'field' => 'options[terms]',
            'label' => 'Terms URL',
            'rules' => 'trim|max_length[255]'
        ),
    );
    private $locale = array(
            'se' => array('country' => 'SE', 'currency' => 'SEK', 'locale' => 'sv-se'),
            'fi' => array('country' => 'FI', 'currency' => 'EUR', 'locale' => 'fi-fi'),
            'no' => array('country' => 'NO', 'currency' => 'NOK', 'locale' => 'nb-no'),
            'de' => array('country' => 'DE', 'currency' => 'EUR', 'locale' => 'de-de'),
        );

    public function __construct() {
        parent::__construct();
    }

    public function form() {

        if (!isset($this->options['locale'])) $this->options['locale'] = 'de';
        if (!isset($this->options['mode'])) $this->options['mode'] = '';
        if (!isset($this->options['secret'])) $this->options['secret'] = '';
        if (!isset($this->options['eid'])) $this->options['eid'] = '';
        if (!isset($this->options['terms'])) $this->options['terms'] = '';

        //return $options;
    }

    public function run($options, $data) {
        if (!function_exists('curl_version')){
            log_message('error', "Klarna Gateway: cURL is not available");
            $options['error'] = "Gateway does not meet the requirements, please contact administrator.";
            return $options;
        }


        require_once 'libs/Klarna/Checkout.php';

        $eid = (string) $options['eid'];
        $sharedSecret = $options['secret'];

        if ($options['mode'] == 'sandbox') {
            $eid = '200';
            $sharedSecret = 'test';
        }
        
        $cart = array();
        $tax_applied = Settings::get('store_tax_value') * 0.01;
        
        foreach ($data->items as $cart_item) {
            $cart[] = array(
                'reference' => $cart_item->id,
                'name' => $cart_item->name,
                'quantity' => intval($cart_item->qty),
                'unit_price' => $cart_item->price_per * 100,
                'discount_rate' => 0,
                'tax_rate' => ($tax_applied * $cart_item->price_per) * 100
            );
        }
        
        $cart[] = array(
                'type' => 'shipping_fee',
                'reference' => 'SHIPPING',
                'name' => 'Shipping Fee',
                'quantity' => 1,
                'unit_price' => $data->order->shipping * 100,
                'tax_rate' => 0
            );

        
        Klarna_Checkout_Order::$baseUri = $options['mode'] == 'sandbox' ? 'https://checkout.testdrive.klarna.com/checkout/orders' : 'https://checkout.klarna.com/checkout/orders' ;
        Klarna_Checkout_Order::$contentType = "application/vnd.klarna.checkout.aggregated-order-v2+json";
        
        //empty($_SESSION) AND session_start();
        
        $connector = Klarna_Checkout_Connector::create($sharedSecret);
        $order = new Klarna_Checkout_Order($connector);
        /*
        if (array_key_exists('klarna_checkout', $_SESSION)) {
            // Resume session
            $order = new Klarna_Checkout_Order(
                    $connector, $_SESSION['klarna_checkout']
            );
            try {
                $order->fetch();
                // Reset cart
                $update['cart']['items'] = array();
                foreach ($cart as $item) {
                    $update['cart']['items'][] = $item;
                }
                $order->update($update);
            } catch (Exception $e) {
                // Reset session
                $order = null;
                unset($_SESSION['klarna_checkout']);
            }
        }*/
        //if ($order == null) {
            // Start new session
        
        
        $create['purchase_country'] = $this->locale[$options['locale']]['country'];
        $create['purchase_currency'] =  $this->locale[$options['locale']]['currency'];
        $create['locale'] =  $this->locale[$options['locale']]['locale'];
        $create['merchant']['id'] = $eid;
        $create['merchant']['terms_uri'] = !empty($options['terms']) ? $options['terms'] : site_url('');
        $create['merchant']['checkout_uri'] = site_url('store/checkout');

        $create['merchant']['confirmation_uri']
                = site_url('store/payments/callback/klarna') .
                '?sid='.$data->order->id.'&klarna_order={checkout.order.uri}';
        // You can not receive push notification on non publicly available uri
        $create['merchant']['push_uri'] = site_url().'push.php' . '?sid='.$data->order->id.'&klarna_order={checkout.order.uri}';
        foreach ($cart as $item) {
            $create['cart']['items'][] = $item;
        }

        /*
            $order = new Klarna_Checkout_Order($connector);
            $order->create($create);
            $order->fetch();
        }
        // Store location of checkout session
        $_SESSION['klarna_checkout'] = $sessionId = $order->getLocation();
        // Display checkout
        $options['snippet'] = $order['gui']['snippet'];

        */
        $order->create($create);
        $order->fetch();
        $options['snippet'] = $order['gui']['snippet'];

        return $options;
    }

    public function callback() { //

        require_once 'libs/Klarna/Checkout.php';

        $sharedSecret = $this->options['secret'];

        if ($this->options['mode'] == 'sandbox') {
            $sharedSecret = 'test';
        }

        Klarna_Checkout_Order::$contentType
            = 'application/vnd.klarna.checkout.aggregated-order-v2+json';

        $orderUri = $this->input->get('klarna_order');
        //$orderUri OR redirect('store');

        $connector = Klarna_Checkout_Connector::create($sharedSecret);
        $order = new Klarna_Checkout_Order($connector, $orderUri);

        $order->fetch();

        $reason = '';
        if ($order['status'] == 'checkout_complete') {
            $status = 'accepted';
        } else if ($order['status'] == 'checkout_incomplete') {
            $status = 'rejected';
        } else {
            $status = 'pending';
        }


        $result = array(
            'order_id' => $this->input->get('sid'),
            'transaction_id' => $order['reference'],
            'status' => $status, // status: accepted, rejected, pending
            'reason' => $reason,
            'amount' => $order['cart']['total_price_including_tax'] * 0.01,
            'gateway' => 'klarna',
        );

        return $result;
    }

}
