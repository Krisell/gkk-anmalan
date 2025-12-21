import { expect } from '@playwright/test'
import { test, login, create } from './helpers.ts'

/**
 * E2E tests for the helper requirement feature that prevents members who haven't
 * helped as function officials from registering for competitions unless they have
 * explicit admin approval.
 */

test('User without explicit approval cannot register for competition', async ({ page }) => {
    // Create a user without explicit approval (and no helper activity)
    const user = await login(page, {
        role: 'member',
        explicit_registration_approval: false
    })

    const competition = await create(page, 'Competition', {
        date: '2030-01-01',
        last_registration_at: '2029-12-01'
    })

    await page.goto('/competitions')
    await page.getByText(competition.name).click()

    // Fill out competition registration form
    await page.locator('div').filter({ hasText: /^Herrar$/ }).getByRole('radio').check()
    await page.getByRole('combobox').selectOption('74')
    await page.locator('.w-11').first().click()

    // Try to register - this should be blocked
    await page.getByRole('button', { name: 'Ja, jag vill tävla' }).click()

    // Should NOT show success (Sparat! button), indicating registration was blocked
    await expect(page.getByRole('button', { name: 'Sparat!' })).not.toBeVisible()
})

test('User with explicit admin approval can register despite no helper activity', async ({ page }) => {
    // Create a user WITH explicit approval (despite no helper activity)
    const user = await login(page, {
        role: 'member',
        explicit_registration_approval: true
    })

    const competition = await create(page, 'Competition', {
        date: '2030-01-01',
        last_registration_at: '2029-12-01'
    })

    await page.goto('/competitions')
    await page.getByText(competition.name).click()

    // Fill out competition registration form
    await page.locator('div').filter({ hasText: /^Herrar$/ }).getByRole('radio').check()
    await page.getByRole('combobox').selectOption('74')
    await page.locator('.w-11').first().click()

    // Try to register - this should work due to explicit approval
    await page.getByRole('button', { name: 'Ja, jag vill tävla' }).click()

    // Should show success indicators (competition registration information)
    await expect(page.getByText('Information om tävlingsavgifter')).toBeVisible()
    await expect(page.getByText('Information om funktionärskrav')).toBeVisible()
})

test('Declining competition always works regardless of approval status', async ({ page }) => {
    // Create a user without explicit approval
    const user = await login(page, {
        role: 'member',
        explicit_registration_approval: false
    })

    const competition = await create(page, 'Competition', {
        date: '2030-01-01',
        last_registration_at: '2029-12-01'
    })

    await page.goto('/competitions')
    await page.getByText(competition.name).click()

    // Fill out form but decline to compete
    await page.locator('div').filter({ hasText: /^Herrar$/ }).getByRole('radio').check()
    await page.getByRole('combobox').selectOption('74')
    await page.locator('.w-11').first().click()

    // Decline competition - this should always work
    await page.getByRole('button', { name: 'Jag vill inte tävla' }).click()

    // Should show success (Sparat! button), indicating decline was accepted
    await expect(page.getByRole('button', { name: 'Sparat!' })).toBeVisible()
})