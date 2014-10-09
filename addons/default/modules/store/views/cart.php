<h2 id="page_title"><?php echo "Your Shopping " . lang('store:cart_title'); ?></h2>
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
            <th class="price"><?php echo lang('store:price'); ?></th>
            <th class="subtotal"><?php echo lang('store:subtotal'); ?></th>
        </tr>
    </thead>
    <?php $i = 1; ?>
    <tbody>
        <?php foreach ($cart as $items): ?>

            <?php echo form_hidden($i . '[rowid]', $items['rowid']); ?>

            <tr>
                <td class="quantity" style="text-align: center;">
                    <?php
                    echo form_input(array(
                        'name' => $i . '[qty]',
                        'value' => $items['qty'],
                        'maxlength' => '3',
                        'class' => 'input-quantity',
                        'size' => '1'));
                    ?>
                    <!--<a href="{{ url:site }}store/cart/delete/<?php echo $items['rowid']; ?>" class="btn confirm delete"><?php echo lang('global:delete'); ?></a>
            -->    </td>
                <td style="text-align: center;">
                    <?php echo anchor($items['uri'], $items['name']); ?>

                    <?php if ($this->cart->has_options($items['rowid']) == TRUE): ?>

                        <p>
                            <?php foreach ($this->cart->product_options($items['rowid']) as $option_name => $option_value): ?>

                                <strong><?php echo $option_name; ?>:</strong> <?php echo $option_value; ?><br />

                            <?php endforeach; ?>
                        </p>

                    <?php endif; ?>

                </td>
                <td class="right price" style="text-align: center;">{{ settings:currency }}<?php echo $items['price']; ?></td>
                <td class="right subtotal" style="text-align: center;">{{ settings:currency }}<?php echo $items['subtotal']; ?></td>
            </tr>

            <?php $i++; ?>

        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2"> </td>
            <td class="right"><strong><?php echo lang('store:dubtotal'); ?></strong></td>
            <td class="right" style="text-align: center;">{{ settings:currency }}<?php echo $this->cart->total(); ?></td>
        </tr>
        
        <?php foreach ($this->cart->totals() as $total): ?>
        <tr>
            <td colspan="2"> </td>
            <td class="right"><strong><?php echo $total['title']; ?></strong></td>
            <td class="right" style="text-align: center;">
                {{ settings:currency }}<?php echo str_replace('&dollar;', '', $total['value']); ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="4" class="right">
                <?php echo anchor('store/cart/destroy', lang('store:empty_cart'), 'class="empty"'); ?>
            </td>
        </tr>
        <tr>
            <td colspan="4">&nbsp;</td>
        </tr>
        <tr>    
            <td colspan="4" class="right">
                <label><?php echo lang('store:coupon'); ?></label>
                <?php echo form_input('coupon', ''); ?>
            </td>
        </tr>
        <tr>    
            <td colspan="4" class="right">
                <?php echo form_submit('', lang('store:update_cart')); ?>
                <button><?php echo anchor('store/checkout/', lang('store:checkout'), 'class="btn checkout"'); ?></button>
            </td>
        </tr>
    </tfoot>

</table>


<?php echo form_close(); ?>
<?php endif; ?>
