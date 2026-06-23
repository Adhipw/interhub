import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';
import path from 'path';
import { transform } from 'esbuild';
import viteCompression from 'vite-plugin-compression';

const stripConsolePlugin = () => ({
    name: 'strip-console-in-production',
    enforce: 'post' as const,
    async transform(code: string, id: string) {
        if (id.includes('node_modules')) return null;

        const isScript =
            id.endsWith('.js') ||
            id.endsWith('.ts') ||
            id.includes('.vue?') ||
            id.includes('lang.ts') ||
            id.includes('lang.js');

        if (!isScript) return null;

        const result = await transform(code, {
            loader: id.includes('lang.ts') || id.endsWith('.ts') ? 'ts' : 'js',
            drop: ['console', 'debugger'],
            minifySyntax: true,
            sourcemap: false,
            sourcefile: id,
        });

        return {
            code: result.code,
        };
    },
});

export default defineConfig(({ mode }) => {
    // Load env file from ../backend/.env
    const env = loadEnv(mode, path.resolve(__dirname, '../backend'), '');

    // Explicitly set process.env for the laravel plugin
    process.env.APP_URL = env.APP_URL;

    const host = env.VITE_LAN_MODE === 'true' ? '0.0.0.0' : '127.0.0.1';
    const devHost = host === '0.0.0.0' ? '127.0.0.1' : host;

    const port = 5174;

    return {
        envDir: '../backend',
        plugins: [
            laravel({
                input: ['resources/js/app.ts', 'resources/css/app.css'],
                refresh: true,
                publicDirectory: '../backend/public',
                buildDirectory: 'build',
            }),
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
            ...(mode === 'production' ? [stripConsolePlugin()] : []),
            tailwindcss(),
            viteCompression({ algorithm: 'brotliCompress' }) as any,
            viteCompression({ algorithm: 'gzip' }) as any,
        ],
        resolve: {
            alias: {
                '@': path.resolve(__dirname, './resources/js'),
                'vue': 'vue/dist/vue.esm-bundler.js',
            },
        },
        server: {
            host: host,
            port: port,
            strictPort: true,
            cors: true,
            origin: `http://${devHost}:${port}`,
            hmr: {
                host: devHost,
            },
            proxy: {
                '/api': {
                    target: env.APP_URL || 'http://127.0.0.1:8000',
                    changeOrigin: true,
                    secure: false,
                },
                '/storage': {
                    target: env.APP_URL || 'http://127.0.0.1:8000',
                    changeOrigin: true,
                    secure: false,
                },
            },
        },
        build: {
            outDir: '../backend/public/build',
            emptyOutDir: true,
            rollupOptions: {
                output: {
                    manualChunks: {
                        'vendor-vue': ['vue', '@inertiajs/vue3', 'pinia'],
                        'vendor-icons': ['lucide-vue-next'],
                        'vendor-ui': ['@headlessui/vue'],
                    },
                },
            },
        },
    };
});
