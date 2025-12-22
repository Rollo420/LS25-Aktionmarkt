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
            host: 'woodlypi.ddns.net', //arbeit 1woodlypi.ddns.net
        },
        watch: {
            usePolling: true,
        }
    }
});
