<h2 id="page_title"><?php echo lang('store:shipment_title'); ?></h2>
<?php
if (validation_errors()) {
    echo '<div class="error">';
    echo validation_errors();
    echo '</div>';
}
?>
<?php echo form_open(); ?>
<fieldset>
    <ul>
        <?php foreach ($shipments as $ship): ?>
            <li class="<?php echo alternator('even', 'odd'); ?>">
                <label class="radio"><?php echo form_radio('shipment_id', $ship->id, set_radio('shipment_id', $ship->id, FALSE), ($ship->max > 0 && $ship->max < $this->cart->total() )? 'disabled="disabled"' : '') . $ship->name; ?> - <span class="price"><?php echo $ship->price; ?></span>{{ settings:currency }}</label>
                <?php echo form_hidden('price_'.$ship->id, $ship->price); ?>
                <small><?php echo $ship->desc; ?></small>
                <hr />
            </li>
        <?php endforeach; ?>
    </ul>
</fieldset>
<fieldset>    
    <span style="float: right;">
                <?php echo anchor('store/cart/', lang('store:back_to_cart')); ?> | 
        <?php echo form_submit('submit', lang('checkout:continue')); ?>
    </span>
</fieldset>


<?php echo form_close(); ?>
