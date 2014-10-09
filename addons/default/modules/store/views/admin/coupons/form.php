	
<section class="title">
    <?php if (isset($id) AND $id > 0): ?>
        <h4><?php echo lang('store:edit').' "'.$name.'"'; ?></h4>
    <?php else: ?>
        <h4><?php echo lang('store:create'); ?></h4>
    <?php endif; ?>
</section>


<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>
<?php echo form_hidden('status', 1); ?>
<section class="item form_inputs">
    <fieldset>
        <legend>Settings</legend>
        <ul>
            <li class="<?php echo alternator('even', ''); ?>">
                <label for="name"><?php echo lang('store:name'); ?> <span>*</span></label>
                <div class="input">
                    <?php echo form_input('name', set_value('name', $name), 'id="name" '); ?>
                </div>
            </li>
            <li class="<?php echo alternator('even', ''); ?>">
                <label for="code"><?php echo lang('store:code'); ?> <span>*</span></label>
                <div class="input">
                    <?php echo form_input('code', set_value('code', $code ? $code : random_string()), 'id="code" '); ?>
                </div>
            </li>
            
            <li class="<?php echo alternator('even', ''); ?>">
                <label for="type"><?php echo lang('store:reduction_type'); ?> <span>*</span></label>
                <div class="input">
                    <?php echo form_dropdown('type',array(
                        '' => lang('global:select-any'),
                        'percent' => lang('store:reduction_percent'),
                        'fixed' => lang('store:reduction_fixed'),
                        ), set_value('type', $type), 'id="reduction_type" autocomplete="off"'); ?>
                </div>
            </li>
            
            <li class="<?php echo alternator('even', ''); ?>">
                <label for="amount"><?php echo lang('store:value'); ?> <span>*</span></label>
                <div class="input">
                    <?php echo form_input('amount', set_value('amount', $amount), 'id="amount" '); ?><span id="suffix">%</span>
                </div>
            </li>
        </ul>
    </fieldset>
    <fieldset>
        <legend>Limits</legend>
        <ul>
            <li class="<?php echo alternator('even', ''); ?>">
                <label for="uses_limit"><?php echo lang('store:uses_limit'); ?> </label>
                <div class="input">
                    <?php echo form_input('uses_limit', set_value('uses_limit', $uses_limit ? $uses_limit : 100)); ?>
                </div>
            </li>
            <li class="<?php echo alternator('even', ''); ?>">
                <label for="uses_order"><?php echo lang('store:uses_order'); ?></label>
                <div class="input">
                    <?php echo form_input('uses_order', set_value('uses_order', $uses_order ? $uses_order : 1)); ?>
                </div>
            </li>
            <li class="<?php echo alternator('even', ''); ?>">
                <label for="uses_user"><?php echo lang('store:uses_user'); ?></label>
                <div class="input">
                    <?php echo form_input('uses_user', set_value('uses_user', $uses_user ? $uses_user : 5)); ?>
                </div>
            </li>
            <li class="<?php echo alternator('even', ''); ?>">
                <label for="date_start"><?php echo lang('store:date_start'); ?></label>
                <div class="input">
                    <?php echo form_input('date_start', set_value('date_start', $date_start ? date('Y-m-d', $date_start) : date('Y-m-d')), 'class="datepicker"'); ?>
                </div>
            </li>
            <li class="<?php echo alternator('even', ''); ?>">
                <label for="date_end"><?php echo lang('store:date_end'); ?></label>
                <div class="input">
                    <?php echo form_input('date_end', set_value('date_end', $date_end ? date('Y-m-d', $date_end) : date('Y-m-d', time() + (3600*24*7) )), 'class="datepicker"'); ?>
                </div>
            </li>
        </ul>
    </fieldset>

    <div class="buttons">
        <?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel'))); ?>
    </div>
</section>

<?php echo form_close(); ?>
<script>
   $(document).ready(function() {
       $('.datepicker').datepicker({
           dateFormat: 'yy-mm-dd'
       });
       $('#reduction_type').change(function(e) {
           if($(this).val() == 'percent') {
               $('#suffix').text('%');
           } else {
               $('#suffix').html('<?php echo Settings::get('currency'); ?>');
           }
           console.log($(this).val());
       });
    });
    
</script>