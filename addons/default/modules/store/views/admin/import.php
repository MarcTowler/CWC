<section class="title">
    <h4>Import Data</h4>
</section>

<section class="item">
    <div class="content">
        <?php echo form_open_multipart(); ?>
        <fieldset>
            <legend><?php echo lang('store:settings'); ?></legend>
            <ul>
                <li>
                    <label>Field Delimiter</label>
                    <div class="input">
                        <?php echo form_input('delimiter', ','); ?>
                    </div>
                </li> 
                <li>
                    <label>Enclosure</label>
                    <div class="input">
                        <?php echo form_input('enclosure', '"'); ?>
                    </div>
                </li> 
                <li>
                    <label>Escape</label>
                    <div class="input">
                        <?php echo form_input('escape', '\\'); ?>
                    </div>
                </li> 
                <li>
                    <label>Options</label>
                    <div class="input">
                        <label><?php echo form_checkbox('no_headers', 1); ?>File does not contain headers</label><br />
                        <label><?php echo form_checkbox('options[add_categories]', 1); ?>Create non existing categories</label><br />
                        <label><?php echo form_checkbox('options[erase_existing]', 1); ?>Erase existing products and categories</label><br />
                        <label><?php echo form_checkbox('options[force_ids]', 1); ?>Force product id's</label>
                    </div>
                </li> 
                <li>
                    <label>Folder for images</label>
                    <div class="input">
                        <?php echo form_dropdown('options[folder]', $folders); ?>
                    </div>
                </li> 
                <li>
                    <label>File</label>
                    <div class="input">
                        <?php echo form_upload('userfile'); ?>
                    </div>
                </li> 
            </ul>
        </fieldset>
        <div class="buttons">
            <?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel'))); ?>
        </div>
        <?php echo form_close(); ?>

        <pre>
            <?php print_r($content); ?>
        </pre>
    </div>
</section>
