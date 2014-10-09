<h2 id="page_title"><?php echo lang('store:wishlist_title'); ?></h2>

<div class="store-container">
    <table>
        <thead>
            <tr>
                <th><?php echo lang('store:image'); ?></th>
                <th><?php echo lang('store:name'); ?></th>
                <th><?php echo lang('store:price'); ?></th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?php echo $item->cover_id ? img("files/thumb/{$item->cover_id}/75") : ''; ?></td>
            <td><a href="{{ url:site }}store/product/<?php echo $item->slug; ?>"><?php echo $item->name; ?></a></td>
            <td><?php if (isset($item->new_price)): ?>
                <span style="text-decoration: line-through"><?php echo $item->price; ?></span> <?php echo $item->new_price; ?> 
                <?php else: ?>
                <?php echo $item->price; ?>
                <?php endif; ?>
                {{ settings:currency }}
            </td>
            <td><a href="{{ url:site }}store/customer/remove_from_wishlist/<?php echo $item->id; ?>">{{ helper:lang line="global:delete" }}</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <p>
        <a href="{{ url:site }}store/customer"><?php echo lang('store:back_to_customer'); ?></a>
    </p>
</div>
