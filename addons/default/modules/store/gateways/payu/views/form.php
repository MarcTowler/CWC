<ul>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Sandbox Mode</label>
        <div class="input">
            <?php echo form_dropdown('options[service]', array('live' => 'No', 'sandbox' => 'Yes'), set_value('options[service]', $options['service'])); ?>
        </div>
    </li>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>POS ID</label>
        <div class="input">
            <?php echo form_input('options[pos_id]', set_value('options[pos_id]', $options['pos_id'])); ?>
        </div>
    </li>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>POS AUTH</label>
        <div class="input">
            <?php echo form_input('options[pos_auth_key]', set_value('options[pos_auth_key]', $options['pos_auth_key'])); ?>
        </div>
    </li>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Klucz #1</label>
        <div class="input">
            <?php echo form_input('options[key1]', set_value('options[key1]', $options['key1'])); ?>
        </div>
    </li>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Klucz #2</label>
        <div class="input">
            <?php echo form_input('options[key2]', set_value('options[key2]', $options['key2'])); ?>
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