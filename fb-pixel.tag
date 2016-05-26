<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','//connect.facebook.net/en_US/fbevents.js');

fbq('init', '1689106544693411');
fbq('track', "PageView");

// check for VIP user
if (($('li.menu-kredit a').length && $('li.menu-kredit a').attr('title') && $('li.menu-kredit a').attr('title').indexOf('GB') > -1) || ($('li a.menu-kredit').length && $('li a.menu-kredit').attr('title') && $('li a.menu-kredit').attr('title').indexOf('GB') > -1)) {

  if (!sessionStorage.getItem('fbUserType')) {
    fbq('track', 'ViewContent', {content_name: 'VIPUser'});
    sessionStorage.setItem('fbUserType', 1);
  }
} else {
// or free download case
  var fbFreeButt = document.getElementById('frm-downloadDialog-freeDownloadForm-freeDownload');
  if (fbFreeButt) fbFreeButt.addEventListener('click', function() {
    fbq('track', 'ViewContent', {content_name: 'FreeDownload'});
  });
}
  
</script>