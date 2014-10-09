	
<section class="title">
    <?php if (isset($id) AND $id > 0): ?>
        <h4><?php echo sprintf(lang('store:edit'), $title); ?></h4>
    <?php else: ?>
        <h4><?php echo lang('store:create'); ?></h4>
    <?php endif; ?>
</section>


<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>
<?php if (isset($id) AND $id > 0): ?>
    <?php echo form_hidden('id', $id); ?>
<?php endif; ?>
<section class="item form_inputs">
    <div class="content">
        <fieldset>
            <legend><?php echo lang('store:product'); ?></legend>
            <ul>
                <li class="<?php echo alternator('even', ''); ?>">
                    <label for="sale_type"><?php echo lang('store:sale_type'); ?></label>
                    <div class="input">
                        <?php
                        echo form_dropdown('sale_type', array(
                            '' => lang('global:select-any'),
                            'sale' => lang('store:option_sale'),
                            'reduce' => lang('store:option_reduce'),
                            'promo' => lang('store:option_promo'),
                            'featured' => lang('store:option_featured'),
                                ), set_value('sale_type', $sale_type), 'id="sale_type" autocomplete="off"');
                        ?>
                    </div>
                </li>
                <li class="<?php echo alternator('even', ''); ?>">
                    <label for="category_id"><?php echo lang('store:category'); ?></label>
                    <div class="input">
                        <?php //echo form_dropdown('category_id', array_merge(array(0 => lang('global:select-all')), $categories), set_value('category_id', $category_id), 'id="category_id" autocomplete="off"');  ?>
                        <select name="category_id" id="category_id">
                            <option value="0"><?php echo lang('global:select-pick'); ?></option>
                            <?php echo $categories; ?> 
                        </select>
                    </div>
                </li>
                <li class="<?php echo alternator('even', ''); ?>">
                    <label for="product_id"><?php echo lang('store:product'); ?></label>
                    <div class="input">
                        <?php echo form_dropdown('product_id', array_merge(array(0 => lang('global:select-any')), $products), set_value('product_id', $product_id), 'id="product_id" autocomplete="off"'); ?>
                    </div>
                </li>
            </ul>
        </fieldset>
        <fieldset>
        <legend><?php echo lang('store:new_price'); ?></legend>
        <ul>
            <li class="<?php echo alternator('even', ''); ?>">
                <label for="reduction"><?php echo lang('store:reduction'); ?></label>
                <div class="input">
                    <?php echo form_input('reduction', set_value('reduction', $reduction), 'id="reduction" '); ?>%
                    <a href="#" class="button" id="count-btn"><?php echo lang('store:count_button'); ?></a>
                </div>
            </li>
            <li class="<?php echo alternator('even', ''); ?>">
                <label for="new_price"><?php echo lang('store:price_notax'); ?></label>
                <div class="input">
                    <?php echo form_input('new_price', set_value('new_price', $new_price), 'id="new_price" '); ?>
                    <a href="#" class="button" id="reset-btn"><?php echo lang('store:reset_button'); ?></a>
                </div>
            </li>
            <li class="<?php echo alternator('even', ''); ?>">
                <label for="new_price_tax"><?php echo lang('store:price_tax'); ?></label>
                <div class="input">
                    <?php echo form_input('new_price_tax', set_value('new_price_tax', $new_price_tax), 'id="new_price_tax" '); ?>
                </div>
            </li>
        </ul>
        </fieldset>

        <div class="buttons">
            <?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel'))); ?>
        </div>
    </div>
</section>

<?php echo form_close(); ?>
<script>
    var item_price = <?php echo $this->method == 'create' ? 0 : $price; ?>;
    var item_price_tax = <?php echo $this->method == 'create' ? 0 : $price_tax; ?>;
    var tax_multiplier = (tax_rate / 100) + 1;

    $(document).ready(function() {
        $('#count-btn').click(function() {
            var reduction = new Number(1 - ($('#reduction').val() / 100));
            $('#new_price').val(new Number(item_price * reduction).toFixed(2));
            $('#new_price_tax').val(new Number(item_price_tax * reduction).toFixed(2));
            return false;
        });
        $('#reset-btn').click(function() {
            $('#new_price').val(item_price);
            $('#new_price_tax').val(item_price_tax);
            $('#reduction').val(0);
            return false;
        });
        $('#product_id').change(function() {
            $.getJSON(SITE_URL + 'admin/store/sales/ajax', {
                product_id: $('#product_id').val()
            }, function(data) {
                item_price = data.price;
                item_price_tax = data.price_tax;
                $('#new_price').val(data.price);
                $('#new_price_tax').val(data.price_tax);
            });
        });
        $('#category_id').change(function() {
            $.getJSON(SITE_URL + 'admin/store/sales/ajax', {
                category_id: $('#category_id').val()
            }, function(data) {
                $('#product_id').find('option').remove();
                for (var index in data) {
                    var item = data[index];
                    $('#product_id').append('<option value="' + item.id + '">' + item.name + '</option>');
                    console.log();
                }
                $("#product_id").trigger("liszt:updated");
            });
        });
    });

</script>