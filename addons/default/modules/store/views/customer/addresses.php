<h2 id="page_title"><?php echo lang('store:addresses_title'); ?></h2>
<?php
if (validation_errors()) {
    echo validation_errors();
}
?>
<div class="address_info">
    <?php if (count($items)) : ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th><?php echo lang('store:name'); ?></th>
                    <th><?php echo lang('store:company'); ?></th>
                    <th><?php echo lang('store:email'); ?></th>
                    <th><?php echo lang('store:address'); ?></th>
                    <th><?php echo lang('store:action'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo $item->id; ?></td>
                    <td><?php echo $item->first_name.' '.$item->last_name; ?></td>
                    <td><?php echo $item->company; ?></td>
                    <td><?php echo $item->email; ?></td>
                    <td><?php echo $item->address1.' '.$item->address2.', '.$item->zip. ' '. $item->city; ?></td>
                    <td>
                        <?php echo anchor('store/customer/address/'.$item->id, lang('global:edit'), 'class="button"'); ?>
                        <?php echo anchor('store/customer/delete_address/'.$item->id, lang('global:delete'), 'class="confirm button"'); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>
        <div><?php echo lang('store:no_items'); ?></div>
    <?php endif; ?>
        
    <p>
        <a href="{{ url:site }}store/customer" class="button"><?php echo lang('store:back_to_customer'); ?></a>
        <a href="{{ url:site }}store/customer/address" class="button"><?php echo lang('store:new_address_title'); ?></a>
    </p>
</div>