<ul>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Sandbox Mode</label>
        <div class="input">
            <?php echo form_dropdown('options[service]', array('live' => 'No', 'sandbox' => 'Yes'), set_value('options[service]', $options['service'])); ?>
        </div>
    </li>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Publishable Key</label>
        <div class="input">
            <?php echo form_input('options[api]', set_value('options[api]', $options['api'])); ?>
        </div>
    </li>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Secret Key</label>
        <div class="input">
            <?php echo form_input('options[secret]', set_value('options[secret]', $options['secret'])); ?>
        </div>
    </li>
</ul>