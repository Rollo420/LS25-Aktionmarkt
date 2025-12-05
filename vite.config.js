import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['/resources/sass/app.scss', '/resources/js/app.js', '/resources/css/app.css'],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            // external: ['laravel-echo', 'pusher-js']
        }
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: '172.20.10.2',
        },
        watch: {
            usePolling: true,
        }
    }
});
