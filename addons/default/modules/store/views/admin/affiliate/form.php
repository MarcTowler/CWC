<section class="title">
	<!-- We'll use $this->method to switch between store.create & store.edit -->
	<h4><?php echo lang('store:'.$this->method); ?></h4>
</section>

<section class="item">

	<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>
    <fieldset>
		<div class="form_inputs">
	
		<ul>
			<li class="<?php echo alternator('', 'even'); ?>">
				<label for="name"><?php echo lang('store:name'); ?> <span>*</span></label>
                                <div class="input"><?php echo form_dropdown($type.'_id', $items, set_value($type.'_id', ${$type.'_id'}), 'class="width-15"'); ?></div>
			</li>

			<li class="<?php echo alternator('', 'even'); ?>">
				<label for="slug"><?php echo lang('store:reduction'); ?> <span>*</span></label>
				<div class="input"><?php echo form_input('discount', set_value('discount', $discount), 'class="width-15"'); ?>%</div>
			</li>
		</ul>
		
		</div>
		
		<div class="buttons">
			<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel') )); ?>
		</div>
		</fieldset>
	<?php echo form_close(); ?>

</section>