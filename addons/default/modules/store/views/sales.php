<h2 id="page_title"><?php echo isset($title) ? $title : lang('store:sales'); ?></h2>
<div class="store-subcategories">
    <ul>
        <li><a href="{{ url:site }}store/extras/promotions"><?php echo lang('store:option_promo'); ?></a></li>
        <li><a href="{{ url:site }}store/extras/sales"><?php echo lang('store:option_sale'); ?></a></li>
        <li><a href="{{ url:site }}store/extras/reduces"><?php echo lang('store:option_reduce'); ?></a></li>
        <li><a href="{{ url:site }}store/extras/featured"><?php echo lang('store:option_featured'); ?></a></li>
    </ul>
</div>

<div class="store-container">

    <?php echo load_subview('product_list', array('items' => $items)); ?>

</div>