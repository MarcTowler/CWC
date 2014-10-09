<table>
    <thead>
        <tr>
            <th width="20"><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all')); ?></th>
            <th><?php echo lang('store:name'); ?></th>
            <th class="collapse"><?php echo lang('store:category'); ?></th>
            <th class="collapse"><?php echo lang('store:date'); ?></th>
            <th class="collapse"><?php echo lang('store:price'); ?></th>
            <th class="collapse" style="width: 140px;"><?php echo lang('store:status'); ?></th>
            <th  style="width: 240px;"></th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan="7">
                <div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
            </td>
        </tr>
    </tfoot>
    <tbody>
        <?php foreach ($products as $post) : ?>
            <tr>
                <td><?php echo form_checkbox('action_to[]', $post->id); ?></td>
                <td><?php echo anchor('store/product/' . $post->slug, $post->name, 'target="_blank"'); ?></td>
                <td class="collapse"><?php echo $post->category_name; ?></td>
                <td class="collapse"><?php echo format_date(mysql_to_unix($post->created)); ?></td>
                <td class="collapse"><?php echo $post->price_tax; ?></td>
                <td class="collapse"><?php
        echo form_dropdown('status', array(
            'in_stock' => lang('store:status_instock'),
            'soon_available' => lang('store:status_soon'),
            'out_of_stock' => lang('store:status_outofstock'),
            'pre_order' => lang('store:status_preorder'),
            'disabled' => lang('store:status_disabled'),
                ), $post->status, 'style="width: 100px;" autocomplete="off" class="ajax" data-rel="' . $post->id . '"');
            ?></td>
                <td style="text-align: right;">
                    <?php // echo anchor('admin/store/options/product/' . $post->id, lang('store:options'), 'class="button green"'); ?>
                    <?php echo anchor('admin/store/products/duplicate/' . $post->id, lang('store:duplicate'), 'class="btn green"'); ?>
                    <?php echo anchor('admin/store/products/edit/' . $post->id, lang('global:edit'), 'class="btn orange edit"'); ?>
                    <?php echo anchor('admin/store/products/delete/' . $post->id, lang('global:delete'), array('class' => 'confirm btn red delete')); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="table_action_buttons">
    <?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
</div>