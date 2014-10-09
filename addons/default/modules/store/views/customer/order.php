<h2 id="page_title"><?php echo lang('store:orders_title'); ?></h2>

<div class="store-container">


    <!--  ORDER DETAILS  -->
    <table style="width: 100%">
        <tr>
            <th colspan="2"><?php echo lang('store:order_details'); ?></th>
        </tr>
        <tr>
            <td>
                {{ helper:lang line="store:order_id" }} {{ order:id }}<br />
                {{ helper:lang line="store:order_date" }} {{ helper:date format="d/m/Y" timestamp=order:order_date }}
            </td>
            <td>
                {{ helper:lang line="store:payment_method" }} {{ order:payment_method }}<br />
                {{ helper:lang line="store:shipping_method" }} {{ order:shipping_method }}
            </td>
        </tr>
    </table>

    <p>&nbsp;</p>

    <!--  ORDER ADDRESS  -->
    <table style="width: 100%">
        <tr>
            <th><?php echo lang('store:billing_address_title'); ?></th>
            <th><?php echo lang('store:delivery_address_title'); ?></th>
        </tr>
        <tr>
            <td>
                {{ invoice:first_name }} {{ invoice:last_name }}<br />
                {{ if invoice:company }} {{ invoice:company }}<br /> {{ endif }}
                {{ invoice:address1 }} {{ invoice:address2 }}<br />
                {{ invoice:zip }} {{ invoice:city }}<br />
                {{ invoice:email }}<br />
            </td>
            <td>
                {{ shipping:first_name }} {{ shipping:last_name }}<br />
                {{ if shipping:company }} {{ shipping:company }}<br /> {{ endif }}
                {{ shipping:address1 }} {{ shipping:address2 }}<br />
                {{ shipping:zip }} {{ shipping:city }}<br />
                {{ shipping:email }}<br />
            </td>
        </tr>
    </table>

    <p>&nbsp;</p>

    <!--  ITEMS  -->
    <table style="width: 100%">
        <thead>
            <tr>
                <th><?php echo lang('store:image'); ?></th>
                <th><?php echo lang('store:name'); ?></th>
                <th><?php echo lang('store:price'); ?></th>
                <th><?php echo lang('store:action'); ?></th>
            </tr>
        </thead>
        <tbody>
            {{ contents }}
            <tr>
                <td>{{ if cover_id }}<img src="{{ url:site }}files/thumb/{{ cover_id }}/75" alt="{{ name }}" /> {{ endif }}</td>
                <td>{{ qty }}x {{ name }}</td>
                <td>{{ price_per }} {{ settings:currency }}</td>
                <td><a href="{{ url:site }}store/product/{{ slug }}">{{ helper:lang line="global:view" }}</a></td>
            </tr>
            {{ /contents }}
        </tbody>
    </table>

    <hr />

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
            {{ if history }} 
            {{ history }}
            <tr>
                <td>{{ helper:date format="d/m/Y" timestamp=date_added }}</td>
                <td>{{ status }}</td>
                <td>{{ comment }}</td>
            </tr>
            {{ /history }}
            {{ else }}
            <tr>
                <td colspan="3">
                    <?php echo lang('store:no_items'); ?>
                </td>
            </tr>
            {{ endif }}
        </tbody>
        <tfoot>
            <?php echo form_open(); ?>
            <tr>
                <td>
                    <?php echo lang('store:comment'); ?>
                </td>
                <td colspan="2">
                    <?php
                    echo form_textarea(array(
                        'name' => 'comment',
                        'value' => set_value('comment'),
                        'rows' => 3
                    ));
                    ?><br />
            <?php echo form_submit('save', lang('store:save')); ?>
                </td>
            </tr>
<?php echo form_close(); ?>
        </tfoot>
    </table>
    <p>
        <a href="{{ url:site }}store/customer"><?php echo lang('store:back_to_customer'); ?></a>
    </p>
</div>
