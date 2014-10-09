<ul>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Account</label>
        <div class="input">
            <?php echo form_input('options[account]', set_value('options[account]', $options['account'])); ?>
        </div>
    </li>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Mode</label>
        <div class="input">
            <?php echo form_dropdown('options[mode]', array('sandbox' => 'Sandbox', 'secure' => 'Secure'), set_value('options[mode]', $options['mode'])); ?>
        </div>
    </li>
</ul>