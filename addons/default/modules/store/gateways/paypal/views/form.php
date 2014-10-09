<ul>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Sandbox Mode</label>
        <div class="input">
            <?php echo form_dropdown('options[service]', array('live' => 'No', 'sandbox' => 'Yes'), set_value('options[service]', $options['service'])); ?>
        </div>
    </li>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Paypal Account</label>
        <div class="input">
            <?php echo form_input('options[account]', set_value('options[account]', $options['account'])); ?>
        </div>
    </li>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Image<small>URL to you want to display in paypal</small></label>
        <div class="input">
            <?php echo form_input('options[image]', set_value('options[image]', $options['image'])); ?>
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