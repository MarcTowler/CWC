<section class="title">
    <h4><?php echo lang('store:gateways'); ?></h4>
</section>

<section class="item">
    <fieldset>
        <?php if (!empty($installed)): ?>

            <table>
                <thead>
                    <tr>
                        <th><?php echo lang('store:name'); ?></th>
                        <th><?php echo lang('store:image'); ?></th>
                        <th><?php echo lang('store:desc'); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($installed as $item): ?>
                        <tr>
                            <td><?php echo $item->name; ?></td>
                            <td><?php echo $item->image ? img($item->image) : ''; ?></td>
                            <td><?php echo $item->desc; ?></td>
                            <td class="actions" style="width: 220px;">
                                <?php
                                if ($item->enabled) {
                                    echo anchor('admin/store/gateways/disable/' . $item->id, lang('global:disable'), 'class="btn green"');
                                } else {
                                    echo anchor('admin/store/gateways/enable/' . $item->id, lang('global:enable'), 'class="btn orange"');
                                };
                                ?>
                                <?php
                                echo
                                anchor('admin/store/gateways/settings/' . $item->id, lang('global:edit'), 'class="btn orange"') . ' ' .
                                anchor('admin/store/gateways/uninstall/' . $item->id, lang('global:delete'), array('class' => 'btn red confirm'));
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">
                            <div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
                        </td>
                    </tr>
                </tfoot>
            </table>

        <?php else: ?>
            <div class="no_data"><?php echo lang('store:no_items'); ?></div>
        <?php endif; ?>
    </fieldset>
</section>
<section class="title">
    <h4><?php echo lang('store:items'); ?></h4>
</section>

<section class="item">
    <fieldset>
        <?php if (!empty($uninstalled)): ?>

            <table>
                <thead>
                    <tr>
                        <th><?php echo lang('store:name'); ?></th>
                        <th><?php echo lang('store:image'); ?></th>
                        <th><?php echo lang('store:desc'); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($uninstalled as $item): ?>
                        <tr>
                            <td><?php echo $item->title; ?></td>
                            <td><?php echo $item->image ? img($item->image) : ''; ?></td>
                            <td><?php echo $item->description; ?></td>
                            <td class="actions">
                                <?php echo anchor('admin/store/gateways/install/' . $item->slug, lang('global:install'), 'class="button"'); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">
                            <div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
                        </td>
                    </tr>
                </tfoot>
            </table>

        <?php else: ?>
            <div class="no_data"><?php echo lang('store:no_items'); ?></div>
        <?php endif; ?>
    </fieldset>
</section>