define(["jquery"], function($) {

	// vraceny AMD modul
	var module = {};

	var mobileLayout = false;

	var detectMobileLayout = function() {
		var viewportWidth = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);

		if (viewportWidth < 767) {
			mobileLayout = true;
			mobilizeMenu();
			mobilizePricelist();
		} else {
			mobileLayout = false;
			unMobilizeMenu();
			unMobilizePricelist();
		}
	};

	var mobilizeMenu = function() {
		if (mobileLayout === true && $('.pg-footer .menu-foot a').length < 1) {
			menuNodes = $.makeArray($('.foot-text a').clone());
			$('.foot-text').hide();
			$('.pg-footer').append('<div class="menu-foot"></div>');
			$('.menu-foot').append(menuNodes).show();
		//	$('.menu-foot');

			$('.content-content').prepend($('.news-ribbon'));
		}
		if ($('#hp-nav').length > 0 && $('.home-ad-right .adFull').length > 0) {
			var adRightOffset = $('#hp-nav').offset();
			$('.home-ad-right .adFull').offset({top: adRightOffset.top});
		}
	};

	var unMobilizeMenu = function() {
		$('.foot-text').show();
	};

	var mobilizePricelist = function() {
		if($('#pricelist').is(':visible')) {
			$('#pricelist').addClass('mobilize');

			$('.price-item').each(function() {
				var thisOnline = $(this).find('.cenik_online_method');
				var thisOffline = $(this).find('.cenik_offline_method');
				var thisOnlineHref = $(this).find('.cenik_online_method a');
				var thisOfflineHref = $(this).find('.cenik_offline_method a');

				if(thisOnline.length || thisOffline.length) {
					if(thisOfflineHref.length==0) {
						thisOffline.css('width', '0');
						thisOnline.css('width', '100%');
						if(thisOnlineHref.length==1) {
							thisOnlineHref.css('width','100%');
						}
					}

				}
			});

			$('.jsPaymentPopupButtonGopayCard, .jsPaymentPopupButtonWebpayCard, .jsPaymentPopupButtonBankGopay, .jsPaymentPopupButtonBank, .cenik_online_method a, .cenik_offline_method a').on('click', function(e){
				var $this = $(this);
				if ($this.attr('data-mobile-href')) {
					window.location = $this.attr('data-mobile-href');
				} else {
					window.location = $this.attr('href');
				}
				return false;
			});
		}
	};

	var unMobilizePricelist = function() {
		if($('#pricelist').is(':visible')) {
			$('#pricelist').removeClass('mobilize');
			$('.price-item li, .price-item a').css('width', '');
		}
	};

	module.init = function () {
		$(function () {
			$('.menu-toggle a.toggle, .menu-foot-toggle a.toggle').on('click', function (e) {
				var menu = $(this).parent().next('div');
				if (menu.hasClass('open')) {
					menu.removeClass('open');
					$(this).parent().find('.user').css('opacity', '1');
					$(this).parent().removeClass("open");
				} else {
					menu.addClass('open');
					$(this).parent().find('.user').css('opacity', '0');
					$(this).parent().addClass("open");
					if($(this).parent().attr('class')=='menu-foot-toggle open'){
						$("html, body").animate({ scrollTop: $(document).height() }, 1000);
					}
				}
			});
			$('.price-item').on('click', function(e) {
				if(mobileLayout === true) {
					$('.price-item').removeClass('mobile-priceitem');
					$(this).addClass('mobile-priceitem');
					$('.cenik_online_method, .cenik_offline_method, .cenik_sms_method').css('display','');
					$(this).children().css('display', 'block');
				}
			});
			$(window).resize(function () {
				detectMobileLayout();
			});
			detectMobileLayout();
		});
	};

	module.isMobileLayout  = function() {
		return mobileLayout;
	}

	return module;
});
