<section class="title">
    <h4><?php echo lang('store:shipments'); ?></h4>
</section>

<section class="item">
    <?php echo form_open('admin/store/shipments/delete'); ?>
    <fieldset>
    <?php if (!empty($items)): ?>

        <table>
            <thead>
                <tr>
                    <th><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all')); ?></th>
                    <th><?php echo lang('store:name'); ?></th>
                    <th><?php echo lang('store:price'); ?></th>
                    <th><?php echo lang('store:status'); ?></th>
                    <th></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="5">
                        <div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
                    </td>
                </tr>
            </tfoot>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo form_checkbox('action_to[]', $item->id); ?></td>
                        <td><?php echo $item->name; ?></td>
                        <td><?php echo $item->price; ?></td>
                        <td><?php echo $item->status; ?></td>
                        <td class="actions">
                            <?php
                            echo
                            anchor('admin/store/shipments/edit/' . $item->id, lang('store:edit'), 'class="button"') . ' ' .
                            anchor('admin/store/shipments/delete/' . $item->id, lang('store:delete'), array('class' => 'button'));
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="table_action_buttons">
            <?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
        </div>

    <?php else: ?>
        <div class="no_data"><?php echo lang('store:no_items'); ?></div>
    <?php endif; ?>
    </fieldset>
    <?php echo form_close(); ?>
</section>