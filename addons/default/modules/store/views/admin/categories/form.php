<div id="details-container">    
    <?php echo form_open(uri_string(), 'id="category-' . $this->method . '" class="form_inputs"'); ?>    
    <ul>        
        <?php if ($this->method == 'edit'): ?>            
            <?php echo form_hidden('category_id', $category->id) ?>        
        <?php endif; ?>        
        <li class="<?php echo alternator('', 'even'); ?>">            
            <label for="name"><?php echo lang('store:name'); ?> <span>*</span></label>
            <div class="input">
                <?php echo form_input('name', $category->name, 'maxlength="100" class="text"'); ?>
            </div>
        </li>        
        <li class="<?php echo alternator('', 'even'); ?>">            
            <label for="slug"><?php echo lang('store:slug'); ?> <span>*</span></label>
            <div class="input">
                <?php echo form_input('slug', $category->slug, 'maxlength="100" class="text" readonly="readonly" contenteditable="false"'); ?>
            </div>
        </li>        
        <li class="<?php echo alternator('', 'even'); ?>">            
            <label for="image"><?php echo lang('store:image'); ?> </label>
            <input type="hidden" name="image_id" value="<?php echo $category->image_id ? $category->image_id : 0; ?>" id="image_id" />
            <?php echo anchor('admin/store/ajax/image', lang('store:select_image'), 'class="modal-large button"'); ?>            
            <span id="image_thumb">
                <?php echo $category->image_id ? img('files/thumb/' . $category->image_id . '/50/50') : ''; ?>
            </span>        
        </li>        
        <li class="<?php echo alternator('', 'even'); ?>">          
                <label for="description">
                    <?php echo lang('store:desc'); ?>
                </label>
            <div class="input">     
                <?php echo form_textarea('description', $category->description, 'rows="20" id="description" cols="30" class="wysiwyg-simple" style="width: 280px; height: 150px; resize: vertical;"'); ?>            
            </div>        
        </li>        
        <li class="<?php echo alternator('', 'even'); ?>">            
            <label for="parent_id">
                <?php echo lang('store:parent'); ?>
            </label>            
            <?php echo form_dropdown('parent_id', $parents, $category->parent_id); ?>        
        </li>    
    </ul>    
    <div class="buttons float-left padding-top">        
        <?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel'))); ?>    
    </div>    
    <?php echo form_close(); ?>
</div>