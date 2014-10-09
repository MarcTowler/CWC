<ul>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Account Number</label>
        <div class="input">
            <?php echo form_input('options[id]', set_value('options[id]', $options['id'])); ?>
        </div>
    </li>
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Gateway language</label>
        <div class="input">
            <?php echo form_dropdown('options[lang]', array('pl' => 'pl', 'en' => 'en', 'de' => 'de'), set_value('options[lang]', $options['lang'])); ?>
        </div>
    </li>
</ul>