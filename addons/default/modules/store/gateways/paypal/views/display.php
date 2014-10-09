<h2 id="page_title">Paypal</h2>

<div class="store-container">
    <form id="paypal" action="<?php echo $options['action']; ?>" method="post">  
        <!-- SETTINGS -->
        <input type="hidden" name="cmd" value="_cart">  
        <input type="hidden" name="business" value="<?php echo $options['account']; ?>">
        
        <?php if ($options['image']): ?>
        <input type="hidden" name="image_url" value="<?php echo $options['image']; ?>">  
        <?php endif; ?>

        <input type="hidden" name="no_shipping" value="0">  
        <input type="hidden" name="currency_code" value="<?php echo Settings::get('store_currency_code'); ?>">  
        <!-- <input type="hidden" name="lc" value="PL">   -->
        <input type="hidden" name="bn" value="PP-BuyNowBF">  
        <input type="hidden" name="rm" value="2" />
        <input type="hidden" name="no_note" value="1" />
        <input type="hidden" name="charset" value="utf-8" />
        <input type="hidden" value="1" name="upload">

        <!-- ITEMS -->

        <input type="hidden" name="item_name" value="Payment From {{ settings:site_name }}">  
        <input type="hidden" name="item_number" value="<?php echo $order->id; ?>">  
        <input type="hidden" name="amount" value="<?php echo $order->total; ?>">  
        <?php $count = 1; ?>
        <?php foreach ($items as $item) : ?>
            <input type="hidden" name="item_name_<?php echo $count; ?>" value="<?php echo $item->name; ?>" />
            <input type="hidden" name="item_number_<?php echo $count; ?>" value="<?php echo $item->product_id; ?>" />
            <input type="hidden" name="amount_<?php echo $count; ?>" value="<?php echo $item->price_per; ?>" />
            <input type="hidden" name="quantity_<?php echo $count; ?>" value="<?php echo $item->qty; ?>" />
            <input type="hidden" name="weight_<?php echo $count; ?>" value="0" />
            <?php $count++; ?>
        <?php endforeach; ?>


        <input type="hidden" name="item_name_<?php echo $count; ?>" value="Shipping, Handling, Discounts & Taxes" />
        <input type="hidden" name="item_number_<?php echo $count; ?>" value="" />
        <input type="hidden" name="amount_<?php echo $count; ?>" value="<?php echo $order->shipping; ?>" />
        <input type="hidden" name="quantity_<?php echo $count; ?>" value="1" />
        <input type="hidden" name="weight_<?php echo $count; ?>" value="0" />

        <!-- CUSTOMER -->
        <input type="hidden" name="first_name" value="<?php echo $billing->first_name; ?>" />
        <input type="hidden" name="last_name" value="<?php echo $billing->last_name; ?>" />
        <input type="hidden" name="address1" value="<?php echo $billing->address1; ?>" />
        <input type="hidden" name="address2" value="<?php echo $billing->address2; ?>" />
        <input type="hidden" name="city" value="<?php echo $billing->city; ?>" />
        <input type="hidden" name="zip" value="<?php echo $billing->zip; ?>" />
        <!-- <input type="hidden" name="country" value="PL" /> -->
        <input type="hidden" name="address_override" value="0" />
        <input type="hidden" name="email" value="<?php echo $billing->email; ?>" />
        <input type="hidden" name="invoice" value="<?php echo $order->id; ?>" />

        <input type="hidden" name="return" value="{{ url:site }}store/payments/success/paypal/">
        <input type="hidden" name="notify_url" value="{{ url:site }}store/payments/callback/paypal/" />
        <input type="hidden" name="cancel_return" value="{{ url:site }}store/payments/cancel" />

        <input type="hidden" name="custom" value="<?php echo $order->id; ?>" />
        <input type="hidden" value="sale" name="paymentaction">
<!--        <input type="hidden" value="authorization" name="paymentaction">-->


        <input type="submit" value="Pay With Paypal"  />   
    </form>  
</div>
<?php if ($options['auto'] == 1): ?>
    <script type="text/javascript">
        (function($) {
            $(document).ready(function() {
                $('#paypal').submit();
            });
        })(jQuery);
    </script>
<?php endif; ?>