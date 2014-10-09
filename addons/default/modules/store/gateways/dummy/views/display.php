<h2 id="page_title">Dummy</h2>

<div class="store-container">
    <form id="dummy" action="<?php echo $options['form_url']; ?>" method="post">
        <?php echo form_hidden('account', $options['account']) ?>
        <ul>
            <li><label>Name on Card: </label> 
                <div class="input">
                    <?php echo form_input('card_name'); ?>
                </div>
            </li>
            <li><label>Card Type: </label> 
                <div class="input">
                    <?php echo form_dropdown('card_type', array('visa' => 'Visa', 'mastercard' => 'Master Card')); ?>
                </div>
            </li>

            <li><label>Card No: </label> 
                <div class="input">
                    <?php echo form_input('card_no'); ?>
                </div>
            </li>
            <li><label>CSC: </label> 
                <div class="input">
                    <?php echo form_input('csc'); ?>
                </div>
            </li>
            <li><label>Expiry date: </label> 
                <div class="input">
                    <?php
                    echo form_dropdown('exp_month', array(
                        '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06',
                        '07' => '07', '08' => '08', '09' => '09', '10' => '10', '11' => '11', '12' => '12',
                    ));
                    ?>
                    <?php echo form_input('exp_year','', 'placeholder="2013"'); ?>
                </div>
            </li>
        </ul>

        <?php echo form_hidden('transaction_id', $order->id); ?>
        <?php echo form_hidden('return_url', site_url('store/payments/callback/dummy/'.$order->id)); ?>
        <?php echo form_hidden('reference', 'Test Order'); ?>
        <?php echo form_hidden('currency_code', Settings::get('store_currency_code')); ?>
        <?php echo form_hidden('amount', $order->total + $order->shipping); ?>

        <input type="submit" value="Proccess Card"  />   
    </form>  
</div>