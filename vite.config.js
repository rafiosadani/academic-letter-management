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
    build: {
        // 1. Menghilangkan peringatan (!) karena kita sudah tahu ukuran vendor memang besar
        chunkSizeWarningLimit: 2000,

        rollupOptions: {
            output: {
                // Memisahkan node_modules ke dalam file 'vendor'
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        // Pisahkan FontAwesome karena biasanya ini yang paling berat
                        if (id.includes('@fortawesome') || id.includes('font-awesome')) {
                            return 'vendor-icons';
                        }
                        // Pisahkan library UI besar lainnya (jika ada)
                        if (id.includes('alpinejs') || id.includes('axios')) {
                            return 'vendor-ui';
                        }
                        // Sisanya masuk ke core vendor
                        return 'vendor-core';
                    }
                },
                // 3. Memastikan penamaan file tetap rapi
                entryFileNames: `assets/[name]-[hash].js`,
                chunkFileNames: `assets/[name]-[hash].js`,
                assetFileNames: `assets/[name]-[hash].[ext]`,
            },
        },
    },
});
