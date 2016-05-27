define(['jquery', 'nodus/utils/keyboard'], function ($, keyboard) {
	var module = {};

	/**
	 * Funkce pro "schovani" stringu v JS kodu
	 *
	 * @param {string} hexString
	 * @returns {string}
	 */
	var hex2str = function (hexString) {
		return hexString.replace(/[a-fA-F0-9]{2}/g, function(match) {
			return String.fromCharCode(parseInt(match, 16));
		});
	};

	var DOT_FILE_RESET = hex2str('2E66696C655265736574'); // '.fileReset'
	var DATA_ICON = hex2str('646174612D69636F6E'); // 'data-icon'
	var DECRYPT_ELEMENT_SELECTOR = hex2str('5B646174612D69636F6E5D'); // '[data-icon]'

	/**
	 * Dekryptuje zablowfishovane elementy
	 * (prazdne elementy, ktere nejsou class fileReset a maji atribut data-icon)
	 *
	 * @param {string} selector
	 */
	var decryptElements = function (selector) {
		if (typeof ad != 'undefined') {
			$(selector).not(DOT_FILE_RESET).each(function () {
				var element = $(this);
				var dataIcon = element.attr(DATA_ICON);
				if (dataIcon && element.html().trim() == '') {
					element.replaceWith(decodeURIComponent(escape(ad.decrypt(kn[dataIcon]))));
				}
			});
		}
	};

	var setSearchLeaderboardPosition = function () {
		$('#jsSearchLeaderboardAdvert').offset($('#jsSearchLeaderboardAdvertContainer').offset());
	};

	$(window).on('resize', setSearchLeaderboardPosition);

	$(document).on("mousedown", DOT_FILE_RESET, function (b) {
		if (b.which == 2) {
			b.preventDefault();
		}
	});

	$(document).click(function (b) {
		var target = $(b.target);
		if (!target.is(DOT_FILE_RESET)) {
			target = $(b.target).parents(DOT_FILE_RESET).first();
		}
		if (target.length == 1) {
			if (b.which == 1) {
				if (keyboard.isPressed(keyboard.CTRL)) {
					b.preventDefault();
					keyboard.reset();
					window.open(ad.decrypt(kn[target.attr(DATA_ICON)]), 'trg' + (new Date).valueOf());
				} else {
					b.preventDefault();
					window.location = ad.decrypt(kn[target.attr(DATA_ICON)]);
				}
			} else if (b.which == 2) {
				b.preventDefault();
				keyboard.reset();
				window.open(ad.decrypt(kn[target.attr(DATA_ICON)]), 'trg' + (new Date).valueOf());
			}
		}
	});

	module.load = function (targetSelector) {
		var loc = document.location.toString();
		if (!loc.match(/[\?&]q=/)) {
			loc = loc + (loc.indexOf('?') >= 0 ? '&' : '?') + 'q=%20';
		} else {
			loc = loc.replace(/(?:([\?&]q=))([^\&]*)/, '$1$2%20');
		}
		$.ajax({
			type: 'GET',
			url: loc,
			dataType: 'html',
			success: function (data) {
				$(targetSelector).html(data);
				decryptElements(targetSelector + ' ' + DECRYPT_ELEMENT_SELECTOR);
				$(document).trigger('paginator:loaded', [$(targetSelector)]);
			}
		});
	};

	module.register = function () {
		$(function () {
			decryptElements(DECRYPT_ELEMENT_SELECTOR);
		});
	};

	module.positionSearchLeaderboard = function (element, liPosition, containerStyle) {

		if (element.length > 0 && element.html().replace(/^\s+|\s+$/g, '')) {

			var liExact = $('#search-content ul.chessFiles.exactSearchResults li').filter(':visible');
			var liBeforeAdvert = liExact.eq(liPosition);
			if (liBeforeAdvert.length > 0) {
				liBeforeAdvert.before('<li class="inChessAd" id="jsSearchLeaderboardAdvertContainer" style="' + containerStyle + '"></li>');
				setSearchLeaderboardPosition();
			} else {
				if (liExact.length > 0 && $('.partialSearchHeading').length) {
					$('.partialSearchHeading').before('<div id="jsSearchLeaderboardAdvertContainer" style="' + containerStyle + '"></div>');
					setSearchLeaderboardPosition();
				} else {
					liBeforeAdvert = $('#search-content ul.chessFiles.partialSearchResults li').filter(':visible').eq(liPosition);
					if (liBeforeAdvert.length > 0) {
						liBeforeAdvert.before('<li class="inChessAd" id="jsSearchLeaderboardAdvertContainer" style="' + containerStyle + '"></li>');
						setSearchLeaderboardPosition();
					}
				}
			}

		}

	}

	return module;
});
