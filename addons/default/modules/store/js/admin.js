jQuery(function($){

    // generate a slug when the user types a title in
    pyro.generate_slug('input[name="name"]', 'input[name="slug"]');
    
    $('select.ajax').live('change', function() {
        $.post(SITE_URL+'admin/store/products/status', {
            product_id: $(this).attr('data-rel'),
            status: $(this).val()
        }, function(data){
            //console.log(data);
            $('#content-body').prepend(data);
        }, 'html');
    });
});
