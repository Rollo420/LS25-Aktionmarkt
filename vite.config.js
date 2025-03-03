import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/postcss7-compat';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/scss/style.scss', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    css: {
        postcss: {
            plugins: [
                tailwindcss,
            ],
        },
    },
});
