import { expect } from '@playwright/test'
import { test, login, update } from './helpers.ts'

test('A user can register', async ({ page }) => {
    await page.goto('/insidan')
    await page.getByText('Skapa konto som medlem').click()

    const email = Math.random().toString(36).substring(7) + '@example.com'

    await page.waitForTimeout(1000)
    await page.getByPlaceholder('Förnamn').fill('A User')
    await page.getByPlaceholder('Efternamn').fill('Test Usersson')
    await page.getByPlaceholder('Epost').fill(email)
    await page.locator('input[name="password"]').fill('password')
    await page.getByPlaceholder('Bekräfta lösenord').fill('password')
    await page.getByRole('button', { name: 'Skapa konto' }).click();
    await expect(page.getByText('Välkommen till GKK!')).toBeVisible()
    await expect(page.getByText('Tävlingsanmälan')).toBeVisible()
})

test('A new user can accept agreements', async ({ page }) => {
    const user = await login(page)
    await update(page, 'User', { id: user.id }, { 
        membership_agreement_signed_at: '2020-01-01',
        anti_doping_agreement_signed_at: '2020-01-01',
    })

    await page.goto('/insidan')

    await page.getByRole('button', { name: 'Godkänn medlemsavtalet' }).click();
    await page.getByRole('button', { name: 'Ja, godkänn avtalet' }).click();

    await expect(page.getByText('signerat')).toBeVisible()

    await page.getByRole('button', { name: 'Godkänn antidopingavtalet' }).click();
    await page.getByRole('button', { name: 'Ja, godkänn avtalet' }).click();

    await expect(page.getByText('signerat')).toBeHidden()
})
