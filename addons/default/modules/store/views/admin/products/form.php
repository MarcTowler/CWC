<section class="title">
    <!-- We'll use $this->method to switch between store.create & store.edit -->
    <h4><?php echo lang('store:' . $this->method); ?></h4>
</section>

<section class="item">
    <div class="content">
        <?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>
        <div class="tabs">

            <ul class="tab-menu">
                <li><a href="#product-tab"><span><?php echo lang('store:product'); ?></span></a></li>
                <li><a href="#content-tab"><span><?php echo lang('store:content'); ?></span></a></li>
                <?php if ($stream_fields): ?><li><a href="#custom"><span><?php echo lang('global:custom_fields') ?></span></a></li><?php endif; ?>
                <li><a href="#images-tab"><span><?php echo lang('store:images'); ?></span></a></li>
                <li><a href="#attributes-tab"><span><?php echo lang('store:attributes'); ?></span></a></li>
                <li><a href="#options-tab"><span><?php echo lang('store:options'); ?></span></a></li>
            </ul>
            <div class="form_inputs" id="product-tab">
                <fieldset>
                    <ul>
                        <li class="<?php echo alternator('', 'even'); ?>">
                            <label for="name"><?php echo lang('store:name'); ?> <span>*</span></label>
                            <div class="input"><?php echo form_input('name', set_value('name', $name), 'class="width-15"'); ?></div>
                        </li>

                        <li class="<?php echo alternator('', 'even'); ?>">
                            <label for="slug"><?php echo lang('store:slug'); ?> <span>*</span></label>
                            <div class="input"><?php echo form_input('slug', set_value('slug', $slug), 'class="width-15"'); ?></div>
                        </li>
                        <li class="<?php echo alternator('', 'even'); ?>">
                            <label for="sku"><?php echo lang('store:sku'); ?> <span>*</span></label>
                            <div class="input"><?php echo form_input('sku', set_value('sku', $sku), 'class="width-15"'); ?></div>
                        </li>
                        <li class="<?php echo alternator('', 'even'); ?>">
                            <label for="category_id"><?php echo lang('store:category'); ?><span>*</span></label>
                            <div class="input">
                                <select name="category_id" id="category_id">
                                    <option value="0"><?php echo lang('global:select-pick'); ?></option>
                                    <?php echo $tree_select; ?> 
                                </select>
                            </div>
                        </li>



                        <li class="<?php echo alternator('', 'even'); ?>">
                            <label for="price"><?php echo lang('store:price_notax'); ?> <span>*</span><br /><small><?php echo lang('store:price_desc'); ?></small></label>
                            <div class="input">
                                <?php echo form_input('price', set_value('price', $price), 'class="width-15" id="price"') . Settings::get('currency'); ?>
                                <label><input type="checkbox" id="link_price" checked="checked" /> <?php echo lang('store:link_price'); ?></label>
                            </div>
                        </li>
                        <li class="<?php echo alternator('', 'even'); ?>">
                            <label for="price_tax"><?php echo lang('store:price_tax'); ?> <span>*</span><br /><small><?php echo lang('store:price_desc'); ?></small></label>
                            <div class="input"><?php echo form_input('price_tax', set_value('price_tax', $price_tax), 'class="width-15" id="price_tax"') . Settings::get('currency'); ?></div>
                        </li>
                        <li class="<?php echo alternator('', 'even'); ?>">
                            <label for="status"><?php echo lang('store:status'); ?></label>
                            <div class="input"><?php
                                echo form_dropdown('status', array(
                                    'in_stock' => lang('store:status_instock'),
                                    'soon_available' => lang('store:status_soon'),
                                    'out_of_stock' => lang('store:status_outofstock'),
                                    'pre_order' => lang('store:status_preorder'),
                                    'disabled' => lang('store:status_disabled'),
                                        ), set_value('status', $status), 'class="width-15"');
                                ?></div>
                        </li>

                    </ul>
                </fieldset>
            </div>
            <div class="form_inputs" id="content-tab">
                <fieldset>
                    <ul> 
                        <li class="<?php echo alternator('', 'even'); ?>">
                            <label for="short"><?php echo lang('store:short'); ?><br />
                                <small>Used also as META Description</small>
                            </label>
                            <div class="input"><?php echo form_textarea('short', set_value('short', $short), 'style="width: 50%"'); ?></div>
                        </li>

                        <li class="<?php echo alternator('', 'even'); ?>">
                            <label for="description"><?php echo lang('store:desc'); ?></label>
                            <div class="input"><?php echo form_textarea('description', set_value('description', $description), 'class="wysiwyg-advanced"'); ?></div>
                        </li>
                        

                        <li>
                            <label for="keywords"><?php echo lang('global:keywords'); ?><br />
                                <small>Used also as META Keywords</small></label>
                            <div class="input"><?php echo form_input('keywords', $keywords, 'id="keywords"') ?></div>
                        </li>
                        <li>
                            <label><?php echo lang('store:select_product'); ?></label>
                            <div class="rel-list">
                                <?php echo form_multiselect('related[]', $related_list, set_value('related[]', $related), 'id="rel-items"'); ?>
                            </div>
                        </li>
                    </ul>
                </fieldset>
            </div>
            <?php if ($stream_fields): ?>

                <div class="form_inputs" id="custom">
                    <fieldset>
                        <ul>
                            <?php
                            foreach ($stream_fields as $field)
                                echo $this->load->view('admin/partials/streams/form_single_display', array('field' => $field), true)
                                ?>

                        </ul>
                    </fieldset>
                </div>

            <?php endif; ?>
            <div class="form_inputs" id="images-tab">
                <fieldset>
                    <ul>
                        <li class="<?php echo alternator('', 'even'); ?>">
                            <label for="cover"><?php echo lang('store:cover'); ?><span>*</span></label>
                            <div class="input">
                                <div>
                                    <span id="image_thumb"><?php echo $cover_id ? img(site_url('files/thumb/' . $cover_id)) . br() : ''; ?></span>
                                    <input type="hidden" name="cover_id" value="<?php echo $cover_id; ?>" id="image_id" />
                                    <?php echo anchor('admin/store/ajax/image', lang('store:select_image'), 'class="modal-large button"'); ?>
                                </div>
                            </div>
                        </li>
                        <li class="<?php echo alternator('', 'even'); ?>">
                            <label for="userfile"><?php echo lang('store:select_files'); ?> <br />
                                <small>
                                    <?php echo lang('store:images_desc'); ?><br />
                                    <?php echo anchor('admin/files', lang('store:manage_folders'), 'target="_blank"'); ?>
                                </small>
                            </label>
                            <div class="input">
                            <?php echo form_dropdown('folder', $folders, set_value('folder', Settings::get('store_images_folder')), 'id="folder"'); ?>
                            <div class="dropzone <?php if (count($images)): ?>dz-started<?php endif; ?>" id="myDropzone">
                                <div class="fallback">
                                    <?php echo form_upload('userfile', set_value('userfile'), 'class="width-15" id="userfile" multiple'); ?>
                                </div>
                                <?php if ($images): ?>
                                    <?php foreach ($images as $img): ?>
                                        <div class="dz-preview dz-image-preview">
                                            <div class="dz-details">
                                                <div class="dz-filename"><span data-dz-name><?php echo $img->name; ?></span></div>
                                                <div class="dz-size" data-dz-size><strong><?php echo format_bytes($img->filesize); ?></strong> MB</div>
                                                <img data-dz-thumbnail src="<?php echo site_url('files/thumb/' . $img->file_id . '/100/100/fit'); ?>" />
                                            </div>
                                            <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
                                            <div class="dz-success-mark"><span>✔</span></div>
                                            <div class="dz-error-mark"><span>✘</span></div>
                                            <div class="dz-error-message"><span data-dz-errormessage></span></div>
                                            <input type="hidden" name="images[]" value="<?php echo $img->file_id; ?>" />
                                            <a class="remove">remove</a>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        </li>

                        
                    </ul>
                </fieldset>
            </div>
            <div class="form_inputs" id="attributes-tab">
                <fieldset>
                    <table>
                        <thead>
                            <tr>
                                <th><?php echo lang('store:attribute_label'); ?></th>
                                <th class="input"><?php echo lang('store:attribute_value'); ?></th>
                            </tr>
                        </thead>
                        <tbody id="attributes-list">
                            <?php $index = 0; ?>
                            <?php if ($attributes || $_POST['attributes']): ?>
                                <?php $attributes_array = $attributes ? json_decode($attributes, TRUE) : $_POST['attributes']; ?>
                                <?php foreach ($attributes_array as $atr): ?>
                                    <tr id="item_<?php echo $index; ?>">
                                        <td> <?php echo form_input('attributes[' . $index . '][label]', set_value('attributes[' . $index . '][label]', $atr['label']), 'class="at_label" placeholder="label"'); ?> :</td>
                                        <td class="input">
                                            <?php echo form_input('attributes[' . $index . '][value]', set_value('attributes[' . $index . '][value]', $atr['value']), 'class="at_value" placeholder="value"'); ?>
                                            <a class="btn red remove" data-row="item_<?php echo $index; ?>"><?php echo lang('store:remove_row'); ?></a>
                                        </td>
                                    </tr>
                                    <?php $index++; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <tr id="item_<?php echo $index; ?>">
                                <td><?php echo form_input('attributes[' . $index . '][label]', set_value('attributes[' . $index . '][label]'), 'class="at_label" placeholder="label"'); ?> :</td>
                                <td class="input">
                                    <?php echo form_input('attributes[' . $index . '][value]', set_value('attributes[' . $index . '][value]'), 'class="at_value" placeholder="value"'); ?>
                                    <a class="btn red remove" data-row="item_<?php echo $index; ?>"><?php echo lang('store:remove_row'); ?></a>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2"><a class="btn green" id="add-attribute"><?php echo lang('store:add_row'); ?></a></td>
                            </tr>
                        </tfoot>
                    </table>
                </fieldset>
            </div>
            <div class="form_inputs" id="options-tab">
                <fieldset>
                    <table>
                        <thead>
                            <tr>
                                <th><?php echo lang('store:name'); ?></th>
                                <th><?php echo lang('store:value'); ?></th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="options-list">
                            <?php foreach ($options as $index => $option): ?>
                                <tr data-option="<?php echo $option->option_id; ?>" data-value="<?php echo $option->value_id; ?>">
                                    <td class="optname"><input type="hidden" name="options[<?php echo $index; ?>][option_id]" value="<?php echo $option->option_id; ?>"><?php echo $option->title; ?></td>
                                    <td class="optvalue"><input type="hidden" name="options[<?php echo $index; ?>][value_id]" value="<?php echo $option->value_id; ?>"><?php echo $option->label; ?></td>
                                    <td class="optprice"><input type="text" name="options[<?php echo $index; ?>][price]" class="priceformat" value="<?php echo $option->price; ?>"></td>
                                    <td class="optremove"><a class="btn red remove">Remove</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>
                                    <?php echo form_dropdown('', array('' => lang('global:select-any')) + $options_list, '', 'id="option-select"'); ?>
                                </td>
                                <td>
                                    <?php echo form_dropdown('', array('' => lang('global:select-all')), '', 'id="option-values-select"'); ?>
                                </td>
                                <td>
                                    <a class="btn green" id="add-option" style="min-width: 60px;"><?php echo lang('store:add_row'); ?></a>
                                </td>
                                <td>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </fieldset>
            </div>
        </div>
        <div class="buttons">
            <?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel'))); ?>
        </div>

        <?php echo form_close(); ?>
    </div>
</section>
