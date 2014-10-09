<section class="title">
    <h4><?php echo lang('store:available_list'); ?></h4>
</section>

<section class="item">
    <div class="content">
        <?php echo form_open('admin/store/options/delete'); ?>

        <?php if (!empty($items)): ?>

            <table>
                <thead>
                    <tr>
                        <th><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all')); ?></th>
                        <th><?php echo lang('store:name'); ?></th>
                        <th><?php echo lang('store:values'); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $option): ?>
                        <tr>
                            <td><?php echo form_checkbox('action_to[]', $option->id); ?></td>
                            <td><?php echo $option->title; ?></td>
                            <td>
                                <?php foreach ($option->values as $value): ?>
                                    <?php echo $value->label . ', '; ?>
                                <?php endforeach; ?>
                            </td>
                            <td class="actions">
                                <?php
                                echo
                                anchor('admin/store/options/edit/' . $option->id, lang('store:edit'), 'class="button"') . ' ' .
                                anchor('admin/store/options/delete/' . $option->id, lang('store:delete'), array('class' => 'button'));
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="table_action_buttons">
                <?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
                <?php if ($this->method == "index"): ?>
                    <?php echo anchor('admin/store/options/create/', lang('store:new_option'), 'class="btn orange"'); ?>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <div class="no_data">
                <?php echo lang('store:no_items'); ?>
            </div>
            <div class="table_action_buttons">
                <?php if ($this->method == "index"): ?>
                    <?php echo anchor('admin/store/options/create/', lang('store:new_option'), 'class="btn orange"'); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php echo form_close(); ?>
    </div>
</section>
