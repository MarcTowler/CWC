<section class="title">
    <!-- We'll use $this->method to switch between store.create & store.edit -->
    <h4><?php echo lang('store:' . $this->method); ?></h4>
</section>

<section class="item">

    <?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>
    <fieldset>
        <div class="form_inputs">

            <ul>
                <li class="<?php echo alternator('', 'even'); ?>">
                    <label for="name"><?php echo lang('store:name'); ?> <span>*</span></label>
                    <div class="input"><?php echo form_input('name', set_value('name', $name), 'class="width-15"'); ?></div>
                </li>
                <li class="<?php echo alternator('', 'even'); ?>">
                    <label for="desc"><?php echo lang('store:desc'); ?></label>
                    <div class="input"><?php echo form_textarea('desc', set_value('desc', $desc), 'class="width-15"'); ?></div>
                </li>

                <li class="<?php echo alternator('', 'even'); ?>">
                    <label for="url"><?php echo lang('store:tracking_url'); ?> <small>use this format: <span>http://track.carrier.com/package/{{ code }}</span><br /> where {{ code }} means carriers tracking id</small></label>
                    <div class="input"><?php echo form_input('url', set_value('url', $url), 'class="width-15" placeholder="http://track.carrier.com/package/{{ code }}"'); ?></div>
                </li>

                <li class="<?php echo alternator('', 'even'); ?>">
                    <label for="price"><?php echo lang('store:price'); ?> <span>*</span></label>
                    <div class="input"><?php echo form_input('price', set_value('price', $price), 'class="width-15"'); ?></div>
                </li>
                <li class="<?php echo alternator('', 'even'); ?>">
                    <label for="type"><?php echo lang('store:type'); ?> <span>*</span></label>
                    <div class="input">
                        <?php
                        echo form_dropdown('type', array('' => lang('global:select-any'),
                            'flat' => "Flat Rate",
                            'weight' => "Weight Based",
                            'quantity' => "Quantity Based",
                            'distance' => "Distance Based"), set_value('type', $type), 'class="width-15" id="shipping_type"');
                        ?>
                    </div>

                    <div id="flat" class="options" style="display: none;">
                        <label><?php echo form_checkbox('options[tax]', '1', set_checkbox('options[tax]', '1', isset($options['tax']))); ?>Use Tax?</label>
                    </div>
                    <div id="weight" class="options" style="display: none;">
                        <?php if ($type == 'weight' && count($options) > 0): ?>
                            <?php foreach ($options as $value): ?>
                                <div class="range sortable">
                                    <span class="sort-handle"></span>
                                    <?php echo form_input('options[0][from]', $value['from'], 'placeholder="from"'); ?><?php echo Settings::get('store_weight_unit'); ?> - 
                                    <?php echo form_input('options[0][to]', $value['to'], 'placeholder="to"'); ?><?php echo Settings::get('store_weight_unit'); ?> = 
                                    <?php echo form_input('options[0][price]', $value['price'], 'placeholder="price"'); ?><?php echo Settings::get('currency'); ?>
                                    <a class="button remove" href="#" style="display: none;">Remove</a>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="range sortable">
                                <span class="sort-handle"></span>
                                <?php echo form_input('options[0][from]', '', 'placeholder="from"'); ?><?php echo Settings::get('store_weight_unit'); ?> - 
                                <?php echo form_input('options[0][to]', '', 'placeholder="to"'); ?><?php echo Settings::get('store_weight_unit'); ?> = 
                                <?php echo form_input('options[0][price]', '', 'placeholder="price"'); ?><?php echo Settings::get('currency'); ?>
                                <a class="button remove" href="#" style="display: none;">Remove</a>
                            </div>
                        <?php endif; ?>
                        <a href="<?php echo uri_string(); ?>#" class="button add">Add Range</a>
                    </div>
                    <div id="quantity" class="options" style="display: none;">
                        <?php if ($type == 'quantity' && count($options) > 0): ?>
                            <?php foreach ($options as $value): ?>
                                <div class="range sortable">
                                    <span class="sort-handle"></span>
                                    <?php echo form_input('options[0][from]', $value['from'], 'placeholder="from"'); ?>x - 
                                    <?php echo form_input('options[0][to]', $value['to'], 'placeholder="to"'); ?>x = 
                                    <?php echo form_input('options[0][price]', $value['price'], 'placeholder="price"'); ?><?php echo Settings::get('currency'); ?>
                                    <a class="button remove" href="<?php echo uri_string(); ?>#" style="display: none;">Remove</a>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="range sortable">
                                <span class="sort-handle"></span>
                                <?php echo form_input('options[0][from]', '', 'placeholder="from"'); ?>x - 
                                <?php echo form_input('options[0][to]', '', 'placeholder="to"'); ?>x = 
                                <?php echo form_input('options[0][price]', '', 'placeholder="price"'); ?><?php echo Settings::get('currency'); ?>
                                <a class="button remove" href="<?php echo uri_string(); ?>#" style="display: none;">Remove</a>
                            </div>
                        <?php endif; ?>
                        <a href="<?php echo uri_string(); ?>#" class="button add">Add Range</a>
                    </div>
                    <div id="distance" class="options" style="display: none;">
                        <div class="range">
                            <small>Your store location</small><br />
                            <?php echo form_input('options[location]', set_value('options[location]', isset($options['location']) ? $options['location'] : ''), 'placeholder="Mittenwalder Straße 14, Berlin, Niemcy" style="width: 300px;"'); ?>
                            
                        </div>
                        <?php if ($type == 'distance' && count($options) > 0): ?>
                            <?php foreach ($options as $k => $value): ?>
                        <?php if (is_numeric($k)): ?>
                                <div class="range sortable">
                                    <span class="sort-handle"></span>
                                    <?php echo form_input('options[0][from]', $value['from'], 'placeholder="from"'); ?>x - 
                                    <?php echo form_input('options[0][to]', $value['to'], 'placeholder="to"'); ?>x = 
                                    <?php echo form_input('options[0][price]', $value['price'], 'placeholder="price"'); ?><?php echo Settings::get('currency'); ?>
                                    <a class="button remove" href="<?php echo uri_string(); ?>#" style="display: none;">Remove</a>
                                </div>
                        <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="range sortable">
                                <span class="sort-handle"></span>
                                <?php echo form_input('options[0][from]', '', 'placeholder="from"'); ?>x - 
                                <?php echo form_input('options[0][to]', '', 'placeholder="to"'); ?>x = 
                                <?php echo form_input('options[0][price]', '', 'placeholder="price"'); ?><?php echo Settings::get('currency'); ?>
                                <a class="button remove" href="<?php echo uri_string(); ?>#" style="display: none;">Remove</a>
                            </div>
                        <?php endif; ?>
                        <a href="<?php echo uri_string(); ?>#" class="button add">Add Range</a>
                    </div>
                </li>

                <li class="<?php echo alternator('', 'even'); ?>">
                    <label for="min"><?php echo lang('store:min_amount'); ?> </label>
                    <div class="input"><?php echo form_input('min', set_value('min', $min), 'class="width-15"'); ?></div>
                </li>
                <li class="<?php echo alternator('', 'even'); ?>">
                    <label for="max"><?php echo lang('store:max_amount'); ?> </label>
                    <div class="input"><?php echo form_input('max', set_value('max', $max), 'class="width-15"'); ?></div>
                </li>
                <li class="<?php echo alternator('', 'even'); ?>">
                    <label for="status"><?php echo lang('store:status'); ?> </label>
                    <div class="input"><?php echo form_dropdown('status', array('active' => lang('store:status_active'), 'disabled' => lang('store:status_disabled')), set_value('status', $status), 'class="width-15"'); ?></div>
                </li>
            </ul>

        </div>

        <div class="buttons">
            <?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel'))); ?>
        </div>

        <?php echo form_close(); ?>
    </fieldset>
