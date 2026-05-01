import { expect } from '@playwright/test'
import { test, login } from './helpers.ts'

test('Fortnox sync runs through all records by calling the endpoint repeatedly', async ({ page }) => {
    await login(page, { role: 'admin' })

    await page.route('**/admin/payment-tools/sync-customers/preview', (route) => {
        route.fulfill({ json: { count: 2, message: '2 aktiva användare saknar Fortnox-kundnummer.' } })
    })

    const executeResponses = [
        { processed: true, status: 'ok', message: 'Anna Andersson', remaining: 1 },
        { processed: true, status: 'ok', message: 'Bertil Bengtsson', remaining: 0 },
    ]
    let executeCallCount = 0
    await page.route('**/admin/payment-tools/sync-customers/execute', (route) => {
        const i = executeCallCount++
        const payload = i < executeResponses.length ? executeResponses[i] : { processed: false, remaining: 0 }
        route.fulfill({ json: payload })
    })

    await page.goto('/admin/payment-tools')

    const card = page.locator('.bg-white').filter({ hasText: 'Synka Fortnox-kunder' })
    await card.getByText('Synka Fortnox-kunder').click()
    await card.getByRole('button', { name: 'Förhandsgranska' }).click()

    await expect(card.getByText('2 aktiva användare saknar Fortnox-kundnummer.')).toBeVisible()

    await card.getByRole('button', { name: 'Kör' }).click()

    await expect(card.getByText('Klart! 2 poster behandlade.')).toBeVisible()
    expect(executeCallCount).toBe(2)
})

test('Fortnox verify payments accumulates paid count across calls', async ({ page }) => {
    await login(page, { role: 'admin' })

    await page.route('**/admin/payment-tools/verify-payments/preview', (route) => {
        route.fulfill({ json: { count: 3, message: '3 fakturor är inte markerade som betalda.' } })
    })

    const executeResponses = [
        { processed: true, status: 'paid', paid: true, message: 'Anna - Faktura 1: Betald', remaining: 2 },
        { processed: true, status: 'unpaid', paid: false, message: 'Bertil - Faktura 2: Ej betald', remaining: 1 },
        { processed: true, status: 'paid', paid: true, message: 'Cecilia - Faktura 3: Betald', remaining: 0 },
    ]
    let executeCallCount = 0
    await page.route('**/admin/payment-tools/verify-payments/execute', (route) => {
        const i = executeCallCount++
        route.fulfill({ json: executeResponses[i] })
    })

    await page.goto('/admin/payment-tools')

    const card = page.locator('.bg-white').filter({ hasText: 'Verifiera betalningar' })
    await card.getByText('Verifiera betalningar').click()
    await card.getByRole('button', { name: 'Förhandsgranska' }).click()

    await expect(card.getByText('3 fakturor är inte markerade som betalda.')).toBeVisible()

    await card.getByRole('button', { name: 'Kör' }).click()

    await expect(card.getByText('Klart! 3 poster behandlade. 2 markerade som betalda.')).toBeVisible()
    expect(executeCallCount).toBe(3)
})
