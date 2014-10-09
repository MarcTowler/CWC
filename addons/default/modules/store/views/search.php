<h2 id="page_title">
  <?php echo $this->method == 'category' ? $title : lang('store:store_title'); ?></h2>
<div class="store-container">    
    <?php echo load_subview('product_list', array('items' => $items)); ?>
</div>