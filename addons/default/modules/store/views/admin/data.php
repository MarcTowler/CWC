<section class="title">
    <h4>Export Data</h4>
</section>

<section class="item">
    <div class="content">
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th width="300"></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Products</td>
                <td>
                    <?php echo anchor('admin/store/data/export/products/xml', 'Export XML', 'class="button"'); ?>
                    <?php echo anchor('admin/store/data/export/products/csv', 'Export CSV', 'class="button"'); ?>
                    <?php echo anchor('admin/store/data/export/products/json', 'Export JSON', 'class="button"'); ?>
                </td>
            </tr>
            <tr>
                <td>Categories</td>
                <td>
                    <?php echo anchor('admin/store/data/export/categories/xml', 'Export XML', 'class="button"'); ?>
                    <?php echo anchor('admin/store/data/export/categories/csv', 'Export CSV', 'class="button"'); ?>
                    <?php echo anchor('admin/store/data/export/categories/json', 'Export JSON', 'class="button"'); ?>
                </td>
            </tr>
            <tr>
                <td>Orders</td>
                <td>
                    <?php echo anchor('admin/store/data/export/orders/xml', 'Export XML', 'class="button"'); ?>
                    <?php echo anchor('admin/store/data/export/orders/csv', 'Export CSV', 'class="button"'); ?>
                    <?php echo anchor('admin/store/data/export/orders/json', 'Export JSON', 'class="button"'); ?>
                </td>
            </tr>
        </tbody>
    </table>
        </div>
</section>
