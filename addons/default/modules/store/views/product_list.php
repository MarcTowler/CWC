
    <?php if (count($items) == 0): ?>
        <p><?php lang('store:no_items'); ?></p>
    <?php else: ?>
        <ul class="store-products unstyled">
            <?php foreach ($items as $item): ?>
                <li>
                    <a href="{{ url:site }}store/product/<?php echo $item->slug; ?>"><img src="{{ url:site }}files/thumb/<?php echo $item->cover_id; ?>/150/150/" /></a>
                    <p>
                        <b><a href="{{ url:site }}store/product/<?php echo $item->slug; ?>"><?php echo $item->name; ?></a></b><br />
                        <?php echo lang('store:price'); ?>: 
                        <?php if (isset($item->sale_type)): ?>
                            <span class="<?php echo $item->sale_type; ?>">
                                <?php echo $item->sale_type == 'reduce' ? '-'.$item->reduction.'%' : lang('store:option_'.$item->sale_type); ?>
                            </span>
                        <?php if ($item->price_tax != $item->new_price_tax): ?>
                            <span style="text-decoration: line-through"><?php echo $item->price_tax; ?></span> <?php echo $item->new_price_tax; ?> 
                        <?php endif; ?>
                        <?php else: ?>
                            <?php echo $item->price_tax; ?>
                        <?php endif; ?>
                        {{ settings:currency }}<br />
                        <a class="add-to-wishlist" href="{{ url:site }}store/customer/add_to_wishlist/<?php echo $item->id; ?>"><?php echo lang('store:add_to_wishlist'); ?></a><br />
                        <a class="add-item" href="{{ url:site }}store/cart/add/<?php echo $item->id; ?>" ><?php echo lang('store:add_to_cart'); ?></a>
                    </p>
                </li>
            <?php endforeach; ?>
        </ul>
        {{ pagination:links }}
    <?php endif; ?>