jQuery(document).ready(function ($) {

	var i = $('.plumba_ui_dynamic').size() + 1;

	$('#plumba_metabox_questions_add').click(function () {
		$('<div class="plumba_ui_div"><input type="text" class="text plumba_ui_dynamic" name="plumba_ui_dynamic[]" /><select name="plumba_ui_color[]">' + plumba_option_colors + '</select></div>').fadeIn('slow').appendTo('#plumba_metabox_questions_ui');
		i++;
		$('.plumba_ui_dynamic:last').focus();
	});

	$('#plumba_metabox_questions_remove').click(function () {
		if ( i > 1 ) {
			$('.plumba_ui_div:last').remove();
			i--;
			$('.plumba_ui_dynamic:last').focus();
		}
	});

	$('#plumba_metabox_questions_reset').click(function () {
		while ( i > 1 ) {
			$('.plumba_ui_div:last').remove();
			i--;
		}
	});

});
