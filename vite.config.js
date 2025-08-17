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
    resolve: {
        alias: {
            'trix': 'https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js',
            '@editorjs/editorjs': 'https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest',
            '@editorjs/header': 'https://cdn.jsdelivr.net/npm/@editorjs/header@latest',
            '@editorjs/list': 'https://cdn.jsdelivr.net/npm/@editorjs/list@latest',
            '@editorjs/image': 'https://cdn.jsdelivr.net/npm/@editorjs/image@latest',
            'chart.js': 'https://cdn.jsdelivr.net/npm/chart.js',
            'sortablejs': 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js',
        },
    },
});