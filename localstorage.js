<script>
$("#frm-downloadDialog-freeDownloadForm-freeDownload, #tab-vip a.button-l, #frm-download-freeDownloadTab-freeDownloadForm-freeDownload, #frmdownloadForm-download, #quickDownloadButton").click(fileHandler);

function fileHandler () {
	if (localStorage && localStorage.setItem) {
		var store = [];
		if (localStorage.myFiles) {
			try {
				store = JSON.parse(localStorage.myFiles);
				if (!store.unshift) {
					store = []; // fix corrupt data
				}
			} catch (e) {
				store = [];
			}
		}
		var key = "" + location.pathname;
		var size = store.unshift[{key : new Date()}];
		if (size > 100) {
			store.slice(0, 100);
		}
		localStorage.myFiles = JSON.stringify(store);
	}
}
</script>