var gulp         = require('gulp')
,   react        = require('gulp-react')
,   gulpIf       = require('gulp-if')
,   uglify       = require('gulp-uglify')
,   _            = require('underscore')
,   elixir       = require('laravel-elixir')
,   utilities    = require('laravel-elixir/ingredients/commands/Utilities')
,   notification = require('laravel-elixir/ingredients/commands/Notification');

elixir.extend('react', function (src, options) {

    var config = this,
        defaultOptions = {
            debug : ! config.production,
            srcDir: config.assetsDir + 'js',
            output: config.jsOutput
        };

    options = _.extend(defaultOptions, options);
    src     = "./" + utilities.buildGulpSrc(src, options.srcDir, '**/*.jsx');

    options = _.extend(defaultOptions, options);

    gulp.task('react', function () {

        var onError = function(e) {
            new notification().error(e, 'React Compilation Failed!');
            this.emit('end');
        };

        return gulp.src(src)
            .pipe(react(options)).on('error', onError)
            .pipe(gulpIf(! options.debug, uglify()))
            .pipe(gulp.dest(options.output))
            .pipe(new notification().message('React Compiled!'));
    });

    this.registerWatcher('react', options.srcDir + '/**/*.jsx');

    return this.queueTask('react');
});


elixir(function(mix) {
    mix.less('app.less');
});

elixir(function(mix) {
    mix.react();
});