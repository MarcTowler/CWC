<?php if (count($items)): ?>
    <?php if ($options['display'] == 'list'): ?>
        <ul class="wishlist-items">
            <?php foreach ($items as $item): ?>
                <li>
                    <a href="<?php echo site_url('store/product/' . $item->slug); ?>">
                        <?php echo $item->name; ?>
                    </a>
                    <?php echo ' - ' . $item->price_tax; ?> {{ settings:currency }}
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <?php foreach ($items as $item): ?>
            <div class="wishlist-item">
                <span class="image"><?php echo $item->cover_id ? img('files/thumb/' . $item->cover_id. '/140/') : ''; ?></span>
                <span class="name"><?php echo anchor('store/product/' . $item->slug, $item->name); ?></span>
                <span class="price"><?php echo ' - ' . $item->price; ?> {{ settings:currency }}</span>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
<?php else: ?>
    <p><?php echo lang('store:no_items'); ?></p>
<?php endif; ?>
<hr />
<a class="show_wishlist" href="{{ url:site }}store/customer/wishlist"><?php echo lang('store:show_wishlist'); ?></a>