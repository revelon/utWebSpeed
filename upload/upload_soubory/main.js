var t;

function SelectAll(id) {
	document.getElementById(id).focus();
	document.getElementById(id).select();
}

function mlog(m) {
	if(console){
		console.log(m);
	}
}

function animateFavoriteStar() {
	$('#menu a.menu-star').effect("pulsate", 1200, function(){animateFavoriteStar();});
}

$(document).ready(function(e){

	$( "#search-input.searchSuggest" ).autocomplete({
		source: "/searchSuggest.php",
		minLength: 3,
		open: function(event, ui) { $(".ui-autocomplete").css("z-index", 999); },
		select: function( event, ui ) {
			var formAction = $('#search').attr('action');
			var cleanAction = (formAction.indexOf('?') >= 0) ? formAction.substr(0, formAction.indexOf('?')) : formAction;
			window.location = cleanAction + '?q=' + encodeURIComponent(ui.item.value);
		}
	});

	$('.datepicker').datepicker();

	// akce po zkopirovani souboru do oblibenych pokud bylo predtim potreba se prihlasit
	if ($('.flash.success.copyToFavoritesCompleted').length > 0) {
		animateFavoriteStar();
	}

}).click(function(e){
	if (e.which > 1) {
		return;
	}
	var target=$(e.target);

	if(target.is('.jsSelectThis')) {
		target.select();
	}

	if(target.is('.jsClose')) {
		e.preventDefault();
		e.stopPropagation();
		target.closest('.spamdialog, .deldialog').hide();
	}

	if(target.is('#jsShowRequestPassword')) {
		e.preventDefault();
		e.stopPropagation();
		$('#jsRequestPasswordForm').toggle();
	}

	if(target.is('.jsAddToFavorites, .jsAddToFavorites i')) {
		$('.jsHiddenNope').hide();
		var turl;
		e.preventDefault();
		e.stopPropagation();
		var t = target.closest('.jsAddToFavorites');
		if (t.attr('href')){
			turl = t.attr('href');
		}
		else{
			turl = ad.decrypt(kn[t.attr('data-icon')]);
		}
		t.html('<i class="fa fa-spinner fa-spin"></i>');
		$.ajax({
			type: 'GET',
			url: turl,
			error: function(error) {
				$('.jsFavoritesNope').hide();
				alert(error.statusText);
			},
			success: function(e) {
				$('.jsFavoritesNope').hide();
				if(e.status===true){
					t.parent('.quickLinks').prepend('<em class="quickFavs"><i class="fa fa-star"></i></em>');
					if(t.is('.copyTo')) {
						animateFavoriteStar();
						var nope = t.closest('.jsNopeContainer').find('.jsFavoritesNope').first().show();
						nope.find('.favoriteNopeContent').html(e.message);
					}
					t.hide();
				} else {
					alert(e.message);
				}
			}
		});
	}

	if(target.is('.jsAddToFavoritesNope, .jsAddToFavoritesNope *')) {
		e.preventDefault();
		e.stopPropagation();
		t = target;
		$('.jsHiddenNope').hide();
		target.closest('.jsNopeContainer').find('.jsFavoritesNope').first().show();
		$('#favoritesNope').fadeIn();
	}

	if(target.is('.jsShowQuickDownloadNope, .jsShowQuickDownloadNope *')) {
		e.preventDefault();
		e.stopPropagation();
		t = target;
		$('.jsHiddenNope').hide();
		if(target.closest('.jsNopeContainer').is('.jsFmEntity')) {
			target.closest('.jsNopeContainer').parent().css('position','relative').css('float','left');
		}
		target.closest('.jsNopeContainer').find('.jsQuickDownloadNope').first().show();
		$('#favoritesNope').fadeIn();
	}

	if(target.is('.hiddenNopeClose')) {
		e.preventDefault();
		e.stopPropagation();
		$('.jsHiddenNope').hide();
	}

	if(target.is('.jsFileInfoToast')) {
		e.preventDefault();
		e.stopPropagation();
		t = target;
		$('.jsHiddenNope').hide();
		$('.virusPuaNopeContent').html(t.attr('data-title'));
		target.closest('.jsNopeContainer').find('.jsVirusPuaNope').first().show();
	}

	if(target.is('.collapse-title, .collapse-title *')) {
		e.preventDefault();
		e.stopPropagation();
		target.parents('.collapse').toggleClass('open').find('.collapse-content').slideToggle();
	}

	if (target.is('#clearDownloadsHistoryButton')) {
		return window.confirm(target.attr('data-message'));
	}

});


