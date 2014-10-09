<h2 id="page_title">Authorize.NET</h2>

<form method='post' action="<?php echo $options['server']; ?>" id="authorize">
    <input type='hidden' name="x_login" value="<?php echo $options['id']; ?>" />
    <input type='hidden' name="x_fp_hash" value="<?php echo $options['fingerprint']; ?>" />
    <input type='hidden' name="x_amount" value="<?php echo ($order->total + $order->shipping); ?>" />
    <input type='hidden' name="x_fp_timestamp" value="<?php echo time(); ?>" />
    <input type='hidden' name="x_fp_sequence" value="<?php echo $order->id; ?>" />
    <input type='hidden' name="x_version" value="3.1">
    <input type='hidden' name="x_description" value="Order #<?php echo $order->id; ?> from {{ settings:site_name }}">
    <?php if ($options['currency']): ?>
        <input type='hidden' name="x_currency_code" value="<? echo $options['currency']; ?>">
    <?php endif; ?>
    <input type='hidden' name="x_invoice_num" value="<?php echo $order->id; ?>">
    <input type='hidden' name="x_po_num" value="<?php echo $order->id; ?>">
    <input type='hidden' name="x_show_form" value="payment_form">
    <input type='hidden' name="x_method" value="cc">
    <input type='hidden' name="x_test_request" value="<?php echo $options['test']; ?>">
    <input type='hidden' name="x_cancel_url" value="<?php echo site_url('store/payments/cancel'); ?>">

    <input type='hidden' name="x_relay_response" value="TRUE">
    <input type='hidden' name="x_relay_always" value="true">
    <input type='hidden' name="x_relay_url" value="<?php echo site_url('store/payments/callback/authorize_sim').'?order_id='.$order->id; ?>">

    <input type='hidden' name="x_receipt_link_method" value="LINK">
    <input type='hidden' name="x_receipt_link_text" value="Click here to return to our home page">
    <input type='hidden' name="x_receipt_link_URL" value="<?php echo site_url('store/payments/success'); ?>">

    <input type='submit' value="Click here for the secure payment form">
</form>
<?php if ($options['auto'] == 1): ?>
    <script type="text/javascript">
        (function($) {
            $(document).ready(function() {
                $('#authorize').submit();
            });
        })(jQuery);
    </script>
<?php endif; ?>