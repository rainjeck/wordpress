var gulp = require('gulp');
var plugin = require('gulp-load-plugins')();
var rollup = require('rollup');
var rollup_resolve = require('@rollup/plugin-node-resolve');
var rollup_commonjs = require('@rollup/plugin-commonjs');
var minifyjs = require('gulp-minify.js');

var destDir = './assets';

// === Stylus
gulp.task('css:app', function() {
    return gulp.src([
        './src/stylus/*.styl',
        '!./src/stylus/libs.styl',
    ])
    .pipe(plugin.sourcemaps.init())
    .pipe(plugin.stylus({
        'include css': true,
        compress: false
    }).on('error', plugin.notify.onError('<%= error.message %>')))
    .pipe(plugin.autoprefixer({
        remove: false,
        cascade: false,
    }))
    .pipe(plugin.sourcemaps.write('../css'))
    .pipe(gulp.dest(destDir + '/css'))
});

gulp.task('css:libs', function() {
    return gulp.src([
        './src/stylus/libs.styl',
    ])
    .pipe(plugin.stylus({
        'include css': true,
        compress: false
    }).on('error', plugin.notify.onError('<%= error.message %>')))
    .pipe(gulp.dest(destDir + '/css/'))
});

gulp.task('css:bundle', function() {
    return gulp.src([
        destDir + '/css/libs.css',
        destDir + '/css/main.css',
    ])
    .pipe(plugin.concat('bundle.css'))
    .pipe(gulp.dest(destDir + '/css/'));
});

gulp.task('css:minify', function() {
    return gulp.src([
        destDir + '/css/*.css',
        '!' + destDir + '/css/*.min.css',
    ])
    .pipe(plugin.cleanCss())
    .pipe(plugin.rename({
        suffix: '.min'
    }))
    .pipe(gulp.dest(destDir + '/css/'));
});

gulp.task('css', gulp.series('css:app', 'css:libs', 'css:bundle', 'css:minify'));


// === JS
gulp.task('js:app', function() {
    return gulp.src([
        './src/js/*.js',
        '!./src/js/libs.js',
    ])
    .pipe(plugin.sourcemaps.init())
    .pipe(plugin.include()).on('error', console.log)
    .pipe(plugin.babel({ presets: [ '@babel/env' ] }))
    .pipe(plugin.sourcemaps.write('../js'))
    .pipe(gulp.dest(destDir + '/js'))
});

gulp.task('js:libs', function() {
    return rollup.rollup({
        input: './src/js/libs.js',
        plugins: [ rollup_commonjs(), rollup_resolve() ],
    })
    .then(bundle => {
        return bundle.write({
            file: destDir + '/js/libs.js',
            format: 'umd',
            sourcemap: false,
        });
    });
});

gulp.task('js:bundle', function() {
    return gulp.src([
        './assets/js/libs.js',
        './assets/js/main.js',
    ])
    .pipe(plugin.concat('bundle.js'))
    .pipe(gulp.dest(destDir + '/js/'))
});

gulp.task('js:minify', function() {
    return gulp.src([
        destDir + '/js/*.js',
        '!' + destDir + '/js/*.min.js',
    ])
    // .pipe(plugin.uglify())
    .pipe(minifyjs())
    .pipe(plugin.rename({
        suffix: '.min'
    }))
    .pipe(gulp.dest(destDir + '/js'));
});

gulp.task('js', gulp.series('js:app', 'js:libs', 'js:bundle', 'js:minify'));


// === SVG
gulp.task('svg:sprite', function() {
    return gulp.src([
        './assets/icons/*.svg',
        '!./assets/icons/sprit*.svg',
    ])
    .pipe(plugin.svgSymbolView({
        name: 'sprite',
        svgo: { plugins: [
            { removeAttrs: {attrs: '(fill-rule|clip-rule|fill|color|stroke-linecap|stroke-linejoin|style)'} }
        ]}
    }))
    .pipe(gulp.dest(destDir + '/icons'));
});

gulp.task('svg:spritecolor', function() {
    return gulp.src([
        './assets/icons/*.svg',
        '!./assets/icons/sprit*.svg',
    ])
    .pipe(plugin.svgSymbolView({
        name: 'sprite-color',
    }))
    .pipe(gulp.dest(destDir + '/icons'));
});

gulp.task('svg', gulp.series('svg:sprite', 'svg:spritecolor'));


// === Reloader
gulp.task('reloader', function(done) {
  plugin.livereload.changed('/');
  done();
});


// === Watch
gulp.task('watch', function() {
  plugin.livereload.listen({
    basePath: './',
    start: true
  });

  // --- CSS
  gulp.watch(['./src/stylus/**/*.styl', '!./src/stylus/**/libs.styl'], gulp.series('css:app', 'css:bundle', 'css:minify', 'reloader'));
  gulp.watch(['./src/stylus/**/libs.styl'], gulp.series('css:libs', 'css:bundle', 'css:minify', 'reloader'));

  // --- JS
  gulp.watch(['./src/js/**/*.js', '!./src/js/**/libs.js'], gulp.series('js:app', 'js:bundle', 'js:minify', 'reloader'));
  gulp.watch(['./src/js/**/libs.js'], gulp.series('js:libs', 'js:bundle', 'js:minify', 'reloader'));

  // --- PHP
  gulp.watch(['*.php', 'views/**/*.php'], function php(done) {
    plugin.livereload.reload();
    done();
  });
});

gulp.task('media', gulp.series('svg', 'reloader'), function () {});

gulp.task('build', gulp.series( 'svg', 'css', 'js' ),
  function() {}
);

gulp.task('default', gulp.series('build', 'watch'), function () {});
