import { expect } from '@playwright/test'
import { test, login, create } from './helpers.ts'

test('Payments only show status before being synced to Fortnox', async ({ page }) => {
    const user = await login(page)

    await create(page, 'Payment', { 
        user_id: user.id, 
        type: 'MEMBERSHIP', 
        year: 2025,
        sek_amount: 123,
    })

    await create(page, 'Payment', { 
        user_id: user.id, 
        type: 'SSFLICENSE', 
        year: 2025,
        sek_amount: 456,
    })

    await page.goto('/profile')

    await expect(await page.getByText('Medlemsavgift 2025')).toBeVisible()
    await expect(await page.getByText('123 SEK')).toBeVisible()
    await expect(await page.getByText('Tävlingslicens 2025')).toBeVisible()
    await expect(await page.getByText('456 SEK')).toBeVisible()
    await expect(await page.getByText(/INVÄNTAR\s*FAKTURERING/).first()).toBeVisible()
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

    await create(page, 'Payment', { 
        user_id: user.id, 
        type: 'SSFLICENSE', 
        year: 2025,
        fortnox_invoice_document_number: 456,
        sek_amount: 456,
    })

    await page.goto('/profile')

    await expect(await page.getByText('Medlemsavgift 2025')).toBeVisible()
    await expect(await page.getByText('123 SEK')).toBeVisible()
    await expect(await page.getByText('Tävlingslicens 2025')).toBeVisible()
    await expect(await page.getByText('456 SEK')).toBeVisible()
    await expect(await page.getByText(/INVÄNTAR\s*BETALNING/).first()).toBeVisible()
    await expect(await page.getByText('Visa faktura').first()).toBeVisible()
    await expect(await page.getByText('Faktura har även skickats till din epost.').first()).toBeVisible()
    await expect(await page.getByText('Efter betalning kan det ta några bankdagar innan status uppdateras här.').first()).toBeVisible()
})