</section>

<script>
    var siblings = 0;
    $(document).ready(function(){
        $('#shipping_type').change(function() {
            $('.options:visible').hide();
            $('.options input').attr('disabled', 'disabled');
            var selected = $(this).val();
            $('#'+selected).show();
            $('#'+selected).find('input').removeAttr('disabled');
        });
        
        $('#shipping_type').change();
        
        $('.options .add').click(function(e) {
            //$(this).before(range_template);
            var clone = $(this).prev().clone();
            siblings = $(this).siblings('.range').length;
            clone.find('input[type="text"]').val('');
            clone.find('input[type="text"]').each(function(i, elem){
                var name = $(elem).attr('name');
                name = name.replace(/\[([0-9]*)\]/gi, "["+siblings+"]");
                $(elem).attr('name', name);
            });
            
            clone.find('.remove').show();
            $(this).before(clone);
            return false;
        });
        
        $('.options .remove').live('click', function() {
            $(this).parent().remove();
            return false;
        });
        $('.options').sortable({
            items: '.sortable',
            scroll: false,
            handle: '.sort-handle',
            update: function(e, ui) {
                var parent = $(ui.item).parent();
                $(parent).find('.range').each(function(i, elem){
                    $(elem).find('input[type="text"]').each(function(id) {
                        var name = $(this).attr('name');
                        name = name.replace(/\[([0-9]*)\]/gi, "["+id+"]");
                        $(this).attr('name', name);
                    }, [i]);
                    if (i == 0) {
                        $(elem).find('.remove').hide();
                    } else {
                        $(elem).find('.remove').show();
                    }
                });
            }
        });
    });
</script>