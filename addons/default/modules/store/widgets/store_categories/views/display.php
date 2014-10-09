<?php if ($build_categories): ?>
    <ul>
        <?php echo $build_categories; ?>
        <?php //echo tree_builder($store_categories, '<li {{ if current_store == slug }}class="current"{{ endif }}><a href="{{ url:site }}store/category/{{ uri }}" alt="{{ id }}">{{ name }}</a>{{ children }}</li>'); ?>
    </ul>
<?php endif; ?>