import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue2';
const path = require('path');

export default defineConfig({
    resolve: {
        alias: {
            '@': path.resolve('resources/js'),
            ziggy: path.resolve('vendor/tightenco/ziggy/dist/vue'), // or 'vendor/tightenco/ziggy/dist/vue' if you're using the Vue plugin
        }
    },
    plugins: [
        laravel({
            input: [
                'resources/js/app.js'
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
