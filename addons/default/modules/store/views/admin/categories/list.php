
<section class="categories box">
    <section class="title">
        <ul>
            <li>
                <h4 title="<?php echo lang('store:categories'); ?>"><?php echo lang('store:categories'); ?></h4>
                <?php echo anchor('admin/store/categories/create/', lang('store:new_category'), 'class="add ajax button"') ?>
            </li>
        </ul>

    </section>

    <?php if (!empty($categories)): ?>

        <section class="item collapsed">
        <div class="content">
            <div id="link-list">
                <ul class="sortable">
                    <?php echo tree_builder($categories, '<li id="link_{{ id }}"><div><a href="#" alt="{{ id }}">{{ name }}</a></div>{{ children }}</li>'); ?>
                </ul>
            </div>

            <div id="link-details" class="categories">

                <p>
                    <?php echo lang('store:explanation'); ?>
                </p>

            </div>
        </div>
        </section>

    <?php else: ?>

        <section class="item collapsed">

            <div id="link-list" class="empty">
                <ul class="sortable">

                    <p><?php echo lang('store:no_links'); ?></p>

                </ul>
            </div>

            <div id="link-details" class="categories">

                <p>
                    <?php echo lang('store:explanation'); ?>
                </p>

            </div>

        </section>
    <?php endif; ?>	

</section>
