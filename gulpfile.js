var gulp = require("gulp");
var plugin = require("gulp-load-plugins")();
var webpack = require('webpack-stream');


var destAssetsDir = "./assets";


// Stylus
gulp.task("css-app", function () {
  return (
    gulp.src([
      "./src/stylus/app.styl",
      "./src/stylus/libs.styl",
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

gulp.task("css-minify", function () {
  return gulp.src([
      destAssetsDir + "/css/app.css",
      destAssetsDir + "/css/libs.css",
      destAssetsDir + "/css/admin.css",
    ])
    .pipe(plugin.cleanCss())
    .pipe(plugin.rename({
      suffix: '.min'
    }))
    .pipe(gulp.dest(destAssetsDir + "/css/"));
});

gulp.task("css-bundle", function () {
  return gulp.src([
      './assets/**/libs.min.css',
      './assets/**/app.min.css'
    ])
    .pipe(plugin.concat("bundle.min.css"))
    .pipe(gulp.dest(destAssetsDir + "/css/"))
});

gulp.task("css", gulp.series("css-app", "css-minify", "css-bundle"));


// JS
gulp.task("js-app", function () {
  return gulp.src('./src/js/*.js')
    .pipe(webpack({
      mode: 'development',
      entry: {
        main: './src/js/main.js',
        admin: './src/js/admin.js',
        libs: './src/js/libs.js'
      },
      output: {
        filename: '[name].js',
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
      },
      devtool: 'source-map',
    }))
    .on("error", plugin.notify.onError("<%= error.message %>"))
    .pipe(gulp.dest(destAssetsDir + "/js"))
});

gulp.task("js-minify", function () {
  return gulp.src([
      destAssetsDir + "/js/main.js",
      destAssetsDir + "/js/libs.js",
      destAssetsDir + "/js/admin.js"
    ])
    .pipe(plugin.uglify())
    .pipe(plugin.rename({
      suffix: '.min'
    }))
    .pipe(gulp.dest(destAssetsDir + "/js"));
});

gulp.task("js-bundle", function () {
  return gulp.src([
      './assets/**/libs.min.js',
      './assets/**/main.min.js'
    ])
    .pipe(plugin.concat("bundle.min.js"))
    .pipe(gulp.dest(destAssetsDir + "/js/"))
});

gulp.task("js", gulp.series(
  "js-app",
  "js-minify",
  "js-bundle"
));


// SVG
gulp.task("svg-sprite", function () {
  return gulp
    .src([
      "./assets/icons/*.svg",
      "!./assets/icons/sprite*.svg"
    ])
    .pipe(plugin.svgmin({
      plugins: [{
          name: 'removeAttrs',
          params: "(fill|stroke|opacity)"
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
    .pipe(plugin.svgmin())
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

  gulp.watch("./src/js/**/*.js", gulp.series("js", "reloader"));
  gulp.watch("./src/stylus/**/*.styl", gulp.series("css", "reloader"));

  gulp.watch(["*.php", "views/**/*.php"], function php(done) {
    plugin.livereload.reload();
    done();
  });
});


gulp.task("media", gulp.series("svg", "reloader"), function () {});


gulp.task("build", gulp.series(
    "svg",
    "css",
    "js"
  ),
  function () {}
);


gulp.task("default", gulp.series("build", "watch"), function () {});
