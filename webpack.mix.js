let mix = require('laravel-mix');
require('laravel-mix-tailwind');

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .options({
        postCss: [require('tailwindcss'), require('autoprefixer')],
    })
    .tailwind('./tailwind.config.js');