const path = require('path');
const { src, dest, series, watch } = require('gulp');
const load_plugin = require('gulp-load-plugins')();
const minifyjs = require('gulp-minify.js');

const rollup_resolve = require('@rollup/plugin-node-resolve');
const rollup_commonjs = require('@rollup/plugin-commonjs');

const _path = './assets';

// --- CSS ---
function css_app() {
    return src([
        './src/stylus/*.styl',
        '!./src/stylus/*libs*',
    ])
    .pipe(load_plugin.sourcemaps.init())
    .pipe(load_plugin.stylus({
        'include css': true,
        compress: false
    }).on('error', load_plugin.notify.onError('<%= error.message %>')))
    .pipe(load_plugin.autoprefixer({
        remove: false,
        cascade: false,
    }))
    .pipe(load_plugin.sourcemaps.write('../css'))
    .pipe(dest(_path + '/css'));
};

function css_libs() {
    return src([
        './src/stylus/*libs*',
    ])
    .pipe(load_plugin.stylus({
        'include css': true,
        compress: false
    }).on('error', load_plugin.notify.onError('<%= error.message %>')))
    .pipe(dest(_path + '/css/'))
};

function css_bundle() {
    return src([
        _path + '/css/libs.css',
        _path + '/css/main.css',
    ])
    .pipe(load_plugin.concat('bundle.css'))
    .pipe(dest(_path + '/css/'));
};

function css_minify() {
    return src([
        _path + '/css/*.css',
        '!' + _path + '/css/*.min.css',
    ])
    .pipe(load_plugin.cleanCss())
    .pipe(load_plugin.rename({
        suffix: '.min'
    }))
    .pipe(dest(_path + '/css/'));
};

const css = series(css_app, css_libs, css_bundle, css_minify);


// --- JS ---
function js_app() {
    return src([
        './src/js/*.js',
        '!./src/js/*libs*',
    ])
    .pipe(load_plugin.sourcemaps.init())
    .pipe(load_plugin.include()).on('error', console.log)
    .pipe(load_plugin.babel({ presets: [ '@babel/env' ] }))
    .pipe(load_plugin.sourcemaps.write('../js'))
    .pipe(dest(_path + '/js'))
};

function js_libs() {
    return src([
        './src/js/libs.js',
        './src/js/admin-libs.js',
    ])
    .pipe(
        load_plugin.rollupEach(
            {
                plugins: [ rollup_commonjs(), rollup_resolve() ]
            },
            file => {
                return {
                    format: 'umd',
                    name: path.basename(file.path, '.js')
                }
            }
        )
    )
    .pipe(dest(_path + '/js'));
};

function js_bundle() {
    return src([
        _path + '/js/libs.js',
        _path + '/js/main.js',
    ])
    .pipe(load_plugin.concat('bundle.js'))
    .pipe(dest(_path + '/js/'))
};

function js_minify() {
    return src([
        _path + '/js/*.js',
        '!' + _path + '/js/*.min.js',
    ])
    .pipe(minifyjs())
    .pipe(load_plugin.rename({
        suffix: '.min'
    }))
    .pipe(dest(_path + '/js'));
};

const js = series(js_app, js_libs, js_bundle, js_minify);


// --- SVG ---
function svg_mono() {
    return src([
        _path + '/icons/*.svg',
        '!' + _path + '/icons/sprit*.svg',
    ])
    .pipe(load_plugin.svgSymbolView({
        name: 'sprite',
        svgo: { plugins: [
            { removeAttrs: {attrs: '(fill-rule|clip-rule|fill|color|stroke-linecap|stroke-linejoin|style)'} }
        ]}
    }))
    .pipe(dest(_path + '/icons'));

};

function svg_color() {
    return src([
        _path + '/icons/*.svg',
        '!' + _path + '/icons/sprit*.svg',
    ])
    .pipe(load_plugin.svgSymbolView({
        name: 'sprite-color',
    }))
    .pipe(dest(_path + '/icons'));
};

const svg = series(svg_mono, svg_color);


// --- TOOLS ---
function reloader(done) {
    load_plugin.livereload.changed('/');
    done();
};

function watching() {
    load_plugin.livereload.listen({
        basePath: './',
        start: true
    });

    // --- CSS
    watch(['./src/stylus/**/*.styl', '!./src/stylus/**/libs.styl'], series(css_app, css_bundle, css_minify, reloader));
    watch(['./src/stylus/**/libs.styl'], series(css_libs, css_bundle, css_minify, reloader));

    // --- JS
    watch(['./src/js/**/*.js', '!./src/js/**/*libs*'], series(js_app, js_bundle, js_minify, reloader));
    watch(['./src/js/**/*libs*'], series(js_libs, js_bundle, js_minify, reloader));

    // --- PHP
    watch(['*.php', 'views/**/*.php'], reloader);
};

const build = series(svg, css, js);

exports.css = css;
exports.js = js;
exports.svg = svg;

exports.build = build;
exports.watching = watching;

exports.default = series(build);
