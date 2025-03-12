const path = require('path');
const { src, dest, series, watch } = require('gulp');
const load_plugin = require('gulp-load-plugins')();
const minifyjs = require('gulp-minify.js');

const rollup_resolve = require('@rollup/plugin-node-resolve');
const rollup_commonjs = require('@rollup/plugin-commonjs');
const rollup_replace = require('@rollup/plugin-replace');

const _path = './assets';

// --- CSS ---
function css_app() {
    return src([
        './src/stylus/*.styl',
    ])
    .pipe(load_plugin.sourcemaps.init())
    .pipe(load_plugin.stylus({
        'include css': true,
        compress: true
    }).on('error', load_plugin.notify.onError('<%= error.message %>')))
    .pipe(load_plugin.autoprefixer({
        remove: false,
        cascade: false,
    }))
    .pipe(load_plugin.cleanCss())
    .pipe(load_plugin.sourcemaps.write('../css'))
    .pipe(dest(_path + '/css'));
};

const css = series(css_app);


// --- JS ---
function js_app() {
    return src([
        './src/js/*.js',
    ])
    .pipe(load_plugin.sourcemaps.init())
    .pipe(
        load_plugin.rollupEach(
            {
                plugins: [
                  rollup_commonjs(),
                  rollup_resolve(),
                  rollup_replace({ 'process.env.NODE_ENV': JSON.stringify( 'production' ), preventAssignment: true })
                ]
            },
            file => {
                return {
                    format: 'umd',
                    name: path.basename(file.path, '.js')
                }
            }
        )
    )
    .pipe(minifyjs())
    .pipe(load_plugin.sourcemaps.write("../js"))
    .pipe(dest(_path + '/js'));
};

const js = series(js_app);


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
    watch(['./src/stylus/**/*.styl'], series(css_app, reloader));

    // --- JS
    watch(['./src/js/**/*.js'], series(js_app, reloader));

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
