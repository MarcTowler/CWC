<ul>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Environment</label>
        <div class="input">
            <?php echo form_dropdown('options[env]', array('sandbox' => 'Sandbox', 'live' => 'Live'), set_value('options[env]', $options['env'])); ?>
        </div>
    </li>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Test Mode</label>
        <div class="input">
            <?php echo form_dropdown('options[test]', array('true' => 'True', 'false' => 'False'), set_value('options[test]', $options['test'])); ?>
        </div>
    </li>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Api Login ID</label>
        <div class="input">
            <?php echo form_input('options[id]', set_value('options[id]', $options['id'])); ?>
        </div>
    </li>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Transaction Key</label>
        <div class="input">
            <?php echo form_input('options[key]', set_value('options[key]', $options['key'])); ?>
        </div>
    </li>
</ul>