/**
 * AJAX Nette Framwork plugin for jQuery
 *
 * @copyright   Copyright (c) 2009 Jan Marek
 * @license     MIT
 * @link        http://nettephp.com/cs/extras/jquery-ajax
 * @version     0.2
 */

jQuery.extend({
	nette: {
		updateSnippet: function (id, html) {
			$("#" + id).html(html);
		},

		success: function (payload) {

			if(payload!==undefined) {
				// redirect
				if (payload.redirect) {
					window.location.href = payload.redirect;
					return;
				}

				// snippets
				if (payload.snippets) {
					for (var i in payload.snippets) {
						jQuery.nette.updateSnippet(i, payload.snippets[i]);
					}
				}
			}

		}
	}
});

jQuery.ajaxSetup({
	success: jQuery.nette.success,
	dataType: "json"
});

$("a.ajax").live("click", function (event) {
    event.preventDefault();
    $.get(this.href);
	//$('<div id="ajax-spinner"></div>').addClass("ui-corner-all").show().appendTo("body");
});

/* AJAXové odeslání formulářů */
$("form.ajax").live("submit", function () {
    $(this).ajaxSubmit();
    return false;
});

$("form.ajax :submit").live("click", function () {
    $(this).ajaxSubmit();
    return false;
});
/**
 * AJAX form plugin for jQuery
 *
 * @copyright  Copyright (c) 2009 Jan Marek
 * @license    MIT
 * @link       http://nettephp.com/cs/extras/ajax-form
 * @version    0.1
 */

jQuery.fn.extend({
	ajaxSubmit: function (callback, dataType, errorCallback) {
		var form;
		var sendValues = {};

		// submit button
		if (this.is(":submit")) {
			form = this.parents("form");
			sendValues[this.attr("name")] = this.val() || "";

		// form
		} else if (this.is("form")) {
			form = this;

		// invalid element, do nothing
		} else {
			return null;
		}

		// validation
		if (form.get(0).onsubmit && !form.get(0).onsubmit()) return null;

		// get values
		var values = form.serializeArray();

		for (var i = 0; i < values.length; i++) {
			var name = values[i].name;

			// multi
			if (name in sendValues) {
				var val = sendValues[name];

				if (!(val instanceof Array)) {
					val = [val];
				}

				val.push(values[i].value);
				sendValues[name] = val;
			} else {
				sendValues[name] = values[i].value;
			}
		}

		// send ajax request
		var ajaxOptions = {
			url: form.attr("action"),
			data: sendValues,
			type: form.attr("method") || "get"
		};

		if (callback) {
			ajaxOptions.success = callback;
		}

		if (dataType) {
			ajaxOptions.dataType = dataType;
		}

		if (errorCallback)
		{
			ajaxOptions.error = errorCallback;
		}

		return jQuery.ajax(ajaxOptions);
	}
});

/* Czech initialisation for the jQuery UI date picker plugin. */
/* Written by Tomas Muller (tomas@tomas-muller.net). */
jQuery(function($){
	$.datepicker.regional['cs'] = {
		closeText: 'Zavřít',
		prevText: '&#x3c;Dříve',
		nextText: 'Později&#x3e;',
		currentText: 'Nyní',
		monthNames: ['leden','únor','březen','duben','květen','červen',
        'červenec','srpen','září','říjen','listopad','prosinec'],
		monthNamesShort: ['led','úno','bře','dub','kvě','čer',
		'čvc','srp','zář','říj','lis','pro'],
		dayNames: ['neděle', 'pondělí', 'úterý', 'středa', 'čtvrtek', 'pátek', 'sobota'],
		dayNamesShort: ['ne', 'po', 'út', 'st', 'čt', 'pá', 'so'],
		dayNamesMin: ['ne','po','út','st','čt','pá','so'],
		weekHeader: 'Týd',
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['cs']);
});

