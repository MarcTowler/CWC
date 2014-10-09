
<section class="title">
    <h4><?php echo lang('store:sales'); ?></h4>
</section>

<section class="item">
    <div class="content">
        <?php echo form_open_multipart(site_url('admin/store/sales/delete'), 'class="crud"'); ?>


        <?php if (empty($products)): ?>
            <div class="no_data"><?php echo lang('store:no_items'); ?></div>
        <?php else: ?>

            <table>		    
                <thead>
                    <tr>
                        <th><input type="checkbox" name="action_to_all" value="" class="check-all" /></th>
                        <th><?php echo lang('store:product'); ?></th>
                        <th><?php echo lang('store:old_price'); ?></th>
                        <th><?php echo lang('store:new_price'); ?></th>
                        <th><?php echo lang('store:reduction'); ?></th>
                        <th><?php echo lang('store:sale_type'); ?></th>
                        <th style="width: 120px"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products AS $product): ?>
                        <tr>
                            <td><input type="checkbox" name="action_to[]" value="<?php echo $product->sale_id; ?>"  /></td>
                            <td><?php echo anchor('store/product/' . $product->slug, $product->name, 'target="_blank"'); ?></td>
                            <td><span style="text-decoration: line-through"><?php echo $product->price_tax; ?></span></td>
                            <td><?php echo $product->new_price_tax; ?></td>
                            <td><?php echo $product->reduction; ?>%</td>
                            <td><?php echo lang('store:option_' . $product->sale_type); ?></td>
                            <td>
                                <a class="button small" href="<?php echo site_url('admin/store/sales/edit/' . $product->sale_id); ?>"><?php echo lang('global:edit'); ?></a>
                                <a class="button small confirm" href="<?php echo site_url('admin/store/sales/delete/' . $product->sale_id); ?>"><?php echo lang('global:delete'); ?></a>
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

        <?php echo form_close(); ?>

        <?php if (isset($pagination)): ?>
            <?php echo $pagination; ?>
        <?php endif; ?>

    </div>
</section>