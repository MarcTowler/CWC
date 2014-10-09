<div class="one_half">
    <section class="title">
        <h4><?php echo lang('store:selected_list'); ?></h4>
    </section>

    <section class="item">
    <div class="content">
        <?php echo form_open('admin/store/options/remove/' . $product_id); ?>
        <?php echo form_hidden('product_id', $product_id); ?>

        <?php if (!empty($items)): ?>

            <table class="table-list">
                <thead>
                    <tr>
                        <th><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all')); ?></th>
                        <th><?php echo lang('store:name'); ?></th>
                        <th><?php echo lang('store:values'); ?></th>
                        <th></th>
    <!--                        <th></th>-->
                    </tr>
                </thead>
                <tbody class="sortable">
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo form_checkbox('action_to[]', $item->id); ?></td>
                            <td><?php echo $item->title; ?></td>
                            <td>
                                <?php foreach ($item->values as $value): ?>
                                    <?php echo $value->label . ', '; ?>
                                <?php endforeach; ?>
                            </td>
                            <td width="30" class="handle"><?php echo Asset::img('icons/drag_handle.gif', 'Drag Handle'); ?></td>
        <!--                            <td class="actions">
                            <?php echo anchor('admin/store/options/remove/' . $product_id . '/' . $item->id, lang('store:delete'), array('class' => 'button')); ?>
                            </td>-->
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="table_action_buttons">
                <?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
                <?php echo anchor('admin/store/products/', lang('buttons:close'), 'class="btn red cancel"'); ?>
            </div>

        <?php else: ?>
            <div class="no_data">
                <?php echo lang('store:no_items'); ?>
            </div>
            <div class="table_action_buttons">
                <?php echo anchor('admin/store/products/', lang('buttons:close'), 'class="btn red cancel"'); ?>
            </div>
        <?php endif; ?>
        <?php echo form_close(); ?>
        </div>
    </section>
</div>
<div class="one_half last">
    <section class="title">
        <h4><?php echo lang('store:available_list'); ?></h4>
    </section>

    <section class="item">

    <div class="content">
        <?php if (!empty($options)): ?>

            <table>
                <thead>
                    <tr>
                        <th><?php echo lang('store:name'); ?></th>
                        <th><?php echo lang('store:values'); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($options as $option): ?>
                        <tr>
                            <td><?php echo $option->title; ?></td>
                            <td>
                                <?php foreach ($option->values as $value): ?>
                                    <?php echo $value->label . ', '; ?>
                                <?php endforeach; ?>
                            </td>
                            <td class="actions">
                                <?php echo anchor('admin/store/options/append/' . $product_id . '/' . $option->id, lang('global:add'), 'class="btn green"'); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php else: ?>
            <div class="no_data">
                <?php echo lang('store:no_items'); ?>
            </div>
        <?php endif; ?>
        </div>
    </section>
</div>
