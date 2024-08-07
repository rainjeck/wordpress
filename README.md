# wordpress

Starting template theme for WordPress

1. `git clone https://github.com/rainjeck/wordpress.git`

2. Rename:

    `env` to `.env`

    `composer-prod.json` to `composer.json`

    `package-prod.json` to `package.json`

3. `composer require vlucas/phpdotenv cmb2/cmb2 ifsnop/mysqldump-php`

4. Install devDependencies

    ```
    npm i @babel/core @babel/preset-env @rollup/plugin-commonjs @rollup/plugin-node-resolve babel-loader gulp gulp-autoprefixer@8 gulp-babel gulp-clean-css gulp-concat gulp-include gulp-livereload gulp-load-plugins gulp-notify gulp-rename gulp-sourcemaps gulp-stylus gulp-svg-symbol-view gulp-uglify rollup -D
    ```

5. Install dependencies

    ```
    npm i modern-css-reset svg4everybody formbouncerjs
    ```

6. `gulp build`

## Images

```
thumbnail - 150 [crop]
medium - 1024
medium_large - 1440
large - 2048

--- From theme: ---
thumblarge - 500 [crop]
small - 768
```
