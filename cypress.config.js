const { defineConfig } = require('cypress')

module.exports = defineConfig({
  video: false,
  pageLoadTimeout: 30000,
  viewportWidth: 1200,
  viewportHeight: 800,
  scrollBehavior: 'center',
  retries: 3,
  blockHosts: [
    '*google-analytics*',
    '*googletagmanager*',
    '*bugsnag*',
    '*youtube*',
  ],
  e2e: {
    baseUrl: 'http://127.0.0.1:65456',
  },
})
