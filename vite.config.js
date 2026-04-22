import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// vite.config.js
export default defineConfig({
    plugins: [
        laravel({
            input: [
            'resources/css/app.css',
            'resources/js/app.js',
            'resources/css/welcome.css',
            'resources/js/welcome.js',
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
            //Admin Users CSS and JS
            'resources/css/admin/users/index.css',
            'resources/js/admin/users/index.js',
            //User
            'resources/css/user/dashboard.css',
            'resources/js/user/dashboard.js',
            //User Project CSS
            'resources/css/user/projects/show.css',
            //User Project JS
            'resources/js/user/projects/show.js'
            ],
            refresh: true,
        }),
    ],
    // server: {
    //     host: '0.0.0.0', // allow access from network
    //     port: 5173,
    //     strictPort: true,

    //     cors: true,
    //     hmr: {
    //         host: '192.168.1.24', // 👈 your local IP
    //     },
    // },
});
