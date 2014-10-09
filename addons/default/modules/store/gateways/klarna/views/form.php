<ul>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Sandbox Mode</label>
        <div class="input">
            <?php echo form_dropdown('options[mode]', array('sandbox' => 'Sandbox', 'live' => 'Live'), set_value('options[mode]', $options['mode'])); ?>
        </div>
    </li>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Locale</label>
        <div class="input">
            <?php echo form_dropdown('options[locale]', array(
            'se' => "Swedish",
            'fi' => "Finnish",
            'no' => 'Norwegian',
            'de' => 'German'
        ), set_value('options[locale]', $options['locale'])); ?>
        </div>
    </li>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>EID</label>
        <div class="input">
            <?php echo form_input('options[eid]', set_value('options[eid]', $options['eid'])); ?>
        </div>
    </li>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Shared Secret</label>
        <div class="input">
            <?php echo form_input('options[secret]', set_value('options[secret]', $options['secret'])); ?>
        </div>
    </li>
</ul>