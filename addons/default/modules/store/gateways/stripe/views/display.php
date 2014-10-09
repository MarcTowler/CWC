<h2 id="page_title">Stripe</h2>

<span class="payment-errors error"></span>
<div class="store-container">
    <?php echo form_open('store/payments/callback/stripe', 'id="stripe"'); ?>
    <ul>
        <li>
            <label>Card Number: </label> 
            <div class="input">                
                <input type="text" size="20" autocomplete="off" class="card-number" />
            </div>
        </li>
        <li>
            <label>CVC: </label> 
            <div class="input">                
                <input type="text" size="4" autocomplete="off" class="card-cvc" />
            </div>
        </li>
        <li><label>Expiry date: </label> 
            <div class="input">
                <?php
                echo form_dropdown('exp_month', array(
                    '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06',
                    '07' => '07', '08' => '08', '09' => '09', '10' => '10', '11' => '11', '12' => '12',
                        ), '', 'class="card-expiry-month" autocomplete="off"');
                ?>
                <?php
                echo form_dropdown('exp_year', array(
                    '2014' => '2014',
                    '2015' => '2015',
                    '2016' => '2016',
                    '2017' => '2017',
                    '2018' => '2018',
                    '2019' => '2019',
                    '2020' => '2020',
                    '2021' => '2021',
                    '2022' => '2022',
                    '2023' => '2023',
                    '2024' => '2024',
                    '2025' => '2025',
                        ), '', 'class="card-expiry-year" autocomplete="off"');
                ?>
            </div>
        </li>
    </ul>

    <?php echo form_hidden('order_id', $order->id); ?>
    <?php echo form_hidden('reference', $billing->email); ?>
<?php echo form_hidden('currency', Settings::get('store_currency_code')); ?>
    <?php echo form_hidden('amount', $order->total + $order->shipping); ?>

    <button type="submit" class="submit-button">Submit Payment</button> 
<?php echo form_close(); ?>  
</div>
<script type="text/javascript" src="https://js.stripe.com/v1/"></script>
<script type="text/javascript">
    // this identifies your website in the createToken call below
    Stripe.setPublishableKey('<?php echo $options['api'] ?>');

    function stripeResponseHandler(status, response) {
        if (response.error) {
            // re-enable the submit button
            $('.submit-button').prop("disabled", false);
            $('.submit-button').removeAttr("disabled");
            // show the errors on the form
            $(".payment-errors").html(response.error.message);
        } else {
            var form$ = $("#stripe");
            // token contains id, last4, and card type
            var token = response['id'];
            // insert the token into the form so it gets submitted to the server
            form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");
            // and submit
            form$.get(0).submit();
        }
    }

    $(document).ready(function() {
        $("#stripe").submit(function(event) {
            // disable the submit button to prevent repeated clicks
            $('.submit-button').attr("disabled", "disabled");
            // createToken returns immediately - the supplied callback submits the form if there are no errors
            Stripe.createToken({
                number: $('.card-number').val(),
                cvc: $('.card-cvc').val(),
                exp_month: $('.card-expiry-month').val(),
                exp_year: $('.card-expiry-year').val()
            }, stripeResponseHandler);
            return false; // submit from callback
        });
    });

</script>