(function ($) {
	'use strict';

	$(document).on('click', '.hp-download-link', function () {
		var postId = $(this).data('post-id');

		if (!postId) {
			return;
		}

		// Track the download view without preventing the default action.
		$.ajax({
			url:      hp_downloads.ajax_url,
			type:     'POST',
			dataType: 'json',
			data: {
				action:  'hp_track_download',
				nonce:   hp_downloads.nonce,
				post_id: postId
			}
		});
	});

})(jQuery);
