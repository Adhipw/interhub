import { defineConfig, devices } from '@playwright/test';

const port = Number(process.env.E2E_PORT || 8000);
const host = process.env.E2E_HOST || '127.0.0.1';
const baseURL = process.env.E2E_BASE_URL || `http://${host}:${port}`;
const baseUrlHost = new URL(baseURL).hostname;
const localHosts = new Set(['127.0.0.1', 'localhost', '::1']);

if (!localHosts.has(baseUrlHost) && process.env.E2E_ALLOW_REMOTE !== 'true') {
    throw new Error('Refusing to run E2E tests against a non-local URL. Set E2E_ALLOW_REMOTE=true only for an isolated test environment.');
}

export default defineConfig({
    testDir: './e2e',
    timeout: 60_000,
    workers: 1,
    expect: {
        timeout: 15_000,
    },
    fullyParallel: false,
    retries: process.env.CI ? 2 : 0,
    reporter: [['list']],
    use: {
        baseURL,
        trace: 'retain-on-failure',
        screenshot: 'only-on-failure',
        video: 'retain-on-failure',
    },
    projects: [
        {
            name: 'chromium',
            use: {
                ...devices['Desktop Chrome'],
                contextOptions: {
                    serviceWorkers: 'allow',
                },
            },
        },
    ],
    webServer: {
        command: [
            'php artisan db:seed --class=E2eNavigationSeeder',
            `php artisan serve --host=${host} --port=${port}`,
        ].join(' && '),
        cwd: '../backend',
        url: baseURL,
        reuseExistingServer: !process.env.CI,
        timeout: 120_000,
    },
});
