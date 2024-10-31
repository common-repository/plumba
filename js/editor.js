

function plumbaSetShortCode() {

	var no_bars  = jQuery('#plumba_no_bars').is(':checked');
	var thankyou = jQuery('#plumba_thankyou').is(':checked');
	var style    = jQuery('#plumba_style').val();

	var shortcode = '[plumba id=' + jQuery('#plumba_select').val();

	console.log(no_bars);

	if (no_bars) shortcode += ' bars=0';

	if (!thankyou) shortcode += ' thankyou=0';

	if (style != 'standard') shortcode += ' style=' + style;

	shortcode += ']';

	return shortcode;

}