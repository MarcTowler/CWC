<h2 id="page_title">PayU</h2>

<div class="store-container">
        <!-- SETTINGS -->
        <form id="payu_form" action="<?php echo $options['action']; ?>" method="POST" name="payform"> 
            <input type="hidden" name="first_name" value="<?php echo $billing->first_name; ?>">
            <input type="hidden" name="last_name" value="<?php echo $billing->last_name; ?>">
            <input type="hidden" name="email" value="<?php echo $billing->email; ?>">
            <input type="hidden" name="street" value="<?php echo $billing->address1 . $billing->address2; ?>">
            <input type="hidden" name="city" value="<?php echo $billing->city; ?>">
            <input type="hidden" name="post_code" value="<?php echo $billing->zip; ?>">
            <input type="hidden" name="pos_id" value="<?php echo $options['pos_id']; ?>"> 
            <input type="hidden" name="pos_auth_key" value="<?php echo $options['pos_auth_key']; ?>"> 
            <input type="hidden" name="session_id" value="<?php echo $order->id; ?>"> 
            <input type="hidden" name="order_id" value="<?php echo $order->id; ?>"> 
            <input type="hidden" name="ts" value="<?php echo time(); ?>"> 
            <input type="hidden" name="amount" value="<?php echo ($order->total + $order->shipping) * 100; ?>"> 
            <input type="hidden" name="desc" value="Płatność ze strony {{ settings:site_name }}"> 
            <input type="hidden" name="client_ip" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>"> 
            <input type="hidden" name="js" value="0"> 
            <input type="submit" name="send" value="Zapłać poprzez Platnosci.pl"> 
        </form> 
</div>
        <script language="JavaScript" type="text/javascript"> 
            document.forms['payform'].js.value=1;
        </script>
<?php if ($options['auto'] == 1): ?>
    <script type="text/javascript">
        (function($) {
            $(document).ready(function() {
                $('#payu_form').submit();
            });
        })(jQuery);
    </script>
<?php endif; ?>