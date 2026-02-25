(function ($) {
	'use strict';

	$(document).on('submit', '#hp-newsletter-form', function (e) {
		e.preventDefault();

		var $form     = $(this);
		var $button   = $form.find('.hp-newsletter-submit');
		var $response = $form.find('.hp-newsletter-response');
		var email     = $form.find('input[name="hp_newsletter_email"]').val();

		// Disable button while processing.
		$button.prop('disabled', true).text('Subscribing...');
		$response.html('').removeClass('hp-newsletter-success hp-newsletter-error');

		$.ajax({
			url:      hp_newsletter.ajax_url,
			type:     'POST',
			dataType: 'json',
			data: {
				action: 'hp_newsletter_subscribe',
				nonce:  hp_newsletter.nonce,
				email:  email
			},
			success: function (response) {
				if (response.success) {
					$response
						.addClass('hp-newsletter-success')
						.html(response.data.message);
				} else {
					$response
						.addClass('hp-newsletter-error')
						.html(response.data.message);
				}
			},
			error: function () {
				$response
					.addClass('hp-newsletter-error')
					.html('An error occurred. Please try again later.');
			},
			complete: function () {
				$button.prop('disabled', false).text('Subscribe');
			}
		});
	});

})(jQuery);
