

var host = window.location.hostname;
var path = window.location.pathname;
if (path.split('/')[2]) {
	$('a.bank_fast_icon').click(function() {
		dataLayer.push({'event':'ga.event','eCategory':'Credit','eAction':(host+path),'eLabel':'fast_transfer','eValue':undefined});
	});
	$('a.card_icon').click(function() {
		dataLayer.push({'event':'ga.event','eCategory':'Credit','eAction':(host+path),'eLabel':'card','eValue':undefined});
	});
	$('a.jsPaymentPopupButtonBank').click(function() {
		dataLayer.push({'event':'ga.event','eCategory':'Credit','eAction':(host+path),'eLabel':'bank_transfer','eValue':undefined});
	});
}
