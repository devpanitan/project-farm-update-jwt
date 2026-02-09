
import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ command, mode }) => {
    // Load environment variables from .env to get APP_URL
    const env = loadEnv(mode, process.cwd(), '');

    // Get the base hostname from APP_URL
    let appHostname = new URL(env.APP_URL).hostname;

    // In Cloud Workstations, the app preview URL is prefixed with the port (e.g., 8000-).
    // We need to strip this prefix before constructing the Vite HMR URL.
    if (appHostname.startsWith('8000-')) {
        appHostname = appHostname.substring(5);
    }

    const vitePort = 5173;
    const viteHost = `${vitePort}-${appHostname}`;
    const viteDevServerUrl = `https://${viteHost}`;

    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
                // Explicitly tell the plugin what the dev server URL is.
                devServerUrl: viteDevServerUrl,
            }),
        ],
        server: {
            // Listen on all network interfaces
            host: '0.0.0.0',
            port: vitePort,
            cors: true,
            hmr: {
                // The HMR client needs to connect to the public-facing URL
                host: viteHost,
                protocol: 'wss',
            },
        },
    };
});
