import { expect } from '@playwright/test'
import { test, login, create } from './helpers.ts'

test('An admin can write minutes and print them', async ({ page }) => {
  await login(page, { role: 'admin', first_name: 'Carl', last_name: 'Öberg' })
  await create(page, 'User', { first_name: 'Philip', last_name: 'Waagiström' })

  await page.goto('/admin/minutes')
  await page.getByRole('button', { name: 'Nytt protokoll' }).click()
  await expect(page.getByRole('heading', { name: 'Nytt protokoll' })).toBeVisible()

  await page.getByPlaceholder('t.ex. Discord (distans)').fill('Discord (distans)')

  // Attendees are picked from members or typed in freely.
  const attendee = page.getByPlaceholder('Välj medlem eller skriv ett namn')
  await attendee.fill('Carl Öberg')
  await attendee.press('Enter')
  await attendee.fill('Philip Waagiström')
  await attendee.press('Enter')
  await attendee.fill('Extern Gäst')
  await page.getByRole('button', { name: 'Lägg till', exact: true }).click()
  await page.locator('button:not([aria-label])', { hasText: 'Extern Gäst' }).click()

  await page.getByLabel('Mötesordförande').fill('Carl Öberg')
  await page.getByLabel('Protokolljusterare').fill('Philip Waagiström')

  await page.getByPlaceholder('Rubrik').fill('Julfest')
  const body = page.locator('.rich-text-editor .tiptap').first()
  await body.click()
  await page.keyboard.type('En budget ')

  // The toolbar is hidden until toggled.
  await expect(page.getByTitle('Fetstil (⌘B)')).toHaveCount(0)
  await page.getByTitle('Visa formatering').first().click()
  await page.getByTitle('Fetstil (⌘B)').click()
  await page.keyboard.type('godkändes')

  await page.getByRole('button', { name: 'Lägg till punkt' }).click()
  await page.getByPlaceholder('Rubrik').nth(1).fill('Inköp')

  await page.screenshot({ path: 'test-results/minutes-editor.png', fullPage: true })

  await page.getByRole('button', { name: 'Spara' }).first().click()
  await expect(page.getByText('Protokollet är sparat.')).toBeVisible()
  await expect(page).toHaveURL(/\/admin\/minutes\/\d+$/)

  await page.goto(page.url() + '/print')
  await expect(page.getByText('Närvarande: Carl Öberg, Philip Waagiström')).toBeVisible()
  await expect(page.getByText('Frånvarande: Extern Gäst')).toBeVisible()
  await expect(page.getByText('Mötesordförande Carl Öberg öppnar mötet.')).toBeVisible()
  await expect(page.getByRole('heading', { name: '5. Julfest' })).toBeVisible()
  await expect(page.locator('strong', { hasText: 'godkändes' })).toBeVisible()
  await expect(page.getByRole('heading', { name: '6. Inköp' })).toBeVisible()
  await expect(page.getByRole('heading', { name: '8. Nästa möte' })).toBeVisible()
})

test('Minutes are reviewed by the adjuster and published to Dokument', async ({ page, browser }) => {
  await login(page, { role: 'admin' })
  await create(page, 'DocumentFolder', { name: 'Styrelseprotokoll', order: 1, only_administrators: false })

  await page.goto('/admin/minutes/new')
  await page.getByLabel('Protokolljusterare').fill('Philip Waagiström')
  await page.getByPlaceholder('Rubrik').fill('Julfest')
  await page.locator('.rich-text-editor .tiptap').first().click()
  await page.keyboard.type('En budget godkändes.')
  await page.getByRole('button', { name: 'Spara' }).first().click()
  await expect(page.getByText('Utkast', { exact: true })).toBeVisible()

  await page.getByRole('button', { name: 'Skicka för justering' }).click()
  await expect(page.getByText('Väntar på justering av Philip Waagiström')).toBeVisible()
  const reviewUrl = await page.locator('input[readonly]').inputValue()

  // The adjuster opens the link without being logged in.
  const adjuster = await (await browser.newContext()).newPage()
  await adjuster.goto(reviewUrl)
  await expect(adjuster.getByText('En budget godkändes.')).toBeVisible()
  await adjuster.getByRole('button', { name: 'Justera protokollet' }).click()
  await expect(adjuster.getByText('Protokollet är justerat')).toBeVisible()

  await page.reload()
  await expect(page.getByText(/Justerat .* av Philip Waagiström/)).toBeVisible()
  await expect(page.getByPlaceholder('Rubrik')).toBeDisabled()
  await page.screenshot({ path: 'test-results/minutes-approved.png' })

  await page.getByRole('button', { name: 'Publicera' }).click()
  await expect(page.getByText('Finns under Dokument i mappen ”Styrelseprotokoll”')).toBeVisible()

  await login(page, { first_name: 'Vanlig', last_name: 'Medlem' })
  await page.goto('/member-documents')
  await expect(page.getByText(/Styrelsemöte \d+, \d{4} \(/)).toBeVisible()

  const popup = page.waitForEvent('popup')
  await page.getByRole('button', { name: 'Visa' }).click()
  const published = await popup
  await expect(published.getByText('En budget godkändes.')).toBeVisible()
  await expect(published.getByText(/Justerat .* av Philip Waagiström/)).toBeVisible()
})
