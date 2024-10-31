
//Register vote via ajax
function plumba_vote(post_id, key, nonce, thanks) {

	jQuery.ajaxSetup({ cache: false });

	//Sending request
	jQuery.post(
			ajaxurl,
			{
				action :'plumba_vote',
				post_id:post_id,
				key    :key,
				nonce  :nonce
			},
			function (response) {

				if ( thanks ) {
					jQuery('#plumba_vote_result_' + post_id).html(response);
					jQuery('#plumba_vote_result_' + post_id).addClass('alert alert-success');
					jQuery('#plumba_vote_result_' + post_id).show(response);
				}

				var id = '#plumba_vote_result_' + post_id;
				setTimeout('jQuery(\'' + id + '\').hide();', 3000);

			}
	);

}

function plumba_stats(post_id, nonce) {

	var plumba_url = ajaxurl + '?action=plumba_stats&post_id=' + post_id + '&nonce=' + nonce;

	jQuery.ajaxSetup({ cache: false });

	jQuery.getJSON(plumba_url,
			{
				format:"json"
			},
			function (data) {

				var plumba_total = 0;

				jQuery.each(data['answers'], function (i, item) {
					plumba_total += item;
				});

				jQuery.each(data['answers'], function (i, item) {

					var percent = 0;
					if ( plumba_total ) {
						percent = Math.round((item / plumba_total) * 100);
					}
					jQuery('#plumba_question_badge_' + post_id + '_' + i).html(item);
					jQuery('#plumba_question_bar_' + post_id + '_' + i).width(percent + '%');
					jQuery('#plumba_question_percent_' + post_id + '_' + i).html(percent + '%');

				});


			});


}



