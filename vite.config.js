import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// vite.config.js
export default defineConfig({
    plugins: [
        laravel({
            input: [
            'resources/css/app.css',
            'resources/js/app.js',
            //Admin
            'resources/css/admin/dashboard.css',
            'resources/js/admin/dashboard.js',
            // Admin Projects CSS
            'resources/css/admin/projects/create.css',
            'resources/css/admin/projects/edit.css',
            'resources/css/admin/projects/index.css',
            'resources/css/admin/projects/show.css',
            // Admin Projects JS
            'resources/js/admin/projects/create.js',
            'resources/js/admin/projects/edit.js',
            'resources/js/admin/projects/index.js',
            'resources/js/admin/projects/show.js',
            // Admin Reports CSS an JS
            'resources/css/admin/reports/index.css',
            'resources/js/admin/reports/index.js',
            ],
            refresh: true,
        }),
    ],
});