
if ((navigator.userAgent.indexOf('Android') > -1) && !$('#newsList').length && localStorage && localStorage.setItem) {

	if (!localStorage.stopAndroidTeasingbanner) {

		$('<div id="newsList"> ' +
			'<div class="news show">' +
				'<div class="newsContent"><img src="//static.uloz.to/content/apk/android-icon.png" style="float:left; width:30px; margin: -6px 10px 0px 9px;"> ' +
				'<p>Vyzkoušej naši <a class="out" href="//static.uloz.to/content/apk/ulozto-release-1.23.apk">aplikaci pro Android</a>! &nbsp; | &nbsp; ' +
				'Try out our <a class="out" href="//static.uloz.to/content/apk/ulozto-release-1.23.apk">Android application</a>!</p></div> ' +
				'<a id="androidTease" class="newslist-close jsMarkNewsAsRead" rel="nofollow" href="#"><i class="fa fa-times"></i></a> ' +
			'</div> ' +
		'</div>').insertBefore('section.pg-set-homepage');

		$('#androidTease, div.newsContent a.out').click(function() {
			localStorage.stopAndroidTeasingbanner = 1;
			dataLayer.push({'event':'ga.event','eCategory':'AndroidApp','eAction':'WebDownload','eLabel':undefined,'eValue':undefined});
			$('#newsList').remove();
		})
	}

}