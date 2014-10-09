<h2 id="page_title"><?php echo lang('store:cart_title'); ?></h2>
<?php if (!$items_exist): ?>
<div class="notification">
    <?php echo lang('store:cart_is_empty'); ?>
</div>
<?php else: ?>

<?php echo form_open('store/cart/update'); ?>

<table cellpadding="0" cellspacing="0" style="width:100%" border="0" id="cart-table">
    <thead>
        <tr>
            <th class="quantity"><?php echo lang('store:quantity'); ?></th>
            <th class="desc"><?php echo lang('store:item'); ?></th>
            <th style="text-align:right" class="price"><?php echo lang('store:price'); ?></th>
            <th style="text-align:right" class="subtotal"><?php echo lang('store:subtotal'); ?></th>
        </tr>
    </thead>
    <?php $i = 1; ?>
    <tbody>
        <?php foreach ($cart as $items): ?>

            <?php echo form_hidden($i . '[rowid]', $items['rowid']); ?>

            <tr>
                <td class="quantity">
                    <?php
                    echo form_input(array(
                        'name' => $i . '[qty]',
                        'value' => $items['qty'],
                        'maxlength' => '3',
                        'class' => 'input-quantity',
                        'size' => '1'));
                    ?>
                    <a href="{{ url:site }}store/cart/delete/<?php echo $items['rowid']; ?>" class="btn confirm delete"><?php echo lang('global:delete'); ?></a>
                </td>
                <td>
                    <?php echo anchor($items['uri'], $items['name']); ?>

                    <?php if ($this->cart->has_options($items['rowid']) == TRUE): ?>

                        <p>
                            <?php foreach ($this->cart->product_options($items['rowid']) as $option_name => $option_value): ?>

                                <strong><?php echo $option_name; ?>:</strong> <?php echo $option_value; ?><br />

                            <?php endforeach; ?>
                        </p>

                    <?php endif; ?>

                </td>
                <td class="right price"><?php echo $items['price']; ?> {{ settings:currency }}</td>
                <td class="right subtotal"><?php echo $items['subtotal']; ?> {{ settings:currency }}</td>
            </tr>

            <?php $i++; ?>

        <?php endforeach; ?>
    </tbody>
    <tfoot>
        
        <tr>
            <td colspan="2"> </td>
            <td class="right"><strong><?php echo lang('store:total'); ?></strong></td>
            <td class="right"><?php echo $this->cart->total(); ?> {{ settings:currency }}</td>
        </tr>
        
        <?php foreach ($this->cart->totals() as $total): ?>
        <tr>
            <td colspan="2"> </td>
            <td class="right"><strong><?php echo $total['title']; ?></strong></td>
            <td class="right">
                <?php echo $total['value']; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <tr>    
            <td colspan="4" class="right">
                <label><?php echo lang('store:coupon'); ?></label>
                <?php echo form_input('coupon', ''); ?>
            </td>
        </tr>
        <tr>    
            <td colspan="4" class="right">
                <?php echo anchor('store/cart/destroy', lang('store:empty_cart'), 'class="empty"'); ?>
                <?php echo form_submit('', lang('store:update_cart')); ?>
                <?php echo anchor('store/checkout/', lang('store:checkout'), 'class="btn checkout"'); ?>
            </td>
        </tr>
    </tfoot>

</table>


<?php echo form_close(); ?>
<?php endif; ?>
