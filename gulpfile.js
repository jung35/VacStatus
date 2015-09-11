var elixir = require('laravel-elixir');

var scripts = [
	'Notify.js',
	'App.js',
	'BasicPage.js',
	'ListHandler.js',

	'partials',
	'pages',

	'Router.js',
]

elixir(function(mix) {
	mix.less('app.less')
		.babel(scripts)
		.browserify('all.js', null, 'public/js');
});