<h2 id="page_title">Pay with your Google Wallet</h2>

<div class="store-container">
    <form id="google-checkout" action="<?php echo $options['action']; ?>" method="post" accept-charset="utf-8">  
        <!-- SETTINGS -->

        <input type="hidden" name="_charset_"/>
        <input type="hidden" name="shopping-cart.merchant-private-data" value="<?php echo $order->id; ?>">


        <!-- ITEMS -->

        <?php $count = 1; ?>
        <?php foreach ($items as $item) : ?>
            <input type="hidden" name="item_name_<?php echo $count; ?>" value="<?php echo $item->name; ?>" />
            <input type="hidden" name="item_description_<?php echo $count; ?>" value="<?php echo $item->product_id; ?>" />
            <input type="hidden" name="item_merchant_id_<?php echo $count; ?>" value="<?php echo $item->product_id; ?>" />
            <input type="hidden" name="item_price_<?php echo $count; ?>" value="<?php echo $item->price_per; ?>" />
            <input type="hidden" name="item_currency_<?php echo $count; ?>" value="<?php echo Settings::get('store_currency_code'); ?>" />
            <input type="hidden" name="item_quantity_<?php echo $count; ?>" value="<?php echo $item->qty; ?>" />
            <?php $count++; ?>
        <?php endforeach; ?>


        <input type="hidden" name="item_name_<?php echo $count; ?>" value="Shipping" />
        <input type="hidden" name="item_description_<?php echo $count; ?>" value="" />
        <input type="hidden" name="item_price_<?php echo $count; ?>" value="<?php echo $order->shipping; ?>" />
        <input type="hidden" name="item_currency_<?php echo $count; ?>" value="<?php echo Settings::get('store_currency_code'); ?>" />
        <input type="hidden" name="item_quantity_<?php echo $count; ?>" value="1" />


        <input type="hidden" name="edit_url" value="{{ url:site }}store/cart/">
        <input type="hidden" name="continue_url" value="{{ url:site }}store/store/">

        <input type="hidden" name="checkout-flow-support.merchant-checkout-flow-support.parameterized-urls.parameterized-url-1.url" value="{{ url:site }}store/payments/callback/gcheckout/"/>
        <input type="hidden" name="checkout-flow-support.merchant-checkout-flow-support.parameterized-urls.parameterized-url-1.parameters.url-parameter-1.name" value="orderID"/>
        <input type="hidden" name="checkout-flow-support.merchant-checkout-flow-support.parameterized-urls.parameterized-url-1.parameters.url-parameter-1.type" value="order-id"/>
        <input type="hidden" name="checkout-flow-support.merchant-checkout-flow-support.parameterized-urls.parameterized-url-1.parameters.url-parameter-2.name" value="totalCost"/>
        <input type="hidden" name="checkout-flow-support.merchant-checkout-flow-support.parameterized-urls.parameterized-url-1.parameters.url-parameter-2.type" value="order-total"/>


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