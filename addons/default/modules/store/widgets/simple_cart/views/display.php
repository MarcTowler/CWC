<b><?php echo lang('store:total_items');?>: </b> <?php echo $total_items; ?><br />
<b><?php echo lang('store:total_cost');?>: </b> <?php echo $total_cost; ?> {{ settings:currency }}<br />
<a href="{{ url:site }}store/cart"><?php echo lang('store:show_cart');?> | <a href="{{ url:site }}store/checkout"><?php echo lang('store:checkout');?></a></a>