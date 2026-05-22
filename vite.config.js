import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/views/permissions/permissions-table.js',
                'resources/views/menu-permissions/menu-permissions-table.js',
                'resources/views/employee-access/employee-access-table.js',
            ],
            refresh: true,
        }),
    ],
});
