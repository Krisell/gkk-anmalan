import { Page, test as base } from '@playwright/test'
import * as fs from 'node:fs'
import path from 'path'

export const test = base.extend({
  page: async ({ page }, use) => {
    // Allow app to detect running in Playwright via window.Playwright. Use
    // this scarcely and only if strictly necessary. One example is disabling
    // the auto-printing feature (and instead let the test verify what is
    // about to be printed).
    await page.addInitScript(() => {
      (window as any).Playwright = true
    })

    await page.route('**/*', route => {
      let blockedDomains = ['youtube', 'bugsnag', 'googletagmanager', 'google-analytics', 'bunny']

      // Blocking domains causes problems for the Vite dev server, so in the case where
      // tests are run against that, skip the blocking.
      if (fs.existsSync(path.join(__dirname, '../../bootstrap/cache/', 'vite'))) {
        blockedDomains = []
      }

      for (const domain of blockedDomains) {
        if (route.request().url().includes(domain)) {
          // console.warn('BLOCKED', route.request().url())
          return route.abort()
        }
      }

      // Uncomment below to see all requests in the console, which is very useful to
      // figure out what requests are being made and what to mock.
      // console.log(route.request().url())

      route.continue()
    })

    use(page)
  },
})

export const login = async (context, attributes: any = {}) => {
  const response = await context.request.post('/__e2e__/login', { data: { attributes } })

  return await response.json()
}

export const create = async (context, model: string, attributes: any = {}, times = 1) => {
  const response = await context.request.post('/__e2e__/factory', { data: { model, attributes, times } })

  return await response.json()
}

export const first = async (context, model: string, attributes: any = {}) => {
  const response = await context.request.post('/__e2e__/first', { data: { model, attributes } })

  return await response.json()
}

export const update = async (context, model: string, attributes: any = {}, values: any = {}) => {
  const response = await context.request.post('/__e2e__/update', { data: { model, attributes, values } })

  return await response.json()
}

export const php = async (context, command: string) => {
  const response = await context.request.post('/__e2e__/php', { data: { command } })

  return await response.json()
}

export const mockRoute = async (url: string, responsePayload: object, page: Page) => {
  return await page.route(`${url}`, (route) => {
    route.fulfill({ json: responsePayload })
  })
}
