define(['nodus/messageApi', 'nodus/file/thumbnail', 'nodus/paginator', 'nodus/tooltip'], function (messageApi, thumbnail, paginator, tooltip) {

	messageApi.register();

	thumbnail.register();

	paginator.register();
	
	tooltip.register();

});
