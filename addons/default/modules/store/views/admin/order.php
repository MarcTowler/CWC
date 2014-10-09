<section class="title">
    <h4><?php echo lang('store:dashboard'); ?></h4>
</section>

<section class="item">
    <div class="content">
        <div class="tabs">

            <ul class="tab-menu">
                <li><a href="#order-tab"><span>Order</span></a></li>
                <li><a href="#billing-tab"><span>Billing</span></a></li>
                <li><a href="#delivery-tab"><span>Delivery</span></a></li>
                <li><a href="#contents-tab"><span>Contents</span></a></li>
                <li><a href="#status-tab"><span>Status History</span></a></li>
                <li><a href="#transactions-tab"><span>Transactions</span></a></li>
            </ul>
            <div id="order-tab" class="form_inputs">
                <fieldset>
                    <ul>
                        <?php if ($order->user_id && $customer): ?>
                            <li>
                                <div class="info">User</div>
                                <div class="value"><?php echo anchor('user/' . $customer->id, $customer->display_name); ?></div>
                            </li>
                        <?php endif; ?>
                        <li>
                            <div class="info">Amount</div>
                            <div class="value">
                                <?php echo ($order->total + $order->shipping); ?><br />
                                <small>includes <?php echo $order->shipping; ?> shipping value</small>
                            </div>
                        </li>
                        <li>
                            <div class="info">Order Placed</div>
                            <div class="value">
                                <?php echo date('Y-m-d', $order->order_date) . ' - ' . timespan($order->order_date) . ' ago'; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">Payment Method</div>
                            <div class="value">
                                <?php echo $order->payment_method_name; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">Shipping Method</div>
                            <div class="value">
                                <?php echo $order->shipment_method_name; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">Tracking URL</div>
                            <div class="value">
                                <?php if (isset($order->tracking_url)): ?>
                                    <?php echo anchor($order->tracking_url); ?>
                                <?php else: ?>
                                    <?php echo form_open('admin/store/update/' . $order->id); ?>
                                    <?php echo form_input('shipping_code', ''); ?>
                                    <?php echo form_submit('', 'Add shipping code'); ?>
                                    <?php echo form_close(); ?>
                                <?php endif; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">Token</div>
                            <div class="value">
                                <?php if ($order->token): ?>
                                    
                                    <?php echo form_open('admin/store/update/' . $order->id); ?>
                                    <?php echo form_hidden('token', ''); ?>
                                <span><?php echo anchor('store/payments/process/' . $order->token, 'Payment Link', 'title="'.$order->token.'" class="tooltip"'); ?></span>
                                    <?php echo form_submit('', 'Expire'); ?>
                                    <?php echo form_close(); ?>
                                <?php else: ?>
                                    Expired
                                    <?php echo form_open('admin/store/update/' . $order->id); ?>
                                    <?php echo form_hidden('token', random_string('unique')); ?>
                                    <?php echo form_submit('', 'Renew'); ?>
                                    <?php echo form_close(); ?>
                                <?php endif; ?>

                            </div>
                        </li>
                        <li>
                            <div class="info">Session ID</div>
                            <div class="value">
                                <?php echo $order->session_id; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">IP Address</div>
                            <div class="value">
                                <?php echo $order->ip_address; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">User Agent</div>
                            <div class="value">
                                <?php echo $order->user_agent; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">Last Activity</div>
                            <div class="value">
                                <?php echo $order->last_activity; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">Fraud Risk Score</div>
                            <div class="value">
                                <?php echo $order->fraud_score; ?>
                            </div>
                        </li>
                    </ul>
                </fieldset>
            </div>
            <div id="billing-tab" class="form_inputs">
                <fieldset>
                    <ul>
                        <li>
                            <div class="info">E-mail</div>
                            <div class="value">
                                <?php echo $invoice->email; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">First Name</div>
                            <div class="value">
                                <?php echo $invoice->first_name; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">Last Name</div>
                            <div class="value">
                                <?php echo $invoice->last_name; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">Company</div>
                            <div class="value">
                                <?php echo $invoice->company; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">CID / NIP</div>
                            <div class="value">
                                <?php echo $invoice->nip; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">Address</div>
                            <div class="value">
                                <?php echo $invoice->address1; ?><br />
                                <?php echo $invoice->address2; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">City</div>
                            <div class="value">
                                <?php echo $invoice->city; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">State</div>
                            <div class="value">
                                <?php echo $invoice->state; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">Country</div>
                            <div class="value">
                                <?php echo $invoice->country; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">Zip</div>
                            <div class="value">
                                <?php echo $invoice->zip; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">Phone</div>
                            <div class="value">
                                <?php echo $invoice->phone; ?>
                            </div>
                        </li>
                    </ul>
    <!--                <pre>
                    <?php print_r($invoice); ?>
                    </pre>-->
                </fieldset>
            </div>
            <div id="delivery-tab" class="form_inputs">
                <fieldset>
                    <ul>
                        <li>
                            <div class="info">E-mail</div>
                            <div class="value">
                                <?php echo $shipping->email; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">First Name</div>
                            <div class="value">
                                <?php echo $shipping->first_name; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">Last Name</div>
                            <div class="value">
                                <?php echo $shipping->last_name; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">Company</div>
                            <div class="value">
                                <?php echo $shipping->company; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">CID / NIP</div>
                            <div class="value">
                                <?php echo $shipping->nip; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">Address</div>
                            <div class="value">
                                <?php echo $shipping->address1; ?><br />
                                <?php echo $shipping->address2; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">City</div>
                            <div class="value">
                                <?php echo $shipping->city; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">State</div>
                            <div class="value">
                                <?php echo $shipping->state; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">Country</div>
                            <div class="value">
                                <?php echo $shipping->country; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">Zip</div>
                            <div class="value">
                                <?php echo $shipping->zip; ?>
                            </div>
                        </li>
                        <li>
                            <div class="info">Phone</div>
                            <div class="value">
                                <?php echo $shipping->phone; ?>
                            </div>
                        </li>
                    </ul>
    <!--                <pre>
                    <?php print_r($shipping); ?>
                    </pre>-->
                </fieldset>
            </div>
            <div id="contents-tab" class="form_inputs">
                <fieldset>
                    <table>
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contents as $item): ?>
                                <tr>
                                    <td><?php echo img(site_url('files/thumb/' . $item->cover_id)); ?></td>
                                    <td><?php echo anchor('store/product/' . $item->slug, $item->name); ?></td>
                                    <td><?php echo $item->qty; ?></td>
                                    <td><?php echo $item->price_per; ?></td>
                                    <td><?php echo ($item->qty * $item->price_per); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </fieldset>
            </div>
            <div id="status-tab" class="form_inputs">
                <fieldset>
                    <!--  ORDER HISTORY  -->
                    <table style="width: 100%">
                        <thead>
                            <tr>
                                <th><?php echo lang('store:date'); ?></th>
                                <th><?php echo lang('store:status'); ?></th>
                                <th><?php echo lang('store:comment'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($history as $item): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', $item->date_added); ?></td>
                                    <td><?php echo $item->status; ?></td>
                                    <td><?php echo $item->comment; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <h4>Nowy status</h4>

                    <?php echo form_open('admin/store/history'); ?>
                    <?php echo form_hidden('order_id', $order->id); ?>
                    <div class="form_inputs">
                        <ul>
                            <li>
                                <label>
                                    <?php echo lang('store:status'); ?>
                                </label>
                                <div class="input">
                                    <?php
                                    echo form_dropdown('status', array(
                                        'reply' => 'reply',
                                        'pending' => 'pending',
                                        'processing' => 'processing',
                                        'processed' => 'processed',
                                        'shipping' => 'shipping',
                                        'complete' => 'complete',
                                        'canceled' => 'canceled',
                                        'rejected' => 'rejected',
                                        'expired' => 'expired'));
                                    ?>
                                </div>
                            </li>
                            <li>
                                <label>
                                    <?php echo lang('store:notify'); ?>Powiadomienie e-mail
                                </label>
                                <div class="input">
                                    <label><?php echo form_radio('notify', 0, TRUE); ?>NIE </label>
                                    <label><?php echo form_radio('notify', 1); ?> TAK </label>
                                </div>
                            </li>
                            <li>
                                <label>
                                    <?php echo lang('store:comment'); ?>
                                </label>
                                <div class="input">
                                    <?php echo form_textarea(array('name' => 'comment', 'value' => set_value('comment'), 'rows' => 3)); ?>
                                </div>
                            </li>
                            <li>
                                <label>&nbsp;</label>
                                <div class="input">
                                    <?php echo form_submit('save', lang('store:save')); ?>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <?php echo form_close(); ?>
                </fieldset>
            </div>
            <div id="transactions-tab" class="form_inputs">
                <fieldset>
                    <table>
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Reason</th>
                                <th>Amount</th>
                                <th>Gateway</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="transactions-data">
                            <?php foreach ($transactions as $item): ?>
                                <tr>
                                    <td><?php echo $item->status; ?></td>
                                    <td><?php echo $item->reason; ?></td>
                                    <td><?php echo $item->amount; ?></td>
                                    <td><?php echo $item->gateway; ?></td>
                                    <td><?php echo date('Y-m-d H:i:s', $item->timestamp); ?></td>
                                </tr>
                            <?php endforeach; ?>
                                <?php echo form_open('admin/store/transaction/', 'id="transaction-form"'); ?>
                                <?php echo form_hidden('order_id', $order->id); ?>
                                <tr id="transaction-row" style="display: none">
                                    <td><?php echo form_dropdown('status', array('pending' => 'pending', 'accepted' => 'accepted', 'rejected' => 'rejected')); ?></td>
                                    <td><?php echo form_input('reason', '', 'placeholder="reason"'); ?></td>
                                    <td><?php echo form_input('amount', '', 'placeholder="amount"'); ?></td>
                                    <td><?php echo form_dropdown('gateway', $gateways); ?></td>
                                    <td><?php echo form_input('timestamp', format_date(time()), 'class="datepicker"'); ?></td>
                                </tr>
                                <?php echo form_submit('', '', 'style="display:none;"'); ?>
                                <?php echo form_close(); ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" style="text-align: right;">
                                    <a href="#" class="add-transaction btn green">Add transaction</a>
                                    <a href="#" class="submit-transaction btn orange" style="display: none">Submit transaction</a>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </fieldset>
            </div>
        </div>
        <?php echo anchor('admin/store', 'Back to orders', 'class="button"'); ?>
    </div>
</section>

<script>
    $(document).ready(function() {
        $('.add-transaction').click(function() {
            $('.submit-transaction, #transaction-row').show();
            $(this).hide();
            return false;
        });
        $('.submit-transaction').click(function() {
            $('form#transaction-form').submit();
            return false;
        });
    })
</script>
