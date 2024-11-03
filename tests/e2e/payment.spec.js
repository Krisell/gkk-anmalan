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

    await expect(page.getByText('OBETALD123 SEK')).toBeVisible()
    await expect(page.getByText('OBETALD456 SEK')).toBeVisible()

    await page.locator('li').filter({ hasText: 'OBETALD123 SEK' }).getByRole('button').click()
    await page.getByRole('button', { name: 'Ja, betalningen är genomförd' }).click()
    await expect(page.getByText('INVÄNTAR VERIFIERING123 SEK')).toBeVisible()

    await update(page, 'Payment', { id: payment.id }, { state: 'PAID' })

    await page.reload()
    await expect(page.getByText('BETALD456 SEK')).toBeVisible()
})
