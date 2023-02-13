var gulp = require("gulp");
var plugin = require("gulp-load-plugins")();
var webpack = require('webpack-stream');


var destAssetsDir = "./assets";


// Stylus
gulp.task("css-app", function () {
  return (
    gulp.src([
      "./src/stylus/main.styl",
      "./src/stylus/admin.styl",
    ])
    .pipe(plugin.sourcemaps.init())
    .pipe(plugin.stylus({
      'include css': true
    }).on("error",
      plugin.notify.onError("<%= error.message %>")))
    .pipe(plugin.autoprefixer({
      remove: false,
      cascade: false
    }))
    .pipe(plugin.sourcemaps.write("../css"))
    .pipe(gulp.dest(destAssetsDir + "/css"))
  );
});

gulp.task("css-app-minify", function () {
  return gulp.src([
      destAssetsDir + "/css/main.css",
      destAssetsDir + "/css/admin.css",
    ])
    .pipe(plugin.cleanCss())
    .pipe(plugin.rename({
      suffix: '.min'
    }))
    .pipe(gulp.dest(destAssetsDir + "/css/"));
});


gulp.task("css-libs", function () {
  return (
    gulp.src([
      "./src/stylus/libs.styl"
    ])
    // .pipe(plugin.sourcemaps.init())
    .pipe(plugin.stylus({
      'include css': true
    }).on("error",
      plugin.notify.onError("<%= error.message %>")))
    .pipe(plugin.autoprefixer({
      remove: false,
      cascade: false
    }))
    .pipe(plugin.cleanCss())
    .pipe(plugin.rename({
      suffix: '.min'
    }))
    // .pipe(plugin.sourcemaps.write("../css"))
    .pipe(gulp.dest(destAssetsDir + "/css"))
  );
});

gulp.task("css-bundle", function () {
  return gulp.src([
      './assets/**/libs.min.css',
      './assets/**/main.min.css'
    ])
    .pipe(plugin.concat("bundle.min.css"))
    .pipe(gulp.dest(destAssetsDir + "/css/"))
});

gulp.task("css-app-bundle", gulp.series("css-app", "css-app-minify", "css-bundle"));
gulp.task("css-libs-bundle", gulp.series("css-libs", "css-bundle"));
gulp.task("css", gulp.series("css-libs", "css-app", "css-app-minify", "css-bundle"));



// JS
gulp.task('js-app', function () {
  return gulp.src(['./src/js/main.js', './src/js/admin.js'])
    .pipe(plugin.sourcemaps.init())
    .pipe(plugin.include()).on('error', console.log)
    .pipe(plugin.babel({
      presets: ['@babel/env']
    }))
    .pipe(plugin.sourcemaps.write("../js"))
    .pipe(gulp.dest(destAssetsDir + "/js"))
});

gulp.task("js-app-minify", function () {
  return gulp.src([
      destAssetsDir + "/js/main.js",
      destAssetsDir + "/js/admin.js"
    ])
    .pipe(plugin.uglify())
    .pipe(plugin.rename({
      suffix: '.min'
    }))
    .pipe(gulp.dest(destAssetsDir + "/js"));
});


gulp.task("js-libs", function () {
  return gulp.src('./src/js/libs.js')
    .pipe(webpack({
      mode: 'production',
      entry: {
        libs: './src/js/libs.js',
      },
      output: {
        filename: '[name].min.js',
      },
      module: {
        rules: [{
          test: /\.js$/,
          use: {
            loader: 'babel-loader',
            options: {
              presets: ['@babel/preset-env']
            }
          }
        }]
      }
    }))
    .on("error", plugin.notify.onError("<%= error.message %>"))
    .pipe(gulp.dest(destAssetsDir + "/js"))
});

gulp.task("js-bundle", function () {
  return gulp.src([
      './assets/**/libs.min.js',
      './assets/**/main.min.js'
    ])
    .pipe(plugin.uglify())
    .pipe(plugin.concat("bundle.min.js"))
    .pipe(gulp.dest(destAssetsDir + "/js/"))
});

gulp.task("js-app-bundle", gulp.series("js-app", "js-app-minify", "js-bundle"));
gulp.task("js-libs-bundle", gulp.series("js-libs", "js-bundle"));
gulp.task("js", gulp.series("js-libs", "js-app", "js-app-minify", "js-bundle"));


// SVG
gulp.task("svg-sprite", function () {
  return gulp
    .src([
      "./assets/icons/*.svg",
      "!./assets/icons/sprite*.svg"
    ])
    .pipe(plugin.svgmin({
      multipass: true,
      plugins: [{
        name: 'removeAttrs',
        params: {
          attrs: "(fill|stroke|opacity|color|style)"
        }
      }]
    }))
    .pipe(plugin.svgSprite({
      mode: {
        symbol: {
          sprite: "sprite.svg",
          bust: false,
          dest: ""
        }
      }
    }))
    .pipe(gulp.dest(destAssetsDir + "/icons"));
});

gulp.task("svg-sprite-color", function () {
  return gulp.src([
      "./assets/icons/*.svg",
      "!./assets/icons/sprite*.svg"
    ])
    .pipe(plugin.svgmin({ multipass: true }))
    .pipe(plugin.svgSprite({
      mode: {
        symbol: {
          sprite: "sprite-color.svg",
          bust: false,
          dest: ""
        }
      }
    }))
    .pipe(gulp.dest(destAssetsDir + "/icons"));
});

gulp.task("svg", gulp.series("svg-sprite", "svg-sprite-color"));


// Reloader
gulp.task("reloader", function (done) {
  plugin.livereload.changed("/");
  done();
});


// Watch
gulp.task("watch", function () {
  plugin.livereload.listen({
    basePath: "./",
    start: true
  });

  // CSS
  gulp.watch(["./src/stylus/**/*.styl", '!./src/stylus/**/libs.styl'], gulp.series("css-app-bundle", "reloader"));
  gulp.watch(["./src/stylus/**/libs.styl"], gulp.series("css-libs-bundle", "reloader"));

  // JS
  gulp.watch(["./src/js/**/*.js", "!./src/js/**/libs.js"], gulp.series("js-app-bundle", "reloader"));
  gulp.watch(["./src/js/**/libs.js"], gulp.series("js-libs-bundle", "reloader"));

  // PHP
  gulp.watch(["*.php", "views/**/*.php"], function php(done) {
    plugin.livereload.reload();
    done();
  });
});

gulp.task("media", gulp.series("svg", "reloader"), function () {});

gulp.task("build", gulp.series( "svg", "css", "js" ),
  function () {}
);

gulp.task("default", gulp.series("build", "watch"), function () {});
