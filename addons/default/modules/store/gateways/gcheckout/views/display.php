<h2 id="page_title">Pay with your Google Wallet</h2>

<div class="store-container">

    <!-- SETTINGS -->

    <input type="hidden" name="_charset_"/>
    <input type="hidden" name="shopping-cart.merchant-private-data" value="<?php echo $order->id; ?>">
    <?php
    $cart = new GoogleCart($options['merchant_id'], $options['merchant_key'], $options['service'], Settings::get('store_currency_code'));

    foreach ($items as $item) {
        $gitem = new GoogleItem($item->name, '', $item->qty, $item->price_per);
        $gitem->SetMerchantItemId($item->id);

        $cart->AddItem($gitem);
    }
    $shipping = new GoogleFlatRateShipping($shipping_method->name, $order->shipping);
    $filters = new GoogleShippingFilters();
//    $filters->SetAllowedCountryArea('ALL');
    $filters->SetAllowedWorldArea(true);
    $shipping->AddShippingRestrictions($filters);
    $cart->AddShipping($shipping);
//        $shipping = new GoogleItem('Shipping', '', 1, $order->shipping);
//        $cart->AddItem($shipping);
    $cart->SetMerchantPrivateData($order->id);
    $cart->SetEditCartUrl(site_url('store/cart'));

    $cart->SetContinueShoppingUrl(site_url('store/'));

    $cart->SetRequestBuyerPhone(false);

    $cart->AddRoundingPolicy("CEILING", "TOTAL");

    //echo $cart->CheckoutButtonCode();
    ?>
    <form id="google-checkout" action="<?php echo $options['action']; ?>" method="post" accept-charset="utf-8">  
        <?php echo form_hidden('cart', base64_encode($cart->GetXML())); ?>
        <?php echo form_hidden('signature', base64_encode($cart->CalcHmacSha1($cart->GetXML()))); ?>
        
        <input type="submit" value="Pay With Google Checkout"  />   
    </form>  
</div>
<?php if ($options['auto'] == 1): ?>
    <script type="text/javascript">
        (function($) {
            $(document).ready(function() {
                $('#google-checkout').submit();
            });
        })(jQuery);
    </script>
<?php endif; ?>