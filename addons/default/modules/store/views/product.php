<h2 id="page_title"><?php echo $product->name; ?></h2>

<div class="product">
    <div id="head">
        <div class="image left" style="width: 50%; float: left;">
            <img src="{{ url:site }}files/thumb/{{ product:cover_id }}/300" alt="{{ product:name }}" id="cover" />
        </div>
        <div class="information right" style="width: 50%; float: left;">

            <?php echo form_open('store/cart/add'); ?>
            <?php echo form_hidden('id', $product->id); ?>
           
                <div class="price-tag">
                <?php if (isset($product->sale_type)): ?>
                    <span class="<?php echo $product->sale_type; ?>">
                        <?php echo $product->sale_type == 'reduce' ? '-' . $product->reduction . '%' : lang('store:option_' . $product->sale_type); ?>
                    </span>
                    <?php if ($product->price > $product->new_price): ?>
                        <span style="text-decoration: line-through">
                        <?php echo $product->price_tax; ?>
                        </span> 
                        <span id="price" data-price="<?php echo $product->new_price_tax; ?>"><?php echo $product->new_price_tax; ?></span>
                    <?php else: ?>
                        <span id="price" data-price="{{ product:price_tax }}">{{ product:price_tax }}</span>
                    <?php endif; ?>
                <?php else: ?>
                    <span id="price" data-price="{{ product:price_tax }}">{{ product:price_tax }}</span>
                <?php endif; ?>
                {{ settings:currency }}
                </div>
                
                {{ if product:short }}
                <p>
                    {{ product:short }}
                </p>
                {{ endif }}


                {{ if has_options == true }}
                    <h4><?php echo lang('store:options'); ?></h4>
                    <?php foreach ($options as $item): ?>
                    <div class="input">
                    <label><?php echo $item['title']; ?></label><br />
                    <?php echo prepare_option($item); ?>
                    </div>
                    <?php endforeach; ?>
                {{ endif }}
                    <div class="input">
                        <label><?php echo lang('store:quantity'); ?>:</label>
                        <input type="text" name="quantity" value="1" />
                    </div>
                    <div class="input">
                        <?php echo form_submit('submit', lang('store:add_to_cart'), 'class="add-item"'); ?><br />
                    </div>
            <?php echo form_close(); ?>
        </div>
        <div style="clear: both"></div>
    </div>
    <div id="details" class="tabbable">
        <ul class="tabs" style="display: none;">
            <li><a href="#information" data-toggle="tab"><?php echo lang('store:info'); ?></a></li>
            {{ if product:description }}
            <li><a href="#description" data-toggle="tab"><?php echo lang('store:desc'); ?></a></li>
            {{ endif }}
            <?php if ($product->attributes): ?>
                <li><a href="#params" data-toggle="tab"><?php echo lang('store:attributes'); ?></a></li>
            <?php endif; ?>
            <?php if (count($images)): ?>
                <li><a href="#images" data-toggle="tab"><?php echo lang('store:images'); ?></a></li>
            <?php endif; ?>
            <?php if (count($product->related)): ?>
                <li><a href="#related" data-toggle="tab"><?php echo lang('store:related'); ?></a></li>
            <?php endif; ?>
            <?php if (Settings::get('enable_comments')): ?>
                <li><a href="#product-comments" data-toggle="tab"><?php echo lang('comments:title'); ?></a></li>
            <?php endif; ?>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="information">
                <h3>Information</h3>
                <ul class="list-unstyled">
                <li>
                    <b><?php echo lang('store:date_created'); ?></b>: <span>{{ product:created }}</span>
                </li>
                <li>
                    <b><?php echo lang('store:status'); ?></b>: <span><?php echo lang('store:status_' . $product->status) ?></span>
                </li>
                <li>
                    <b><?php echo lang('store:category'); ?></b>: 
                    <span>
                        <?php echo prepare_categories($node, ' &raquo; '); ?>
                    </span>
                </li>
                <?php if ($product->keywords): ?>
                <li>
                    <b><?php echo lang('global:keywords'); ?>:</b>
                    <div class="keywords">
                        <?php foreach ($product->keywords as $keyword): ?>
                            <span><?php echo anchor('store/tagged/' . $keyword->name, $keyword->name, 'class="keyword"') ?></span>
                        <?php endforeach; ?>
                    </div>
                </li>
                <?php endif; ?>
                <li>
                    <a href="{{ url:site }}store/customer/add_to_wishlist/{{ product:id }}"><?php echo lang('store:add_to_wishlist'); ?></a>
                </li>
                </ul>
            </div>
            {{ if product:description }}
            <div class="tab-pane active" id="description">
                <h3><?php echo lang('store:desc'); ?></h3>
                <p>
                    {{ product:description }}
                </p>
            </div>
            {{ endif }}
            <?php if ($product->attributes): ?>
                <div class="tab-pane" id="params">
                    <h3><?php echo lang('store:attributes'); ?></h3>
                    <p>
                        <?php foreach (json_decode($product->attributes) as $atr): ?>
                            <b><?php echo $atr->label; ?>:</b> <?php echo $atr->value; ?><br />
                        <?php endforeach; ?>
                    </p>
                </div>
            <?php endif; ?>
            <?php if (count($images)): ?>
                <div class="tab-pane" id="images">
                    <h3><?php echo lang('store:images'); ?></h3>
                    <?php foreach ($images as $img): ?>
                        <a href="{{ url:site }}files/large/<?php echo $img->file_id; ?>" class="pyro-image" >
                            <img src="{{ url:site }}files/thumb/<?php echo $img->file_id; ?>/100/100/fit" />
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if (count($product->related)): ?>
                <div class="tab-pane" id="related">
                    <h3><?php echo lang('store:related'); ?></h3>
                    <?php echo load_subview('product_list', array('items' => $product->related)); ?>
            <?php endif; ?>
            <?php if (Settings::get('enable_comments')): ?>
                <div id="product-comments" class="tab-pane">
                    <div id="existing-comments">
                        <h3><?php echo lang('comments:title') ?></h3>
                        <?php echo $this->comments->display() ?>
                    </div>
                        <?php echo $this->comments->form() ?>
                </div>
            <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
    $(document).ready(function() {
        $('.option').change(function() {
            var base = new Number($('#price').attr('data-price'));
            var mod = new Number(0);

            $('.option').each(function(i,o) {
                if ($(o).is(':checked')) {
                    mod = mod + new Number($(o).attr('data-price'));
                }
                if ($(o).is('select')) {
                    mod = mod + new Number($(o).find(':selected').attr('data-price'));
                }
            });
            console.log(mod);
            mod = mod + base;
            $('#price').text(mod.toFixed(2));

        }).trigger('change');
    });
    </script>