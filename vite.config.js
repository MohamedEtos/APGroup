import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/assets/css/nucleo-icons.css',
                'resources/assets/css/nucleo-svg.css',
                'resources/assets/css/soft-ui-dashboard.css',
                'resources/css/sections/tables.css',
                'resources/css/sections/receive-invoices-form.css',
                'resources/css/sections/office-invoices.css',
                'resources/css/sections/invoice-receipt.css',
                'resources/css/sections/edit-invoice.css',
                'resources/assets/js/core/popper.min.js',
                'resources/assets/js/core/bootstrap.min.js',
                'resources/assets/js/plugins/perfect-scrollbar.min.js',
                'resources/assets/js/plugins/smooth-scrollbar.min.js',
                'resources/assets/js/plugins/chartjs.min.js',
                'resources/assets/js/soft-ui-dashboard.min.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
