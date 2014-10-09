var has_temp = false;
$(document).ready(function() {
    
    pyro.generate_slug('input[name="title"]', 'input[name="slug"]');
    
    $('.values').bind('keypress', function(e) {
        if (e.keyCode == 13) {
            $('#option-add').click();
            $('#new_label').focus();
            return false;
        }
    });
    
    
    $('#option-add').bind('click', function(e) {
        
        var label = $('#new_label').val();
        var value = $('#new_value').val();
        
        var new_label = $('<input type="hidden" name="values[label][]" class="label" />').val(label);
        var new_value = $('<input type="hidden" name="values[value][]" class="value" />').val(value);
        
        var wrapper = $('<div class="value-item" />');
        var item = '<span class="value-label">' + label + ' - ' + value + '</span>';
        
        $(wrapper).append(item);
        $(wrapper).append(new_label);
        $(wrapper).append(new_value);
        $('li.values-list .input').append(wrapper);
        $('#new_label').val('');
        $('#new_value').val('');
        
        return false;
    //alert($(this).val());
    });
    
    $('.value-item').live('click', function() {
        if (!has_temp) {
            $(this).find('span').hide();
            var label = $(this).find('input.label').val();
            var amount = $(this).find('input.value').val();
            
            
            var temp_label = $('<input type="text" class="temp_input label" />').val(label);
            var temp_value = $('<input type="text"  class="temp_input value" />').val(amount);
            
            $(this).append(temp_label);
            $(this).append(temp_value);
            $('#temp_input').focus();
            has_temp = true;
        }
    });
    
    $('.temp_input').live('keypress', function(e) {
        var parent = $(this).parent();
        
        if (e.keyCode == 13) {
            var label = $(parent).find('.temp_input.label').val();
            var amount = $(parent).find('.temp_input.value').val();
            
            $('.temp_input').remove();
            
            $(parent).find('span.value-label').text(label + ' - ' + amount);
            $(parent).find('input.label').val(label);
            $(parent).find('input.value').val(amount);
            
            $(parent).find('span').show();
            has_temp = false;
        }
        if (e.keyCode == 27) {
            $('.temp_input').remove();
            
            $(parent).find('span').show();
            has_temp = false;
        }
    });
    
    $('.icon-remove').live('click', function(e) {
        var parent = $(this).parent();
        $(parent).remove();
    });
    
    $('form').submit(function(e) {
        if ($('.values').is(':focus')) {
            e.preventDefault();
            return false;
        }
        if ($('.value-item input').is(':focus')) {
            e.preventDefault();
            return false;
        }
    })
});
(function($)
{
    $(function() {

        $('tbody.sortable').sortable({
            handle: 'td',
            helper: 'clone',
            update: function() {
                order = new Array();
                $('tr', this).each(function(){
                    order.push( $(this).find('input[name="action_to[]"]').val() );
                });
                order = order.join(',');
				
                $.post(SITE_URL+'admin/store/options/sort', 
                {
                    order: order, 
                    product: $('input[name=product_id]').val() ,  
                    csrf_hash_name: $.cookie(pyro.csrf_cookie_name)
                    }, 
                function(data) {
                    if (data == 1) {
                        $('#content-body').prepend('<div class="alert success">Akcja wykonana pomyślnie</div>')
                    } else {
                        $('#content-body').prepend('<div class="alert error">Wystąpił błąd</div>')
                    }
                });
            }
			
        }).disableSelection();
		
    });
})(jQuery);