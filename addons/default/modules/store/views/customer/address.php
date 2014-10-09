<h2 id="page_title"><?php echo lang('store:address_title'); ?></h2>
<?php
if (validation_errors()) {
    echo validation_errors('<div class="error">', '</div>');
}
?>
<div class="address_info">

    <?php echo form_open(); ?>
    <?php echo form_hidden('user_id', $this->current_user->id); ?>
    <fieldset>
        <ul class="two_column">
            <li class="<?php echo alternator('odd', 'even'); ?>">
                <label><?php echo lang('store:first_name_field'); ?><span>*</span></label>
                <div class="input">
                    <?php echo form_input('first_name', set_value('first_name', $first_name)); ?>
                </div>
            </li>
            <li class="<?php echo alternator('odd', 'even'); ?>">
                <label><?php echo lang('store:last_name_field'); ?><span>*</span></label>
                <div class="input">
                    <?php echo form_input('last_name', set_value('last_name', $last_name)); ?>
                </div>
            </li>
            <li class="<?php echo alternator('odd', 'even'); ?>">
                <label><?php echo lang('store:company_field'); ?></label>
                <div class="input">
                    <?php echo form_input('company', set_value('company', $company)); ?>
                </div>
            </li>
            <li class="<?php echo alternator('odd', 'even'); ?>">
                <label><?php echo lang('store:nip_field'); ?></label>
                <div class="input">
                    <?php echo form_input('nip', set_value('nip', $nip)); ?>
                </div>
            </li>
            <li class="<?php echo alternator('odd', 'even'); ?>">
                <label><?php echo lang('store:email_field'); ?><span>*</span></label>
                <div class="input">
                    <?php echo form_input('email', set_value('email', $email)); ?>
                </div>
            </li>
            <li class="<?php echo alternator('odd', 'even'); ?>">
                <label><?php echo lang('store:phone_field'); ?><span>*</span></label>
                <div class="input">
                    <?php echo form_input('phone', set_value('phone', $phone)); ?>
                </div>
            </li>
            <li class="<?php echo alternator('odd', 'even'); ?>">
                <label><?php echo lang('store:address1_field'); ?><span>*</span></label>
                <div class="input">
                    <?php echo form_input('address1', set_value('address1', $address1)); ?>
                </div>
            </li>
            <li class="<?php echo alternator('odd', 'even'); ?>">
                <label><?php echo lang('store:address2_field'); ?></label>
                <div class="input">
                    <?php echo form_input('address2', set_value('address2', $address2)); ?>
                </div>
            </li>
            <li class="<?php echo alternator('odd', 'even'); ?>">
                <label><?php echo lang('store:city_field'); ?><span>*</span></label>
                <div class="input">
                    <?php echo form_input('city', set_value('city', $city)); ?>
                </div>
            </li>
            <li class="<?php echo alternator('odd', 'even'); ?>">
                <label><?php echo lang('store:state_field'); ?></label>
                <div class="input">
                    <?php echo form_input('state', set_value('state', $state)); ?>
                </div>
            </li>
            <li class="<?php echo alternator('odd', 'even'); ?>">
                <label><?php echo lang('store:country_field'); ?></label>
                <div class="input">
                    <?php echo form_input('country', set_value('country', $country)); ?>
                </div>
            </li>
            <li class="<?php echo alternator('odd', 'even'); ?>">
                <label><?php echo lang('store:zip_field'); ?><span>*</span></label>
                <div class="input">
                    <?php echo form_input('zip', set_value('zip', $zip)); ?>
                </div>
            </li>
            <li> 
                <label><?php echo form_checkbox('agreement', 1, FALSE); ?><?php echo lang('checkout:agreement'); ?></label>
                <span style="float: right;">
                    <?php echo anchor('store/customer/', lang('store:back_to_customer')); ?> | 
                    <?php echo form_reset('reset', lang('checkout:reset'), 'class="button"'); ?>
                    <?php echo form_submit('submit', lang('store:save')); ?>
                </span>
            </li>
        </ul>
    </fieldset>
</div>
<?php echo form_close(); ?>