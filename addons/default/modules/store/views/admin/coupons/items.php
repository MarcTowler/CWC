
<section class="title">
    <h4><?php echo lang('store:coupons'); ?></h4>
</section>

<?php echo form_open_multipart(uri_string() . '/delete', 'class="crud"'); ?>


<section class="item">
    <div class="content">
        <?php if (empty($items)): ?>
            <div class="no_data"><?php echo lang('store:no_items'); ?></div>
        <?php else: ?>

            <table>		    
                <thead>
                    <tr>
                        <th><input type="checkbox" name="action_to_all" value="" class="check-all" /></th>
                        <th><?php echo lang('store:name'); ?></th>
                        <th><?php echo lang('store:code'); ?></th>
                        <th><?php echo lang('store:uses_left'); ?></th>
                        <th><?php echo lang('store:value'); ?></th>
                        <th><?php echo lang('store:date_end'); ?></th>
                        <th style="width: 120px"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items AS $item): ?>
                        <tr>
                            <td><input type="checkbox" name="action_to[]" value="<?php echo $item->id; ?>"  /></td>
                            <td><?php echo $item->name; ?></td>
                            <td><?php echo $item->code; ?></td>
                            <td><?php echo $item->uses_limit - $item->used; ?></td>
                            <td><?php echo $item->type == 'percent' ? '-' . $item->amount . ' %' : '-' . $item->amount . ' ' . Settings::get('currency'); ?></td>
                            <td><?php echo $item->date_end > time() ? timespan(time(), $item->date_end) : lang('store:ended'); ?></td>
                            <td>
                                <a class="button small" href="<?php echo site_url('admin/store/coupons/edit/' . $item->id); ?>"><?php echo lang('global:edit'); ?></a>
                                <a class="button small confirm" href="<?php echo site_url('admin/store/coupons/delete/' . $item->id); ?>"><?php echo lang('global:delete'); ?></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7"><div style="float:right;"></div></td>
                    </tr>
                </tfoot>
            </table>

            <div class="buttons">
                <?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php echo form_close(); ?>

<?php if (isset($pagination)): ?>
    <?php echo $pagination; ?>
<?php endif; ?>