<ul>
    <li class="<?php echo alternator('even', 'odd') ?>">
    <li class="<?php echo alternator('even', 'odd') ?>">
        <label>Api Key</label>
        <div class="input">
            <?php echo form_input('options[api]', set_value('options[api]', $options['api'])); ?>
        </div>
    </li>
</ul>