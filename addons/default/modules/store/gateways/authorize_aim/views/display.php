<h2 id="page_title">Authorize.NET</h2>

<?php echo form_open('store/payments/callback/authorize_aim?redirect=1', 'id="authorize"'); ?>
<ul>
    <li>
        <label>Card Number: </label> 
        <div class="input">                
            <input type="text" size="20" maxlength="16" autocomplete="off" class="card-number" />
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
                '14' => '2014',
                '15' => '2015',
                '16' => '2016',
                '17' => '2017',
                '18' => '2018',
                '19' => '2019',
                '20' => '2020',
                '21' => '2021',
                '22' => '2022',
                '23' => '2023',
                '24' => '2024',
                '25' => '2025',
                    ), '', 'class="card-expiry-year" autocomplete="off"');
            ?>
        </div>
    </li>
    <li>
        <button type="submit" class="submit-button">Submit Payment</button> 
    </li>
</ul>

    <?php echo form_hidden('order_id', $order->id); ?>
    <?php echo form_hidden('amount', $order->total + $order->shipping); ?>
    <?php echo form_close(); ?>  
<script>
    $(document).ready(function() {
        $('#authorize').submit(function(event) {
            event.preventDefault();

            $('.submit-button').attr("disabled", "disabled");
            $.post('<?php echo site_url("store/payments/callback/authorize_aim") ?>', 
                {
                    order_id: $('input[name=order_id]').val(),
                    amount: $('input[name=amount]').val(),
                    card_num: $('.card-number').val(),
                    exp_date: $('.card-expiry-month').val()+'/'+$('.card-expiry-year').val(),
                },
                function(data) {
                    console.log(data);
                    $('.submit-button').prop("disabled", false);
                    $('.submit-button').removeAttr("disabled");
                    if (data == 1) {
                        window.location = "<?php echo site_url("store/payments/success") ?>";
                    } else {
                        window.location = "<?php echo site_url("store/payments/cancel") ?>";
                    }
                });

            return false; 
        });
    });
</script>