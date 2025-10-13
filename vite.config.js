import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            buildDirectory: 'build', // penting untuk Laravel membaca manifest di lokasi yang benar
        }),
    ],
    build: {
        outDir: 'public_html/build',
        manifest: true,
        emptyOutDir: true,
    },
});
