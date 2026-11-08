import { expect } from '@playwright/test'
import { test, login, php } from './helpers.ts'

test('An admin can edit the board shown on the Om GKK page', async ({ page }) => {
  // A made-up board, since the real one is not in the repository.
  const board = [
    'Ordförande',
    'Kassör',
    'Sekreterare',
    'Ledamot',
    'Ledamot',
    'Ledamot',
    'Ledamot',
    'Suppleant',
    'Suppleant',
  ]
  const names = [
    'Carl Ceder',
    'Ida Ek',
    'Alva Al',
    'Bo Björk',
    'Gun Gran',
    'Hans Hassel',
    'Lo Lind',
    'Mia Masur',
    'Olle Oxel',
  ]
  const json = JSON.stringify(board.map((role, i) => ({ name: names[i], role })))
  await php(page, `\\Illuminate\\Support\\Facades\\Artisan::call('gkk:import-board-members', ['source' => '${json}'])`)
  await login(page, { role: 'admin' })

  await page.goto('/admin/board')
  await expect(page.getByText('Carl Ceder')).toBeVisible()

  // Give an existing member areas of responsibility and a contact address.
  await page.locator('div.flex.items-center', { hasText: 'Ida Ek' }).getByRole('button', { name: 'Redigera' }).click()
  await page.getByLabel('Ansvarsområden').fill('Klubbkläder, Ungdomsträning')
  await page.getByLabel('E-post (visas publikt)').fill('klader@gkk-styrkelyft.se')
  await page.getByRole('button', { name: 'Spara' }).click()
  await expect(page.getByText('Klubbkläder · Ungdomsträning')).toBeVisible()

  // Add a new member, then move them to the top.
  await page.getByRole('button', { name: 'Lägg till' }).click()
  await page.getByLabel('Namn').fill('Nora Ny')
  await page.getByLabel('Roll').fill('Ledamot')
  await page.locator('form').getByRole('button', { name: 'Lägg till' }).click()
  await expect(page.getByText('Nora Ny')).toBeVisible()
  for (let i = 0; i < 9; i++) {
    await page.locator('div.flex.items-center', { hasText: 'Nora Ny' }).getByLabel('Flytta upp').click()
  }

  await page.screenshot({ path: 'test-results/admin-board.png', fullPage: true })

  await page.goto('/gkk')
  const cards = page.locator('ul[role="list"] li h3')
  await expect(cards.first()).toHaveText('Nora Ny')
  await expect(page.getByText('Vem kontaktar jag om …?')).toBeVisible()
  await expect(page.locator('dl dt', { hasText: 'Ungdomsträning' })).toBeVisible()
  await expect(page.locator('a[href="mailto:klader@gkk-styrkelyft.se"]').first()).toBeVisible()

  await page.locator('section', { hasText: 'Förtroendevalda' }).screenshot({ path: 'test-results/about-board.png' })
})
