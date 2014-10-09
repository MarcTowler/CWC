<?php if ($cart): ?>
    <ul class="list-unstyled">
        <?php foreach ($cart as $item): ?>
            <li>
                <?php echo $item['qty'].'x'; ?>
                <a href="<?php echo site_url($item['uri']); ?>">
                    <?php echo $item['name']; ?>
                </a>
                <?php echo ' - '.$item['price']; ?> {{ settings:currency }}
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
<p><?php echo lang('store:no_items');?></p>
<?php endif; ?>
<hr />
<b><?php echo lang('store:total_items');?>: </b> <?php echo $total_items; ?><br />
<b><?php echo lang('store:total_cost');?>: </b> <?php echo $total_cost; ?> {{ settings:currency }}<br />
<a class="show_cart" href="{{ url:site }}store/cart"><?php echo lang('store:show_cart');?></a> | <a class="checkout" href="{{ url:site }}store/checkout"><?php echo lang('store:checkout');?></a>