var gulp = require('gulp');
var browserify = require('browserify');
var babelify = require('babelify');
var source = require('vinyl-source-stream');

var scripts = [
	'Notify.js',
	'App.js',
	'BasicComp.js',
	'ListHandler.js',

	'partials',
	'pages',

	'Router.js',
]

gulp.task('scripts', function() {
	browserify('resources/assets/js/main.js')
		.transform(babelify.configure({ optional: ['es7.decorators', 'es7.classProperties'] }))
		.bundle()
		.pipe(source('all.js'))
		.pipe(gulp.dest('public/js'))
});

gulp.task('watch', function() {
	gulp.watch('resources/assets/js/**/*.js', ['scripts']);
});

gulp.task('default', ['watch', 'scripts']);

// elixir(function(mix) {
// 	mix.less('app.less');
// 	mix.babel(scripts).browserify('all.js', null, 'public/js');
// });