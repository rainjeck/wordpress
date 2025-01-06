const { src, dest, series, watch } = require('gulp');
const load_plugin = require('gulp-load-plugins')();
const minifyjs = require('gulp-minify.js');

const rollup = require('rollup');
const rollup_resolve = require('@rollup/plugin-node-resolve');
const rollup_commonjs = require('@rollup/plugin-commonjs');

const path = './assets';

// --- CSS ---
function css_app() {
    return src([
        './src/stylus/*.styl',
        '!./src/stylus/libs.styl',
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
    .pipe(dest(path + '/css'));
};

function css_libs() {
    return src([
        './src/stylus/libs.styl',
    ])
    .pipe(load_plugin.stylus({
        'include css': true,
        compress: false
    }).on('error', load_plugin.notify.onError('<%= error.message %>')))
    .pipe(dest(path + '/css/'))
};

function css_bundle() {
    return src([
        path + '/css/libs.css',
        path + '/css/main.css',
    ])
    .pipe(load_plugin.concat('bundle.css'))
    .pipe(dest(path + '/css/'));
};

function css_minify() {
    return src([
        path + '/css/*.css',
        '!' + path + '/css/*.min.css',
    ])
    .pipe(load_plugin.cleanCss())
    .pipe(load_plugin.rename({
        suffix: '.min'
    }))
    .pipe(dest(path + '/css/'));
};

const css = series(css_app, css_libs, css_bundle, css_minify);


// --- JS ---
function js_app() {
    return src([
        './src/js/*.js',
        '!./src/js/libs.js',
    ])
    .pipe(load_plugin.sourcemaps.init())
    .pipe(load_plugin.include()).on('error', console.log)
    .pipe(load_plugin.babel({ presets: [ '@babel/env' ] }))
    .pipe(load_plugin.sourcemaps.write('../js'))
    .pipe(dest(path + '/js'))
};

function js_libs() {
    return rollup.rollup({
        input: './src/js/libs.js',
        plugins: [ rollup_commonjs(), rollup_resolve() ],
    })
    .then(bundle => {
        return bundle.write({
            file: path + '/js/libs.js',
            format: 'umd',
            sourcemap: false,
        });
    });
};

function js_bundle() {
    return src([
        path + '/js/libs.js',
        path + '/js/main.js',
    ])
    .pipe(load_plugin.concat('bundle.js'))
    .pipe(dest(path + '/js/'))
};

function js_minify() {
    return src([
        path + '/js/*.js',
        '!' + path + '/js/*.min.js',
    ])
    .pipe(minifyjs())
    .pipe(load_plugin.rename({
        suffix: '.min'
    }))
    .pipe(dest(path + '/js'));
};

const js = series(js_app, js_libs, js_bundle, js_minify);


// --- SVG ---
function svg_bw() {
    return src([
        path + '/icons/*.svg',
        '!' + path + '/icons/sprit*.svg',
    ])
    .pipe(load_plugin.svgSymbolView({
        name: 'sprite',
        svgo: { plugins: [
            { removeAttrs: {attrs: '(fill-rule|clip-rule|fill|color|stroke-linecap|stroke-linejoin|style)'} }
        ]}
    }))
    .pipe(dest(path + '/icons'));

};

function svg_color() {
    return src([
        path + '/icons/*.svg',
        '!' + path + '/icons/sprit*.svg',
    ])
    .pipe(load_plugin.svgSymbolView({
        name: 'sprite-color',
    }))
    .pipe(dest(path + '/icons'));
};

const svg = series(svg_bw, svg_color);


// --- TOOLS ---
function reloader(done) {
    load_plugin.livereload.changed('/');
    done();
};

function look() {
    load_plugin.livereload.listen({
        basePath: './',
        start: true
    });

    // --- CSS
    watch(['./src/stylus/**/*.styl', '!./src/stylus/**/libs.styl'], series(css_app, css_bundle, css_minify, reloader));
    watch(['./src/stylus/**/libs.styl'], series(css_libs, css_bundle, css_minify, reloader));

    // --- JS
    watch(['./src/js/**/*.js', '!./src/js/**/libs.js'], series(js_app, js_bundle, js_minify, reloader));
    watch(['./src/js/**/libs.js'], series(js_libs, js_bundle, js_minify, reloader));

    // --- PHP
    watch(['*.php', 'views/**/*.php'], reloader);
};

const build = series(svg, css, js);

exports.css = css;
exports.js = js;
exports.svg = svg;

exports.build = build;
exports.look = look;

exports.default = series(build);
