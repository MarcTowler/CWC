<section class="title">
    <h4>Import Data</h4>
</section>

<section class="item">
    <div class="content">
        <?php echo form_open_multipart(site_url('admin/store/data/assign')); ?>
        
        <div style="overflow: auto; max-width: 100%;">
            <?php echo isset($table) ? $table : ''; ?>
        </div>
        <div class="buttons">
            <?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel'))); ?>
        </div>
        <?php echo form_close(); ?>

    </div>
</section>
