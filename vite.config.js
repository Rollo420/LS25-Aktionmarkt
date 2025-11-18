import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/sass/app.scss', 'resources/js/app.js', 'resources/css/app.css'],
            refresh: true,
        }),
    ],
    server: {
        host: '10.45.1.228',
        port: 5173,
        hmr: {
            host: '10.45.1.228',
        },
        watch: {
            usePolling: true,
        }
    }
});
