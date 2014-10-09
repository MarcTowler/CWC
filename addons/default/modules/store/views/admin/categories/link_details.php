<div id="details-container">	<h4>
    <?php echo $link->name; ?></h4>	 	
  <input id="link-id" type="hidden" value="<?php echo $link->id; ?>" /> 	
  <input id="link-uri" type="hidden" value="<?php echo ! empty($link->slug) ? $link->slug : ''; ?>" />                 
  <?php echo $link->image_id ? '<img src="files/thumb/'.$link->image_id.'" />' : ''; ?> 	
  <fieldset>		
    <legend>
      <?php echo lang('nav_details_label'); ?>
    </legend>		
    <p>			<strong>ID:</strong> #
      <?php echo $link->id; ?>		
    </p>		
    <p>			<strong>
        <?php echo lang('store:name');?>:</strong> 
      <?php echo $link->name; ?>		
    </p>		 		
    <p>			<strong>
        <?php echo lang('store:desc');?>:</strong> 
      <?php echo strip_tags($link->description); ?>		
    </p>		 		
    <p>			<strong>
        <?php echo lang('store:slug');?>:</strong>			
      <?php echo $link->slug; ?>		
    </p>		 		
    <p>			<strong>
        <?php echo lang('store:uri');?>:</strong>                        
      <a target="_blank" href="<?php echo site_url(); ?>store/category/<?php echo $link->uri; ?>">
        <?php echo $link->uri; ?></a>		
    </p>		 	
  </fieldset>	 	 	
  <div class="buttons">                
    <?php echo anchor('admin/store/categories/create/'. $link->id, lang('store:add_child_category'), 'class="add ajax button"') ?>		
    <?php echo anchor('admin/store/categories/edit/' . $link->id, lang('global:edit'), 'rel="'.$link->id.'" class="button ajax"'); ?>		
    <?php echo anchor('admin/store/categories/delete/' . $link->id, lang('global:delete'), 'class="confirm button"'); ?>	
  </div>
</div>