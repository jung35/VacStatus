var elixir = require('laravel-elixir');

var scripts = [
	'partials',
	'pages',

	'ListHandler.js',
	'Notify.js',
	'Router.js',
]

elixir(function(mix) {
	mix.less('app.less')
		.babel(scripts)
		.browserify('all.js', null, 'public/js');
});