jQuery.fn.extend({

/* reportovaci formular  */
	reportContentForm: function (tree) {
		var form = this;
		var allControls = [];
		var allContainters = [];

		for (var cName in tree) {
			for (var iName in tree[cName]) {
				for (var value in tree[cName][iName]) {
					var items = jQuery.isArray(tree[cName][iName][value]) ? tree[cName][iName][value] : [tree[cName][iName][value]];
					items = $('#'+items.join(', #'));
					if (items.length > 0) {
						tree[cName][iName][value] = items;
						items.each(function () { allContainters.push(this); });
						form.find('input[name="'+cName+'['+iName+']"]').each(function () { allControls.push(this); });
					}
					else {
						delete tree[cName][iName][value];
					}
					if (jQuery.isEmptyObject(tree[cName][iName])) {
						delete tree[cName][iName];
					}
				}
				if (jQuery.isEmptyObject(tree[cName])) {
					delete tree[cName];
				}
			}
		}
		allControls = jQuery(jQuery.unique(allControls));
		allContainters = jQuery(jQuery.unique(allContainters));

		var refresh = function () {
			allContainters.hide();
			for (var cName in tree) {
				for (var iName in tree[cName]) {
					var value = form.find('input[name="'+cName+'['+iName+']"]:checked').val();
					if (value && tree[cName][iName][value]) {
						tree[cName][iName][value].show();
					}
				}
			}
			allContainters.filter('div:visible').each(function () {
				var countVisible = $(this).find(':visible').not('div').length;
				if (countVisible === 0) {
					$(this).hide();
				}
			});
		};

		allControls.change(function () {
			var thisName = $(this).attr('name');
			var found = false;
			for (var cName in tree) {
				for (var iName in tree[cName]) {
					if (cName+'['+iName+']' == thisName) {
						found = true;
					}
					if (found) {
						for (var value in tree[cName][iName]) {
							tree[cName][iName][value].find('input:text, textarea, select').val('');
							tree[cName][iName][value].find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
						}
					}
				}
			}
			refresh();
		});

		form.submit(function(event) {
			if (form.find('input:submit:visible').length === 0) {
				event.preventDefault();
			}
		});

		form.each(function() { this.noValidate = true; });
		form.find('input, textarea, select').removeAttr('data-nette-rules');
		form.find('input:submit').attr('formnovalidate', 'formnovalidate');
		refresh();
		return form;
	},

/* coutdown pro text inputy */
	textInputCountdownMessage: function (input, messageHtmlTemplate) {
		var $container = $(this);
		var $input = $(input).first();
		var maxLength = $input.attr('maxlength');

		var render = function () {
			var rest = maxLength - $input.val().length;
			var message = messageHtmlTemplate.replace('%rest%', rest);
			message = message.replace('%total%', maxLength);
			$container.html(message);
		};

		if (maxLength > 0 && messageHtmlTemplate) {

			$input.keyup(function() {
				render();
			});

			render();
		}
	}
});


$(function () {

	var $newsList = $('#newsList .news');
	var index = 0;

	var timeoutId, toggleNews;

	var timeoutCurrentNews = function () {
		if ($newsList.length <= 1) {
			return;
		}

		timeoutId = window.setTimeout(toggleNews, 10000);
	};

	toggleNews = function() {
		$newsList.eq(index).removeClass('show');

		index = (index + 1) % $newsList.length;
		$newsList.eq(index).addClass('show');

		timeoutCurrentNews();
	};

	$('#newsList').on('click', '.jsMarkNewsAsRead', function (e) {
		window.clearTimeout(timeoutId);
		$.get($(e.currentTarget).data('href')); // oznaci novinku jako prectenou

		$newsList.eq(index).remove();
		$newsList = $('#newsList .news');

		if ($newsList.length > 0) {
			index = index % $newsList.length;
			$newsList.eq(index).addClass('show');
		}

		timeoutCurrentNews();
	});

	$('#newsList').on('click', '.newsContent a', function (e) {
		var $markAsReadLink = $(e.currentTarget).parents('.news').find('.jsMarkNewsAsRead');
		$.get($markAsReadLink.data('href')); // oznaci novinku jako prectenou
	});

	timeoutCurrentNews();

});
