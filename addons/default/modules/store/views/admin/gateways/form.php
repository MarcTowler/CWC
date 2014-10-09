<section class="title">
    <!-- We'll use $this->method to switch between store.create & store.edit -->
    <h4><?php echo $gateway->name; ?></h4>
</section>

<section class="item">
    <div class="content">
        <?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>
        <?php echo form_hidden('id', $gateway->id); ?>

        <div class="form_inputs">

            <fieldset>
                <legend>Gateway</legend>
                <ul>
                    <li class="<?php echo alternator('', 'even'); ?>">
                        <label for="name"><?php echo lang('store:name'); ?> <span>*</span></label>
                        <div class="input"><?php echo form_input('name', set_value('name', $gateway->name), 'class="width-15"'); ?></div>
                    </li>

                    <li class="<?php echo alternator('', 'even'); ?>">
                        <label for="desc"><?php echo lang('store:desc'); ?> <span>*</span></label>
                        <div class="input"><?php echo form_textarea('desc', set_value('desc', $gateway->desc), 'class="width-15"'); ?></div>
                    </li>
                    <li class="<?php echo alternator('', 'even'); ?>">
                        <label for="enabled"><?php echo lang('store:status'); ?> <span>*</span></label>
                        <div class="input">
                            <div class="onoffswitch">
                                <?php echo form_dropdown('enabled', array('0' => lang('store:status_disabled'), '1' => lang('store:status_active')), set_value('enabled', $gateway->enabled)); ?>
                            </div> 
                        </div>
                    </li>
                </ul>
            </fieldset>
            <fieldset>
                <legend>Options</legend>
                <?php $this->load->file($gateway->form); ?>
            </fieldset>

        </div>

        <div class="buttons">
            <?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel'))); ?>
        </div>

        <?php echo form_close(); ?>
    </div>
</section>