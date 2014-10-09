<h2 id="page_title"><?php echo lang('store:customer_title'); ?></h2>

<div class="store-container">
    <ul>
        <li><?php echo anchor('store/customer/orders', lang('store:orders_title')); ?></li>
        <li><?php echo anchor('users/edit', lang('store:profile_title')); ?></li>
        <li><?php echo anchor('store/customer/addresses', lang('store:addresses_title')); ?></li>
        <li><?php echo anchor('store/customer/wishlist', lang('store:wishlist_title')); ?></li>
    </ul>
    <p>
        <a href="{{ url:site }}store"><?php echo lang('store:back_to_store'); ?></a>
    </p>
</div>
