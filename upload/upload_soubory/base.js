requirejs.config({
	baseUrl: '..',
	paths: {
		'requireLib': 'require',
		'jquery': 'lib/jquery/jquery.min',
		'lib/jquery/ui': 'lib/jquery/jquery-ui-1.8.16.custom.min',
		'lib/jquery/jcarousel': 'lib/jquery/jquery.jcarousel.min'
	},
	shim: {
		'lib/jquery/ui': ['jquery'],
		'lib/jquery/jcarousel': ['jquery']
	}
});
