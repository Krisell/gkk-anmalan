import { expect } from '@playwright/test'
import { test, login, create, update } from './helpers.ts'

test('Payments can be handled on the profile page', async ({ page }) => {
    const user = await login(page, { role: 'admin' })

    await create(page, 'Payment', { 
        user_id: user.id, 
        type: 'MEMBERSHIP', 
        year: new Date().getFullYear() + 1,
        sek_amount: 123,
    })

    const payment = await create(page, 'Payment', { 
        user_id: user.id, 
        type: 'SSFLICENSE', 
        year: new Date().getFullYear() + 2,
        sek_amount: 456,
    })

    await page.goto('/profile')

    await expect(await page.getByText('Medlemsavgift 2025123 SEK OBETALD')).toBeVisible()
    await expect(await page.getByText('Tävlingslicens 2026456 SEK OBETALD')).toBeVisible()

    await page.locator('li').filter({ hasText: 'Medlemsavgift 2025123 SEK' }).getByRole('button').click();
    await page.getByRole('button', { name: 'Ja, betalningen är genomförd' }).click()
    await expect(await page.getByText('Medlemsavgift 2025123 SEK VERIFIERAS')).toBeVisible()

    await update(page, 'Payment', { id: payment.id }, { state: 'PAID' })

    await page.reload()
    await expect(await page.getByText('Tävlingslicens 2026456 SEK BETALD')).toBeVisible()
})
