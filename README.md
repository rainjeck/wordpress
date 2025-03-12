# wordpress

Starting template theme for WordPress

1. `git clone https://github.com/rainjeck/wordpress.git`

2. Rename:

    `env` to `.env`

    `composer.example.json` to `composer.json`

    `package.example.json` to `package.json`

3. `composer require vlucas/phpdotenv cmb2/cmb2 ifsnop/mysqldump-php`

4. Install devDependencies

    ```
    npm i @babel/core @babel/preset-env @rollup/plugin-commonjs @rollup/plugin-node-resolve @rollup/plugin-replace babel-loader gulp gulp-autoprefixer@8 gulp-babel gulp-clean-css gulp-concat gulp-include gulp-livereload gulp-load-plugins gulp-minify.js gulp-notify gulp-rename gulp-rollup-each gulp-sourcemaps gulp-stylus gulp-svg-symbol-view gulp-uglify path -D
    ```

5. Install dependencies

    ```
    npm i modern-css-reset svg4everybody formbouncerjs drag-on-drop imask lightgallery.js swiper@6
    ```

6. `gulp build`

## Images

```
thumbnail - 512 x 512 [crop]
medium - 1024 x 1024
large - 2048 x 2048
original - 2048 x 2048
medium_large - off

--- From theme: ---
thumblarge - 500 x 500 [crop]
small - 768 x 2048
```
