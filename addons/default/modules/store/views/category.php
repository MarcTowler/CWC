<h2 id="page_title"><?php echo isset($title) ? $title : lang('store:store_title'); ?></h2>
<div class="select-sort">
    <?php echo form_open(uri_string(), array('method' => 'get', 'id' => 'sort_form')); ?>
    <?php echo form_dropdown('sort', $sorting_options, $sort, 'id="sort_method"'); ?>
    <?php echo form_close(); ?>
    <script>
        $(document).ready(function() {
            $('#sort_method').change(function() {
                $('#sort_form').submit();
            });
        })
    </script>
</div>
<?php if (count($subcategories)): ?>
<div class="subcategories-list">
    <ul>
        {{ subcategories }}
        <li><a href="{{ url:site }}store/category/{{ uri }}">{{ name }}</a></li>
        {{ /subcategories }}
    </ul>
</div>
<hr />
<?php endif; ?>
<div class="products-container">
    <?php echo load_subview('product_list', array('items' => $items)); ?>
</div>