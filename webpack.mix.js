let mix = require('laravel-mix');
require('laravel-mix-tailwind');

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/scss/style.scss', 'public/css')
    .options({
        postCss: [require('tailwindcss'), require('autoprefixer')],
    })
    .tailwind('./tailwind.config.js');