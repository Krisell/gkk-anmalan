import { expect } from '@playwright/test'
import { test, login, create } from './helpers.ts'

test('An admin can go from the event page to its admin page', async ({ page }) => {
  await login(page, { role: 'admin' })

  const event = await create(page, '\\App\\Models\\Event', { date: '2030-01-01' })

  await page.goto(`/events/${event.id}`)

  await page.getByRole('link', { name: 'Admin' }).click()

  await expect(page).toHaveURL(`/admin/events/${event.id}`)
})

test('An admin can go from the competition page to its admin page', async ({ page }) => {
  await login(page, { role: 'admin' })

  const competition = await create(page, 'Competition', { date: '2030-01-01' })

  await page.goto(`/competitions/${competition.id}`)

  await page.getByRole('link', { name: 'Admin' }).click()

  await expect(page).toHaveURL(`/admin/competitions/${competition.id}`)
})

test('A member does not see the admin link', async ({ page }) => {
  await login(page, { role: 'member' })

  const event = await create(page, '\\App\\Models\\Event', { date: '2030-01-01' })

  await page.goto(`/events/${event.id}`)

  await expect(page.getByRole('button', { name: 'Jag kan delta' })).toBeVisible()
  await expect(page.getByRole('link', { name: 'Admin' })).toBeHidden()
})
