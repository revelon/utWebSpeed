define(['jquery'], function ($) {
	var module = {};

	var $holder = null;
	var $caret = null;

	var isBodyFocused = function () {
		return document.activeElement.tagName.toLowerCase() === 'body';
	};

	var isKeyForWindow = function (e) {
		return (
			((e.ctrlKey || e.metaKey) && e.which === 67)	// CTRL + C, CMD + C
			|| ((e.ctrlKey || e.metaKey) && e.which === 45)	// CTRL + INSERT, CMD + INSERT
			|| [
				16, // SHIFT
				17,	// CTRL
				18,	// ALT
				27,	// ESC
				33,	// PAGE UP
				34,	// PAGE DOWN
				38,	// UP ARROW
				40	// DOWN ARROW
			].indexOf(e.which) >= 0
		);
	};

	var keyPressed = function (e) {
		if (!isKeyForWindow(e) && isBodyFocused()) {
			$holder.focus();
			disable();
		}
	};

	var enable = function () {
		if (!$holder || $holder.length == 0) {
			return;
		}

		if (!$caret || !$caret.is(':visible')) {
			if (!$caret) {
				$caret = $('<div id="fakeCaret"></div>');
				$('body').append($caret);
			}

			var position = $holder.offset();
			$caret.css('left', position.left + 'px');
			$caret.css('top', position.top + 'px');
			$caret.show();

			$(document)
				.on('keydown.nodusAutofocus', keyPressed)
				.on('blur.nodusAutofocus', disable)
				.on('focus.nodusAutofocus', disable)
				.on('mousedown.nodusAutofocus', disable)
				.on('touchstart.nodusAutofocus', disable);
		}
	};

	var disable = function (e) {
		if (e && e.target == document) {
			return; // FF na OSX vola focus na document behem pageloadu
		}
		if ($caret) {
			$caret.hide();
			$(document).off('.nodusAutofocus');
		}
	};

	/**
	 * Nastavi element, kteremu se bude predavat focus pri stisku klavesy, nema-li focus jiny prvek
	 *
	 * @param string selector
	 */
	module.setDefaultFocusTo = function (selector) {

		$holder = $(selector).first();

		$(document).ready(function () {
			if (isBodyFocused()) {
				enable();
			}
		});
	};

	/**
	 * Nastavi default predavani focusu na vyhledavaci pole
	 */
	module.makeSearchDefault = function () {
		module.setDefaultFocusTo('#search-input');
	};

	return module;
});
