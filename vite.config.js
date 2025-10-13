import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            buildDirectory: 'build', // penting: arahkan Laravel ke folder yang benar
        }),
    ],
    build: {
        outDir: 'build', // ubah dari public/build ke build saja
        manifest: true,
        emptyOutDir: true,
    },
});
