Dropzone.enqueueForUpload = false;
Dropzone.autoDiscover = false;

var tax_multiplier = (tax_rate / 100) + 1;
$(document).ready(function() {
	$('#price').blur(function() {
		var num = new Number($(this).val());
		var tax = new Number($(this).val() * tax_multiplier);
		$(this).val(num.toFixed(2));
		if ($('#link_price').is(':checked')) {
			$('#price_tax').val(tax.toFixed(2));
		}
	});
	$('#price_tax').blur(function() {
		var num = new Number($(this).val());
		var notax = new Number($(this).val() / tax_multiplier);
		$(this).val(num.toFixed(2));
		if ($('#link_price').is(':checked')) {
			$('#price').val(notax.toFixed(2));
		}
	});
	$('.priceformat').live('blur', function () {
		var num = new Number($(this).val());
		$(this).val(num.toFixed(2));
	})
	$('#link_price').change(function(e){
		if($(this).is(':checked')) {
			$('#price').blur();
		}
	});
	$.ajaxSetup({
		allowEmpty: true
	});
	$('#keywords').tagsInput({
		autocomplete_url: 'admin/keywords/autocomplete'
	});


	$('.dz-preview .remove').on("click", function() {
		test = confirm('Remove this image?');
		if (test) {
			$(this).parent().remove();
		}
	});

	$("#myDropzone").dropzone({
		url: SITE_URL + 'store/uploader/',
		paramName: "userfile",
		maxFilesize: 2,
		uploadMultiple: true,
		clickable: true,
		init: function() {
			this.on("sending", function(file, xhr, formData) {
				formData.append("csrf_hash_name", $('input[name=csrf_hash_name]').val());
				formData.append("folder", $('#folder').val());
			});
			this.on("error", function(file, msg, xhr) {
				$('div#content-body').prepend('<div class="alert error"><p>' + msg + '</p></div>');
			});
			this.on("success", function(file, msg, xhr) {
				msg = $.parseJSON(msg);
				console.log(msg);
				var item = $('<input type="hidden" name="images[]" />');
				$(item).val(msg.id);
				$(item).appendTo(file.previewElement);
				$('div#content-body').prepend('<div class="alert success"><p>' + file.name + ' uploaded successfuly</p></div>');
			});
			this.on("addedfile", function(file) {
				var close = $('<a />').text('remove').addClass('remove').on("click", function() {
					test = confirm('Remove this image?');
					if (test) {
						$(file.previewElement).remove();
					}
				});
				close.appendTo(file.previewElement);
			});
		}
	});

	$('#add-attribute').click(function() {
		var id = $("#attributes-list tr").length;
		// var content = '<li id="item_'+id+'"><label><input type="text" class="at_label" placeholder="label" value="" name="attributes['+id+'][label]"> :</label>';
		// content += '<div class="input"><input type="text" value="" class="at_value" placeholder="value" name="attributes['+id+'][value]"> ';
		// content += '<a class="btn red remove" data-row="item_'+id+'">Remove Row</a></div></li>';
		var tr = $('#attributes-list').find('tr:last').clone();
		$(tr).attr('id', 'item_'+id);
		$(tr).find('.at_label').attr('name', 'attributes['+id+'][label]');
		$(tr).find('.at_value').attr('name', 'attributes['+id+'][value]');

		console.log(tr);

		$('#attributes-list').append(tr);
		return false;
	});

	$('#attributes-list .remove').live('click', function(e) {
		var item = $(this).parents('tr');
		var test = confirm('Sure you want to delete this field?');
		if (test) {
			$(item).remove();
			sort_attributes();
		}
		return false;
	});

	$('#add-option').click(function(e) {
		//$('#option-select').val();
		if ($('#option-select').val() > 0) {
			$.post(SITE_URL+'admin/store/options/ajax', 
				{
					id:$('#option-select').val()
				},
				function(data) {
					var opt_val = $('#option-values-select').val();
					
					for (var i = 0; i < data.values.length; i++) {
						if (opt_val == '') {
							option_row(data, i);
						} else if (opt_val > 0 && opt_val == data.values[i]['id']) {
							option_row(data, i);
						}
					};

				}, 'json');
		};
		return false;
	});
	$('#option-select').change(function(e) {
		if ($(this).val() > 0) {
			$.post(SITE_URL+'admin/store/options/ajax', 
				{
					id:$('#option-select').val()
				},
				function(data) {
					$('#option-values-select').find('option').remove();
					$('#option-values-select').append($('<option value="" selected="selected">All values</option>'));
					for (var i = 0; i < data.values.length; i++) {
						var $opt = $('<option />').val(data.values[i]['id']).text(data.values[i]['label']);
						$('#option-values-select').append($opt);
						console.log(data.values[i]);
					};
					//$("#option-values-select").trigger("chosen:updated");

					pyro.chosen();

				}, 'json');
		} else {
			$('#option-values-select').find('option').remove();
			pyro.chosen();
		}
	});
	$('#options-list .optremove a.remove').live('click', function() {
		$(this).parents('tr').remove();
		return false;
	});
});

var option_template = '<tr><td class="optname"><input type="hidden" name="options[0][option_id]" value=""></td><td class="optvalue"><input type="hidden" name="options[0][value_id]" value=""></td><td class="optprice"><input type="text" name="options[0][price]" class="priceformat" value=""></td><td class="optremove"><a class="btn red remove">Remove</a></td></tr>';
	
function option_row (data, i) {

	var index = $("#options-list tr").length;
	if(!$('#options-list tr[data-value="'+data.values[i]['id']+'"]').get(0)) {
		var $tr = $(option_template);
		$tr.attr('data-option', data.id).attr('data-value', data.values[i]['id']);
		$tr.find('.optname').append(data.title);
		$tr.find('.optname input').attr('name', 'options['+index+'][option_id]').val(data.id);

		$tr.find('.optvalue').append(data.values[i]['label']);
		$tr.find('.optvalue input').attr('name', 'options['+index+'][value_id]').val(data.values[i]['id']);

		
		$tr.find('.optprice input').attr('name', 'options['+index+'][price]').val("0.00");
		console.log(data.values[i]);
		$('#options-list').append($tr);
	}
}


function sort_attributes() {
	$("#attributes-list tr").each(function(index) {
		$(this).attr('id', 'item_'+index);
		$(this).find('.at_label').attr('name', 'attributes['+index+'][label]');
		$(this).find('.at_value').attr('name', 'attributes['+index+'][value]');
		$(this).find('.remove').attr('data-row', 'item_'+index);
	});
}

function selectImage(id) {
	$('#image_id').val(id);
	$('#image_thumb').html('<img src="'+BASE_URL+'files/thumb/'+id+'/50/50" />');
	$.colorbox.close();
}