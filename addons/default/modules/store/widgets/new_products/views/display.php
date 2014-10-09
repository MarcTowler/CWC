<?php if (count($items)): ?>
    <?php foreach ($items as $item): ?>
        <div class="product-box first last">
            <?php if (isset($item->sale_type)): ?>
                    <span class="ribbon <?php echo $item->sale_type; ?>">
                        <?php echo $item->sale_type == 'reduce' ? '-' . $item->reduction . '%' : lang('store:option_' . $item->sale_type); ?>
                    </span>
            <?php endif; ?>
            <div class="price">
                <?php if (isset($item->sale_type)): ?>
                    <?php if ($item->price != $item->new_price_tax): ?>
                        <?php echo $item->new_price_tax; ?> {{ settings:currency }}<br />
                        <span class="old_price"><?php echo $item->price_tax; ?> {{ settings:currency }}</span> 
                    <?php endif; ?>
                <?php else: ?>
                    <?php echo $item->price_tax; ?> {{ settings:currency }}
                <?php endif; ?>
                
            </div>
            <a href="{{ url:site }}store/product/<?php echo $item->slug; ?>" class="image">
                <img src="{{ url:site }}files/thumb/<?php echo $item->cover_id; ?>/150/150/" />
            </a>

            <div class="product-inner">
                <h3><a href="{{ url:site }}store/product/<?php echo $item->slug; ?>"><?php echo $item->name; ?></a></h3>
                <p>
                    <a href="{{ url:site }}store/product/<?php echo $item->slug; ?>">
                        <?php echo word_limiter(strip_tags($item->short), 50); ?>
                    </a>
                </p>

                <div class="product-links">
                    <a class="add-to-wishlist btn" href="{{ url:site }}store/customer/add_to_wishlist/<?php echo $item->id; ?>">do ulubionych</a>
                    <a class="add-item btn" href="{{ url:site }}store/cart/add/<?php echo $item->id; ?>" >do koszyka</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p><?php echo lang('store:no_items'); ?></p>
<?php endif; ?>
