import { expect } from '@playwright/test'
import { test, login, create } from './helpers.ts'

test('A lifter can register for a competition', async ({ page }) => {
    await login(page, { role: 'member' })

    const competition = await create(page, 'Competition', { date: '2030-01-01' })

    await page.goto('/competitions')

    await expect(page.getByText('Du har inte meddelat om du vill tävla ännu').first()).toBeVisible()

    await page.getByText(competition.name).click();
    await page.locator('div').filter({ hasText: /^Herrar$/ }).getByRole('radio').check();
    await page.getByRole('combobox').selectOption('74');
    await page.locator('.w-11').first().click();
    await page.getByPlaceholder('Ev. kommentar/ytterligare').fill('Some comment');
    await page.getByRole('button', { name: 'Ja, jag vill tävla' }).click();
    await page.getByText('Grymt, vi ses där!').click();

    await page.goto('/competitions')

    await expect(page.getByText('Du har anmält intresse att tävla').first()).toBeVisible()
})

test('A lifter can register for an event', async ({ page }) => {
    await login(page, { role: 'member' })

    const event = await create(page, '\\App\\Models\\Event', { date: '2030-01-01' })

    await page.goto('/events')

    await expect(page.getByText('Du har inte meddelat om du kan delta').first()).toBeVisible()

    await page.getByText(event.name).click();
    await page.getByPlaceholder('Ev. kommentar/ytterligare').fill('Some comment');
    await page.getByRole('button', { name: 'Jag kan delta' }).click();
    await page.getByText('Grymt, vi ses där!').click();

    await page.goto('/events')

    await expect(page.getByText('Du är anmäld som funktionär, tack!')).toBeVisible()
})