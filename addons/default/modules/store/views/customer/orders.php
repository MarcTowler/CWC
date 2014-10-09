<h2 id="page_title"><?php echo lang('store:orders_title'); ?></h2>

<div class="store-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th><?php echo lang('store:date_placed'); ?></th>
                <th><?php echo lang('store:price'); ?></th>
                <th><?php echo lang('store:billing_address_title'); ?></th>
                <th><?php echo lang('store:items'); ?></th>
                <th><?php echo lang('store:action'); ?></th>
            </tr>
        </thead>
        <tbody>
            {{ items }}
            <tr>
                <td>{{ id }}</td>
                <td>{{ helper:date format="d/m/Y" timestamp=order_date }}</td>
                <td>{{ total }} + {{ shipping }} {{ settings:currency }}</td>
                <td>{{ billing_address }}</td>
                <td>{{ total_items }}</td>
                <td><a href="{{ url:site }}store/customer/order/{{ id }}" class="button">{{ helper:lang line="global:view" }}</a></td>
            </tr>
            {{ /items }}
        </tbody>
    </table>
    <p>
        <a href="{{ url:site }}store/customer"><?php echo lang('store:back_to_customer'); ?></a>
    </p>
</div>
