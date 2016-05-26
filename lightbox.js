$('body').append('<p id="dynamic">dynamic</p><link rel="stylesheet" href="//static.uloz.to/content/apk/lightbox/lightbox.css">');

(function() {
 var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
 po.src = 'https://static.uloz.to/content/apk/lightbox/lightbox-plus-jquery.min.js';
 var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
})();

$('#dynamic').on('click', function() {

 $(this).lightGallery({
   dynamic: true,
   dynamicEl: [{
     "src": '//imageth.uloz.to/4/3/b/43b23c68477ec3ac29366d115841ab4f.640x360.jpgg',
     'thumb': '//imageth.uloz.to/4/3/b/43b23c68477ec3ac29366d115841ab4f.160x120.jpg',
     'subHtml': '<p>name obrazku</p>'
   }]
 })

});



var gImgs = '<div style="display:none">', gCount = 0, lightbox;
$('body').append('<link rel="stylesheet" href="//static.uloz.to/content/lightbox/lightbox.css"><script src="//static.uloz.to/content/lightbox/lightbox.min.js"><\/script>');

setTimeout(function() {
	// iterate through all thumbs and trt to create the gallery
	$('ul.publicThumbs li, table.publicTable tr').each(function() {
		var el = $(this);
		var img = el.find('img.thumb_icon');
		if (img.length) { // skip non thumbs
			gImgs += '<img data-jslghtbx data-jslghtbx-group=myGallery data-jslghtbx-caption="' + 
				el.find('a.name, div.fileReset').text() + '" src=' + img.attr('src').replace('160x120', '640x360') + '>';
			gCount++;
		}
	});

	// initialize it only for cases of 3+ pictures
	if (gCount > 2) {
		// append faked images, trigger icon, css and js itself
		$('#publicControl form').append(gImgs + '</div><img id=galleryStarter alt=Galerie style="margin-bottom:-10px;width:32px;cursor:pointer" src="//static.uloz.to/content/lightbox/gallery2.png">');

		lightbox = new Lightbox();
		lightbox.load({nextOnClick: false});

		// bind starting trigger
		document.getElementById('galleryStarter').addEventListener('click', function(){
			lightbox.open(false, 'myGallery');
		});
	}
}, 500); // wait for the data to be loaded




//// done 1 alpha

(function(){

var gImgs = '<div style="display:none">', gCount = 0, lightbox;
$('body').append('<link rel="stylesheet" href="//static.uloz.to/content/lightbox/lightbox.css"><script src="//static.uloz.to/content/lightbox/lightbox.min.js"><\/script>');

setTimeout(function() {
	// iterate through all thumbs and texts to create the gallery
	$('ul.publicThumbs li, table.publicTable tr').each(function() {
		var el = $(this);
		var img = el.find('img.thumb_icon');
		if (img.length) { // skip non thumbs
			gImgs += '<img data-jslghtbx data-jslghtbx-group=myGallery data-jslghtbx-caption="' + 
				el.find('h4 a.name, tr div.fileReset').text() + '" src=' + img.attr('src').replace('160x120', '640x360') + '>';
			gCount++;
		}
	});

	// initialize it only for cases of 3+ pictures
	if (gCount > 2) {
		// append faked images, trigger icon, css and js itself
		$('#publicControl form').append(gImgs + '</div><img id=galleryStarter alt=Galerie title="Galerie" style="margin:0px 10px -10px 0px;float:right;width:32px;cursor:pointer" src="//static.uloz.to/content/lightbox/gallery2.png">');

		lightbox = new Lightbox();
		lightbox.load({nextOnClick: false});

		// bind starting trigger
		document.getElementById('galleryStarter').addEventListener('click', function(){
			lightbox.open(false, 'myGallery');
		});
	}
}, 777); // wait for the data to be loaded


})();





<script>

(function(){

var gImgs = '<div style="display:none">', gCount = 0, lightbox;
$('body').append('<link rel="stylesheet" href="//static.uloz.to/content/lightbox/lightbox.css"><script src="//static.uloz.to/content/lightbox/lightbox.min.js"><\/script>');

setTimeout(function() {
	// iterate through all thumbs and texts to create the gallery
	$('ul.publicThumbs li, table.publicTable tr').each(function() {
		var el = $(this);
		var img = el.find('img.thumb_icon');
		var anotherRealm = el.find('.fileRealmLogo');
		if (img.length && (!anotherRealm.length || location.hostname.indexOf('pornfile') > -1)) { // skip non thumbs
			gImgs += '<img data-jslghtbx data-jslghtbx-group=myGallery data-jslghtbx-caption="' + 
				el.find('h4 a.name, tr div.fileReset').text() + '" src=' + img.attr('src').replace('160x120', '640x360') + '>';
			gCount++;
		}
	});

	// initialize it only for cases of 3+ pictures
	if (gCount > 1) {
		// append faked images, trigger icon, css and js itself
		$('#publicControl form').append(gImgs + '</div><img id=galleryStarter alt=Galerie title="Galerie" style="margin:0px 10px -10px 0px;float:right;width:32px;cursor:pointer" src="//static.uloz.to/content/lightbox/gallery2.png">');

		lightbox = new Lightbox();
		lightbox.load({nextOnClick: false});

		// bind starting trigger
		document.getElementById('galleryStarter').addEventListener('click', function(){
			lightbox.open(false, 'myGallery');
		});
	}
}, 1199); // wait for the data to be loaded


})();

</script>