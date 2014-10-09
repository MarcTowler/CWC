<div class="notification">
    <h3>Integration info</h3>
    <ol>
        <li>In google merchant control panel go to Settings -> Integration</li>
        <li>Callback URL: {{ url:site }}store/payments/callback/gcheckout </li>
        <li>Use XML notification as Callback contents</li>
        <li>Use 2.0 API version</li>
        <li>Uncheck Digital signed only carts</li>
        <li>In Settings -> Preferences check "Automatically authorize and charge the buyer's credit card."</li>
    </ol>
    <hr />
</div>
<ul>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Sandbox Mode</label>
        <div class="input">
            <?php echo form_dropdown('options[service]', array('live' => 'No', 'sandbox' => 'Yes'), set_value('options[service]', $options['service'])); ?>
        </div>
    </li>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Merchant ID</label>
        <div class="input">
            <?php echo form_input('options[merchant_id]', set_value('options[merchant_id]', $options['merchant_id'])); ?>
        </div>
    </li>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Merchant Key</label>
        <div class="input">
            <?php echo form_input('options[merchant_key]', set_value('options[merchant_key]', $options['merchant_key'])); ?>
        </div>
    </li>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Automatic self submit?<small>Do you want your form to submit itself to paypal</small></label>
        <div class="input">
            <label><?php echo form_radio('options[auto]', 0, set_radio('options[auto]', 0, $options['auto'] == 0)); ?> No </label>
            <label><?php echo form_radio('options[auto]', 1, set_radio('options[auto]', 1, $options['auto'] == 1)); ?> Yes </label>
        </div>
    </li>
</ul>