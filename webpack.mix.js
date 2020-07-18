const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.ts('resources/js/index.ts', 'public/js/app.js')
.sass('resources/sass/app.scss', 'public/css/app.css');

if (mix.inProduction()) {
  mix.version();
} else {
  mix.browserSync({
    proxy: process.env.MIX_APP_URL
  });
}
