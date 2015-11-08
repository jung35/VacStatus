var gulp = require('gulp');
var browserify = require('browserify');
var babelify = require('babelify');
var source = require('vinyl-source-stream');
var less = require('gulp-less');

gulp.task('scripts', function() {
	browserify('resources/assets/js/main.js')
		.transform(babelify.configure({ optional: ['es7.decorators', 'es7.classProperties'] }))
		.bundle()
		.pipe(source('all.js'))
		.pipe(gulp.dest('public/js'))
});

gulp.task('less', function () {
	return gulp.src('resources/assets/less/app.less')
		.pipe(less())
		.pipe(gulp.dest('public/css'));
});

gulp.task('watch', ['scripts', 'less'], function() {
	gulp.watch('resources/assets/js/**/*.js', ['scripts']);
	gulp.watch('resources/assets/less/**/*.less', ['less']);
});

gulp.task('default', ['scripts', 'less']);
