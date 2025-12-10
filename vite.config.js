import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': '/resources',
        }
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    // Split large dependencies into separate chunks
                    'vendor-chart': ['chart.js'],
                    'vendor-alpine': ['alpinejs'],
                    'vendor-flatpickr': ['flatpickr'],
                },
            },
        },
    },
});