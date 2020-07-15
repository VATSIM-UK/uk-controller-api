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

mix.ts('resources/js/index.ts', 'public/js/app.js');

if (mix.inProduction()) {
  mix.version();
}

mix.browserSync({
  proxy: 'ukcp.devapp/web'
});
