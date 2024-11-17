import { test, expect } from '@playwright/test'

test('homepage has title and links to intro page', async ({ page }) => {
  await page.goto('/')
  await expect(page.locator('body')).toContainText('Göteborg Kraftsportklubb')

  await page.locator('[data-cy="navbar"] > a[href="/styrkelyft"]').click()
  await expect(page.locator('body')).toContainText(
    'I styrkelyft tävlar man i grenarna knäböj, bänkpress och marklyft'
  )

  await page.locator('[data-cy="navbar"] > a[href="/gkk"]').click()
  await expect(page.locator('body')).toContainText(
    'Göteborg Kraftsportklubb bildades 1933 och har idag omkring 100 medlemmar'
  )

  await page.locator('[data-cy="navbar"] > a[href="/medlem"]').click()
  await expect(page.locator('body')).toContainText('Vill du bli medlem?')

  await page.locator('[data-cy="navbar"] > a[href="/dokument"]').click()
  await expect(page.locator('body')).toContainText('Samarbete med SportRehab')

  await page.locator('[data-cy="navbar"] > a[href="/klubbrekord"]').click()
  await expect(page.locator('body')).toContainText('Kvinnor')

  await page.locator('[data-cy="inside"] > a[href="/insidan"]').click()
  await expect(page.locator('body')).toContainText('Skapa konto som medlem')
})
