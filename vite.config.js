export default defineConfig({
    server: {
        host: '0.0.0.0', // Stellt sicher, dass Vite auf allen Hosts verfügbar ist
        port: 5173
    },
    plugins: [
        ViteLiveReload('resources/views/**/*.blade.php', 'resources/scss/**/*.scss')
    ],
    css: {
        preprocessorOptions: {
            scss: {
                additionalData: `@import "resources/scss/bootstrap";`
            }
        }
    }
});
