import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/notifications.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            external: [
                'https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js',
                'https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest',
                'https://cdn.jsdelivr.net/npm/@editorjs/header@latest',
                'https://cdn.jsdelivr.net/npm/@editorjs/list@latest',
                'https://cdn.jsdelivr.net/npm/@editorjs/image@latest',
                'https://cdn.jsdelivr.net/npm/chart.js',
                'https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js',
            ],
        },
    },
});
