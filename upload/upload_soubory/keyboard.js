define(['jquery'], function($) {

	var module = {};
	var keys = [];

	$(document).keydown(function(e) {
		keys[e.which] = true;
	});

	$(document).keyup(function(e) {
		keys[e.which] = false;
	});


	module.CTRL = 17;
	module.SHIFT = 16;
	module.ALT = 18;

	module.isPressed = function (keyCode) {
		return keys[keyCode];
	}

	module.reset = function () {
		keys = [];
	}

	return module;

});
