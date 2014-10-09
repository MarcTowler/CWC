
<div class="one_half">
    <section class="title">
        <h4>Overview</h4>
    </section>
    <section class="item">
        <div class="content">
            <table>
                <?php foreach ($stats as $value): ?>
                    <tr class="<?php echo alternator('even', 'odd') ?>">
                        <td><?php echo $value['name']; ?></td>
                        <td><?php echo $value['result']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </section>
</div>

<div class="one_half last">
    <section class="chart-tabs">
        <ul class="tab-menu">
            <li class="ui-state-active"><a href="<?php echo site_url('admin/store/stats/ajax/7/items'); ?>" class="chart-data"><span>Items</span></a></li>
            <li><a href="<?php echo site_url('admin/store/stats/ajax/7/orders'); ?>" class="chart-data"><span>Orders</span></a></li>
            <li><a href="<?php echo site_url('admin/store/stats/ajax/7/income'); ?>" class="chart-data"><span>Income</span></a></li>
<!--            <li><a href="<?php echo site_url('admin/store/stats/'); ?>"><span>More &raquo;</span></a></li>-->
        </ul>
    </section>
    <section class="item">
        <div class="content">
            <div class="tabs">
                <div id="chart_div" style="width: 100%; height: 230px;"></div>
            </div>
        </div>
    </section>
</div>

<div class="one_full" style="margin-top: 20px;">
    <section class="title">
        <h4><?php echo lang('store:dashboard'); ?></h4>
    </section>

    <section class="item">
        <div class="content">
            <?php echo form_open('admin/store/delete'); ?>
            <?php if (empty($items)): ?>
                <div class="no_data"><?php echo lang('store:no_items'); ?></div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th width="20"><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all')); ?></th>
                            <th>Customer</th>
                            <th class="collapse">Delivery Address</th>
                            <th class="collapse">Date</th>
                            <th class="collapse">Amount</th>
                            <th class="collapse">Payment</th>
                            <th class="collapse">Status</th>
                            <th width="160"></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <td colspan="8">
                                <div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
                            </td>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php foreach ($items as $order) : ?>
                            <tr>
                                <td><?php echo form_checkbox('action_to[]', $order->id); ?></td>
                                <td><?php echo safe_mailto($order->customer_email, $order->customer_name, 'class="tooltip-s" title="' . $order->billing_address . '"'); ?></td>
                                <td class="collapse">
                                    <?php echo $order->shipping_address; ?>
                                </td>
                                <td class="collapse"><?php echo format_date($order->order_date); ?></td>
                                <td class="collapse"><?php echo format_price($order->total + $order->shipping); ?></td>


                                <td class="collapse"><?php echo $order->payment . '/' . $order->payment_status; ?></td>
                                <td>
                                    <p><?php echo $order->status; ?></p>
                                </td>
                                <td>
                                    <?php echo anchor('admin/store/order/' . $order->id, lang('global:preview'), 'class="button orange edit"'); ?>
                                    <?php echo anchor('admin/store/delete/' . $order->id, lang('global:delete'), 'class="button red delete confirm"'); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="table_action_buttons">
                    <?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
                </div>
            <?php endif; ?>
            <?php echo form_close(); ?>
        </div>
    </section>
</div>