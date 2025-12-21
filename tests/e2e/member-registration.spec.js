import { expect } from '@playwright/test'
import { test, login, create } from './helpers.ts'

test('A lifter can register for a competition', async ({ page }) => {
    await login(page, { role: 'member', explicit_registration_approval: true })

    const competition = await create(page, 'Competition', { date: '2030-01-01' })

    await page.goto('/competitions')

    await page.getByText(competition.name).click();
    await page.locator('div').filter({ hasText: /^Herrar$/ }).getByRole('radio').check();
    await page.getByRole('combobox').selectOption('74');
    await page.locator('.w-11').first().click();
    await page.getByPlaceholder('Ev. kommentar/ytterligare').fill('Some comment');
    await page.getByRole('button', { name: 'Ja, jag vill tävla' }).click();
    await expect(page.getByText('Information om tävlingsavgifter')).toBeVisible()
    await expect(page.getByText('Information om funktionärskrav')).toBeVisible()

    await page.goto('/competitions')

    await expect(page.getByText('Du har anmält intresse att tävla').first()).toBeVisible()
})

test('A lifter without explicit approval can register for a competition', async ({ page }) => {
    const user = await login(page, { role: 'member', explicit_registration_approval: true })

    const competition = await create(page, 'Competition', { date: '2030-01-01' })
    const event = await create(page, '\\App\\Models\\Event', { date: '2025-12-20' })
    await create(page, 'EventRegistration', {
        user_id: user.id,
        event_id: event.id,
        status: true,
        presence_confirmed: true,
    })

    await page.goto('/competitions')

    await page.getByText(competition.name).click();
    await page.locator('div').filter({ hasText: /^Herrar$/ }).getByRole('radio').check();
    await page.getByRole('combobox').selectOption('74');
    await page.locator('.w-11').first().click();
    await page.getByPlaceholder('Ev. kommentar/ytterligare').fill('Some comment');
    await page.getByRole('button', { name: 'Ja, jag vill tävla' }).click();
    await expect(page.getByText('Information om tävlingsavgifter')).toBeVisible()
    await expect(page.getByText('Information om funktionärskrav')).toBeVisible()

    await page.goto('/competitions')

    await expect(page.getByText('Du har anmält intresse att tävla').first()).toBeVisible()
})

test('A lifter without approval cant register for a competition', async ({ page }) => {
    await login(page, { role: 'member', explicit_registration_approval: false })

    const competition = await create(page, 'Competition', { date: '2030-01-01' })

    await page.goto('/competitions')

    await page.getByText(competition.name).click();
    await page.locator('div').filter({ hasText: /^Herrar$/ }).getByRole('radio').check();
    await page.getByRole('combobox').selectOption('74');
    await page.locator('.w-11').first().click();
    await page.getByPlaceholder('Ev. kommentar/ytterligare').fill('Some comment');
    await page.getByRole('button', { name: 'Ja, jag vill tävla' }).click();
    await expect(page.getByText('Du kan inte anmäla dig till tävlingar ')).toBeVisible()

    await page.goto('/competitions')

    await expect(page.getByText('Du har anmält intresse att tävla').first()).not.toBeVisible()
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