<fieldset id="filters">

    <legend><?php echo lang('global:filters'); ?></legend>

    <?php echo form_open('admin/store/products/ajax_filter'); ?>

    <?php echo form_hidden('f_module', $module_details['slug']); ?>
    <ul>  
        <li>
            <?php echo lang('store:category', 'f_category'); ?><br />
                <?php echo form_dropdown('f_category', array(0 => lang('global:select-all')) + $categories); ?>
        </li>
        <li>
            <?php echo lang('store:sort_order', 'f_order'); ?><br />
                <?php echo form_dropdown('f_order', array(
                    'name' => lang('store:name'),
                    'category_name' => lang('store:category'),
                    'price' => lang('store:price'),
                    'status' => lang('store:status')
                )); ?>
        </li>
        <li>
            <?php echo lang('store:desc', 'f_keywords'); ?><br />
            <?php echo form_input('f_keywords', '', 'style="height: 20px; padding: 3px 10px;"'); ?>
        </li>
        <li>
            <span style="display: block; margin-top: 17px;">&nbsp;</span>
            <?php echo anchor(current_url() . '#', lang('buttons:cancel'), 'class="cancel"'); ?>
        </li>
    </ul>
<?php echo form_close(); ?>
</fieldset>