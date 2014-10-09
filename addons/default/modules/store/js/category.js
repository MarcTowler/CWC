
$(document).ready(function() {
    $('a.ajax').live('click', function(){

            if ($(this).hasClass('add')) {
                $('#link-list a').removeClass('selected');
            }
            // Load the form
            $('div#link-details').load($(this).attr('href'), '', function(){
                $('div#link-details').fadeIn();
                // display the create/edit title in the header
                //var title = $('#title-value').html();
                //$('section.box .title h4.group-title-'+id).html(title);

                // Update Chosen
                pyro.chosen();
                pyro.init_ckeditor();

                pyro.generate_slug('input[name="name"]', 'input[name="slug"]');
            });
            return false;
        });

        // submit create form via ajax
        $('#category-create button:submit').live('click', function(e){
            e.preventDefault();
            $.post(SITE_URL + 'admin/store/categories/create', $('#category-create').serialize(), function(message){

                // if message is simply "success" then it's a go. Refresh!
                if (message == 'success') {
                    window.location.href = window.location
                }
                else {
                    $('.notification').remove();
                    $('div#content-body').prepend(message);
                    // Fade in the notifications
                    $(".notification").fadeIn("slow");
                }
            });
        });

        // submit edit form via ajax
        $('#category-edit button:submit').live('click', function(e){
            e.preventDefault();
            $.post(SITE_URL + 'admin/store/categories/edit/' + $('input[name="link_id"]').val(), $('#category-edit').serialize(), function(message){

                // if message is simply "success" then it's a go. Refresh!
                if (message == 'success') {
                    window.location.href = window.location
                }
                else {
                    $('.notification').remove();
                    $('div#content-body').prepend(message);
                    // Fade in the notifications
                    $(".notification").fadeIn("slow");
                }
            });

        });

        // show link details
        $('#link-list li a').livequery('click', function()
        {
            var id = $(this).attr('rel');
            link_id = $(this).attr('alt');
            $('#link-list a').removeClass('selected');
            $(this).addClass('selected');

            // Load the details box in
            $('div#link-details').load(SITE_URL + 'admin/store/categories/ajax_link_details/' + link_id, '', function(){
                $('div#link-details').fadeIn();
            });
            // Remove the title from the form pane.
            $('section.box .title h4').html('');

            return false;
        });

        $('.box:visible ul.sortable').livequery(function(){
            $item_list		= $(this);
            $url			= 'admin/store/categories/order';
            $cookie			= 'open_links';
            $data_callback	= function(event, ui) {
                // Grab the group id so we can update the right links
                return {
                    'group' : ui.item.parents('section.box').attr('rel')
                };
            }
            // $post_callback is available but not needed here

            // Get sortified
            pyro.sort_tree($item_list, $url, $cookie, $data_callback);
        });
});