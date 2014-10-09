<section class="title">
    <!-- We'll use $this->method to switch between store.create & store.edit -->
    <h4><?php echo lang('store:' . $this->method); ?></h4>
</section>

<section class="item">
    <fieldset>
        <?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>

        <div class="form_inputs">

            <ul>
                <li class="<?php echo alternator('', 'even'); ?>">
                    <label for="title"><?php echo lang('store:name'); ?> <span>*</span></label>
                    <div class="input"><?php echo form_input('title', set_value('title', $title), 'class="width-15"'); ?></div>
                </li>
                <li class="<?php echo alternator('', 'even'); ?>">
                    <label for="slug"><?php echo lang('store:slug'); ?> <span>*</span></label>
                    <div class="input"><?php echo form_input('slug', set_value('slug', $slug), 'class="width-15"'); ?></div>
                </li>
                <li class="<?php echo alternator('', 'even'); ?>">
                    <label for="field_type"><?php echo lang('store:field_type'); ?> <span>*</span></label>
                    <div class="input"><?php echo form_dropdown('field_type', array('select' => 'Dropdown', 'radio' => "Radio", 'checkbox' => 'Checkbox'), set_value('field_type', $field_type), 'class="width-15"'); ?></div>
                </li>
                <li class="<?php echo alternator('', 'even'); ?> values-list">
                    <label for="values"><?php echo lang('store:values'); ?> <span>*</span></label>
                    <div class="input">
                        <?php
                        if (!empty($values)) {
                            foreach ($values as $value) {
                                echo '<div class="value-item">';
                                echo '<span class="value-label">' . $value->label . ' - ' . $value->value . '</span>';
                                echo '<input type="hidden" class="label" name="values[label][]" value="' . htmlspecialchars($value->label) . '" />';
                                echo '<input type="hidden" class="value" name="values[value][]" value="' . htmlspecialchars($value->value) . '" />';
                                echo '<span class="icon-edit"></span>';
                                echo '<span class="icon-remove"></span>';
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </li>
                <li class="<?php echo alternator('', 'even'); ?>">
                    <label for="new_value"><?php echo lang('store:new_value'); ?> <span>*</span></label>
                    <div class="input">
                        <?php echo form_input('new_label', '', 'class="width-15 values" id="new_label" placeholder="' . lang('store:label') . '"'); ?>
                        <?php echo form_input('new_value', '', 'class="width-5 values" id="new_value" placeholder="' . lang('store:value') . '"'); ?>
                        <?php echo anchor('#', lang('global:add'), 'class="btn green" id="option-add"'); ?>
                    </div>
                </li>
            </ul>

        </div>

        <div class="buttons">
            <?php $this->load->view('admin/partials/buttons', array('buttons' => array('save'))); ?>
            <?php echo anchor('admin/store/options/', lang('buttons:cancel'), 'class="btn gray cancel"'); ?>
        </div>

        <?php echo form_close(); ?>
    </fieldset>

</section>