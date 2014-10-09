
<section class="title">
	<h4><?php echo lang('store:products'); ?></h4>
</section>

<section class="item">
<div class="content">
<?php if ($products) : ?>

<?php echo $this->load->view('admin/products/filters'); ?>

<?php echo form_open('admin/store/products/delete'); ?>
	<div id="filter-stage">
		<?php echo $this->load->view('admin/products/table'); ?>
	</div>
<?php echo form_close(); ?>

<?php else : ?>
		<div class="no_data"><?php echo lang('store:no_items'); ?></div>
<?php endif; ?>
</div>
</section>
