<section class="title">
    <h4><?php echo lang('store:affiliate'); ?></h4>
</section>

<section class="item">
    <div class="tabs">

        <ul class="tab-menu">
            <li><a href="#users-tab"><span><?php echo lang('store:users_label'); ?></span></a></li>
            <li><a href="#groups-tab"><span><?php echo lang('store:groups_label'); ?></span></a></li>
        </ul>

        <div class="form_inputs" id="users-tab">
            <?php if (!empty($users)): ?>

                <?php echo form_open('admin/store/affiliate/delete/user'); ?>
                <fieldset>
                    <table>
                        <thead>
                            <tr>
                                <th><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all')); ?></th>
                                <th><?php echo lang('store:name'); ?></th>
                                <th><?php echo lang('store:reduction'); ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <td colspan="4">
                                    <div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
                                </td>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php foreach ($users as $item): ?>
                                <tr>
                                    <td><?php echo form_checkbox('action_to[]', $item->user_id); ?></td>
                                    <td><?php echo $item->name; ?></td>
                                    <td><?php echo $item->discount; ?> %</td>
                                    <td class="actions">
                                        <?php
                                        echo
                                        anchor('admin/store/affiliate/delete/user/' . $item->user_id, lang('store:delete'), array('class' => 'button'));
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="table_action_buttons">
                        <?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
                    </div>
                </fieldset>

                <?php echo form_close(); ?>

            <?php else: ?>
            <fieldset>
                <div class="no_data"><?php echo lang('store:no_items'); ?></div>
                </fieldset>
            <?php endif; ?>
        </div>
        <div class="form_inputs" id="groups-tab">
            <?php if (!empty($groups)): ?>

                <?php echo form_open('admin/store/affiliate/delete/group'); ?>
                <fieldset>
                    <table>
                        <thead>
                            <tr>
                                <th><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all')); ?></th>
                                <th><?php echo lang('store:name'); ?></th>
                                <th><?php echo lang('store:reduction'); ?></th>
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
                            <?php foreach ($groups as $item): ?>
                                <tr>
                                    <td><?php echo form_checkbox('action_to[]', $item->group_id); ?></td>
                                    <td><?php echo $item->name; ?></td>
                                    <td><?php echo $item->discount; ?> %</td>
                                    <td class="actions">
                                        <?php
                                        echo
                                        anchor('admin/store/affiliate/delete/group/' . $item->group_id, lang('store:delete'), array('class' => 'button'));
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="table_action_buttons">
                        <?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
                    </div>
                </fieldset>

                <?php echo form_close(); ?>

            <?php else: ?>
            <fieldset>
                <div class="no_data"><?php echo lang('store:no_items'); ?></div>
                </fieldset>
            <?php endif; ?>
        </div>
</section>