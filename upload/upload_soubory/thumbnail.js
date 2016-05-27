define(['jquery'], function ($) {

	var module = {
		interval: 500,
		count: 10
	};

	var currentTimer = null;
	var currentElement = null;
	var currentIndex = 0;
	var initialUrl = null;
	var baseUrl = null;

	var start = function (el) {
		if (currentTimer) {
			stop();
		}

		initialUrl = el.attr('src');
		baseUrl = initialUrl.substring(0, (initialUrl.length - 6)); // odmazeme ".0.jpg"
		currentElement = el;
		currentIndex = -1;

		for (i = 0; i <= 9; i++) {
			currentElement.attr('src', baseUrl + "." + i + ".jpg");
		}
		next();
	};

	var stop = function () {
		if (currentTimer !== null) {
			clearTimeout(currentTimer);
			currentTimer = null;
		}
		if (currentElement) {
			currentElement.attr('src', initialUrl);
		}
	};

	var next = function () {
		currentIndex = (currentIndex + 1) % 10;
		currentElement.attr('src', baseUrl + "." + currentIndex + ".jpg");
		currentTimer = setTimeout(function () {
			next();
		}, module.interval);
	};

	module.register = function () {
		$(document).on('mouseenter', '.thumbVideo img, .liveTopPreview  img', function (e) {
			start($(this));
		});
		$(document).on('mouseenter', '.thumbVideo em.overlay', function (e) {
			start($(this).siblings('img'));
		});
		$(document).on('mouseleave', '.thumbVideo img, .liveTopPreview img, .thumbVideo em.overlay', function (e) {
			stop();
		});
	};

	return module;
});
