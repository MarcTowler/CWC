	
<section class="title">
    <h4>Promocja grupowa</h4>
</section>


<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>
<section class="item form_inputs">
    <div class="content">
        <fieldset>
            <ul>
                <li class="<?php echo alternator('even', ''); ?>">
                    <label for="sale_type">Rodzaj promocji</label>
                    <div class="input">
                        <?php
                        echo form_dropdown('sale_type', array(
                            '' => lang('global:select-any'),
                            'sale' => 'Wyprzedaż',
                            'reduce' => 'Obniżka',
                            'promo' => 'Promocja',
                            'featured' => 'Polecane',
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
                    <label for="reduction"><?php echo lang('store:reduction'); ?></label>
                    <div class="input">
                        <?php echo form_input('reduction', set_value('reduction', $reduction), 'id="reduction" '); ?>%
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

    $(document).ready(function() {
        $('#count-btn').click(function() {
            var reduction = new Number(1 - ($('#reduction').val() / 100));
            $('#new_price').val(new Number(item_price * reduction).toFixed(2));
            return false;
        });
        $('#reset-btn').click(function() {
            $('#new_price').val(item_price);
            $('#reduction').val(0);
            return false;
        });
        $('#product_id').change(function() {
            $.getJSON(SITE_URL + 'admin/store/specials/ajax', {
                product_id: $('#product_id').val()
            }, function(data) {
                item_price = data.price;
                $('#new_price').val(data.price);
            });
        });
        $('#category_id').change(function() {
            $.getJSON(SITE_URL + 'admin/store/specials/ajax', {
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