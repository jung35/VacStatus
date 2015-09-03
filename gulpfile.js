var elixir = require('laravel-elixir');

elixir(function(mix) {
	mix.less('app.less').babel('**/*.js').browserify('all.js', null, 'public/js');
});