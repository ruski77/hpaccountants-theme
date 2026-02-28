(function ($) {
	'use strict';

	$(function () {
		var $fileId    = $('#hp_file_id');
		var $fileName  = $('#hp-file-name');
		var $selectBtn = $('#hp-select-file');
		var $removeBtn = $('#hp-remove-file');

		function toggleUI() {
			if ($fileId.val()) {
				$fileName.show();
				$removeBtn.show();
			} else {
				$fileName.hide();
				$removeBtn.hide();
			}
		}

		toggleUI();

		$selectBtn.on('click', function (e) {
			e.preventDefault();

			var frame = wp.media({
				title:    'Select Download File',
				button:   { text: 'Use this file' },
				multiple: false
			});

			frame.on('select', function () {
				var attachment = frame.state().get('selection').first().toJSON();
				$fileId.val(attachment.id);
				$fileName.text(attachment.filename).show();
				$removeBtn.show();
			});

			frame.open();
		});

		$removeBtn.on('click', function (e) {
			e.preventDefault();
			$fileId.val('');
			$fileName.text('').hide();
			$removeBtn.hide();
		});
	});
})(jQuery);
