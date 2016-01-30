<script>
$(document).ready(function() {
	var redirForm = $('div#serviceRedirDialog fieldset form');
	if (redirForm.length) {
		redirForm.attr('action', redirForm.attr('action') + '&media=&redir=0');
	}
});
</script>


<script>
// statistics page
$('div.statistics td.file-name a').each(function (i) {
	console.log( i, $(this).attr('href'), $(this).attr('title'));
});
</script>