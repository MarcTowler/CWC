<ul>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Account Number <small>include IBAN for interational</small></label>
        <div class="input">
            <?php echo form_input('options[account]', set_value('options[account]', $options['account'])); ?>
        </div>
    </li>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Bank BIC ID <small>for interational</small></label>
        <div class="input">
            <?php echo form_input('options[bic]', set_value('options[bic]', $options['bic'])); ?>
        </div>
    </li>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Receiver</label>
        <div class="input">
            <?php echo form_input('options[receiver]', set_value('options[receiver]', $options['receiver'])); ?>
        </div>
    </li>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Address</label>
        <div class="input">
            <?php echo form_textarea('options[address]', set_value('options[address]', $options['address'])); ?>
        </div>
    </li>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Send Email?</label>
        <div class="input">
            <label><?php echo form_radio('options[email]', 0, set_radio('options[email]', 0, FALSE)); ?>No</label>
            <label><?php echo form_radio('options[email]', 1, set_radio('options[email]', 1, TRUE)); ?>Yes</label>
        </div>
    </li>
    
</ul>