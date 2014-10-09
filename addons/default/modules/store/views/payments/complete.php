<h2 id="page_title"><?php echo lang('store:congrats_title'); ?></h2>

<div class="store-container">
    <p><?php echo lang('store:congrats'); ?></p>

    <?php $this->load->file($gateway->display); ?>
    <p>
        <a href="{{ url:site }}store"><?php echo lang('store:back_to_store'); ?></a>
    </p>
</div>
