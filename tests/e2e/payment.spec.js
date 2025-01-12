import { expect } from '@playwright/test'
import { test, login, create, update } from './helpers.ts'

test('Payments only show status before being synced to Fortnox', async ({ page }) => {
    const user = await login(page)

    await create(page, 'Payment', { 
        user_id: user.id, 
        type: 'MEMBERSHIP', 
        year: 2025,
        sek_amount: 123,
    })

    const payment = await create(page, 'Payment', { 
        user_id: user.id, 
        type: 'SSFLICENSE', 
        year: 2025,
        sek_amount: 456,
    })

    await page.goto('/profile')

    await expect(await page.getByText('Medlemsavgift 2025123 SEK INVÄNTAR FAKTURERING')).toBeVisible()
    await expect(await page.getByText('Tävlingslicens 2025456 SEK INVÄNTAR FAKTURERING')).toBeVisible()
})

test('Payments show status and invoice link after being synced to Fortnox', async ({ page }) => {
    const user = await login(page)

    await create(page, 'Payment', { 
        user_id: user.id, 
        type: 'MEMBERSHIP', 
        year: 2025,
        fortnox_invoice_document_number: 123,
        sek_amount: 123,
    })

    const payment = await create(page, 'Payment', { 
        user_id: user.id, 
        type: 'SSFLICENSE', 
        year: 2025,
        fortnox_invoice_document_number: 456,
        sek_amount: 456,
    })

    await page.goto('/profile')

    await expect(await page.getByText('Medlemsavgift 2025123 SEKINVÄNTAR BETALNINGKlicka för att öppna faktura')).toBeVisible()
    await expect(await page.getByText('Tävlingslicens 2025456 SEKINVÄNTAR BETALNINGKlicka för att öppna faktura')).toBeVisible()
    await expect(await page.getByText('Faktura har även skickats till din epost.').first()).toBeVisible()
    await expect(await page.getByText('Efter betalning kan det ta några bankdagar innan status uppdateras här.').first()).toBeVisible()
})
