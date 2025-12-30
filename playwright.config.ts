import { devices } from '@playwright/test';
import { defineConfig } from '@playwright/test';

const SERVER_PORT = 65456;

export default defineConfig({
  testDir: './tests/e2e',
  globalSetup: require.resolve('./tests/e2e/setup.mjs'),
  webServer: {
    command: `php artisan octane:start --workers=1 --port=${SERVER_PORT}`,
    url: `http://localhost:${SERVER_PORT}`,
    env: {
      SESSION_SECURE_COOKIE: 'false', // Allow app to run on non-https
      DB_CONNECTION: 'sqlite-e2e',
      TELESCOPE_ENABLED: 'false',
      DEBUGBAR_ENABLED: 'false',
      MAIL_MAILER: 'array',
      APP_ENV: 'testing',
      BCRYPT_ROUNDS: '4',
    }
  },
  timeout: 30 * 1000,
  expect: {
    timeout: 5000
  },
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 1,
  workers: process.env.CI ? 1 : undefined,
  reporter: 'list',
  use: {
    actionTimeout: 0,
    baseURL: `http://localhost:${SERVER_PORT}`,
    trace: 'on-first-retry',
  },
  projects: [
    {
      name: 'chromium',
      use: {
        ...devices['Desktop Chrome'],
      },
    },
  ],
  outputDir: 'test-results/',
});
