define(['jquery'], function ($) {

	var module = {};

	var displayMessage = function (data, element, message) {
		var heightBefore = element.height();
		var heightAfter = 0;
		if (message) {
			element.html('<p>' + message + '</p>');

			if (typeof data.resize != 'undefined') {
				element.show();
				heightAfter = element.height();
			} else {
				element.slideDown();
			}

		} else {
			element.html('');
			if (typeof data.resize != 'undefined') {
				element.hide();
			} else {
				element.slideUp();
			}

		}
		if (typeof data.resize != 'undefined') {
			$(data.resize).css('height', ($('.window').height() + heightAfter - heightBefore) + 'px');
		}
	};

	var clear = function (data) {
		if (typeof data.clear != 'undefined') {
			$(data.clear).each(function () {
				displayMessage(data, $(this), '');
			});
		}
	};

	module.register = function () {
		$(document).on('click', '[data-message-api]', function (e) {
			var data = $.parseJSON(String($(e.target).attr('data-message-api')));
			var element = $(data.target);

			if (typeof data.text != 'undefined') {
				clear(data);
				displayMessage(data, element, data.text);
			} else if (typeof data.ajax != 'undefined') {
				element.removeAttr('data-loaded');
				$.ajax({
					type: 'GET',
					url: data.ajax,
					error: function (error) {
						element.attr('data-loaded', 'loaded');
						element.attr('data-error', 'error');
						clear(data);
						displayMessage(data, element, '');
					},
					success: function (e) {
						clear(data);
						if (e.content && e.content !== '') {
							displayMessage(data, element, e.content);
						} else {
							displayMessage(data, element, '');
						}
						element.attr('data-loaded', 'loaded');
						element.removeAttr('data-error');
					}
				});
			}
		});
	};

	return module;
});
