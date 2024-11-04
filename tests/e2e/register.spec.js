import { expect } from '@playwright/test'
import { test, login, update, first } from './helpers.ts'

test('A user can register', async ({ page }) => {
    await page.goto('/register')

    const email = Math.random().toString(36).substring(7) + '@example.com'

    await expect(await page.getByPlaceholder('Förnamn')).toBeVisible()
    await page.getByPlaceholder('Förnamn').fill('A User')
    await page.getByPlaceholder('Efternamn').fill('Test Usersson')
    await page.getByPlaceholder('Födelseår').fill('1990')
    await page.getByPlaceholder('Epost').fill(email)
    await page.locator('input[name="password"]').fill('password')
    await page.getByPlaceholder('Bekräfta lösenord').fill('password')
    await page.getByRole('button', { name: 'Skapa konto' }).click()
    await expect(page.getByText('Välkommen till GKK!')).toBeVisible()
    await expect(page.getByText('Tävlingsanmälan')).toBeVisible()

    const user = await first(page, 'User', { email })

    expect(user.first_name).toBe('A User')
    expect(user.last_name).toBe('Test Usersson')
    expect(user.email).toBe(email)
    expect(user.birth_year).toBe(1990)
})

test('Missing data triggers validation errors and user error messages', async ({ page }) => {
    await page.goto('/register')

    const email = Math.random().toString(36).substring(7) + '@example.com'

    await page.getByPlaceholder('Förnamn').fill('A User')

    await page.getByRole('button', { name: 'Skapa konto' }).click()
    await expect(page.getByText('Välkommen till GKK!')).toBeHidden()

    await page.getByPlaceholder('Efternamn').fill('Test Usersson')
    await page.getByPlaceholder('Födelseår').fill('1990')
    await page.getByPlaceholder('Epost').fill(email)
    await page.locator('input[name="password"]').fill('password')

    await page.getByRole('button', { name: 'Skapa konto' }).click()
    await expect(page.getByText('Välkommen till GKK!')).toBeHidden()

    await page.getByPlaceholder('Bekräfta lösenord').fill('password')
    await page.getByRole('button', { name: 'Skapa konto' }).click()
    await expect(page.getByText('Välkommen till GKK!')).toBeVisible()
})

test('A new user can accept agreements', async ({ page }) => {
    const user = await login(page)
    await update(page, 'User', { id: user.id }, { 
        membership_agreement_signed_at: null,
        anti_doping_agreement_signed_at: null,
    })

    await page.goto('/insidan')

    await page.getByRole('button', { name: 'Godkänn medlemsavtalet' }).click();
    await page.getByRole('button', { name: 'Ja, godkänn avtalet' }).click();

    await expect(page.getByText('signerat')).toBeVisible()

    await page.getByRole('button', { name: 'Godkänn antidopingavtalet' }).click();
    await page.getByRole('button', { name: 'Ja, godkänn avtalet' }).click();

    await expect(page.getByText('signerat')).toBeHidden()
})

test('An existing user need to accept agreements signed more than one year ago', async ({ page }) => {
    const user = await login(page)
    await update(page, 'User', { id: user.id }, { 
        membership_agreement_signed_at: '2020-03-05',
        anti_doping_agreement_signed_at: '2020-03-05',
    })

    await page.goto('/insidan')

    await page.getByRole('button', { name: 'Godkänn medlemsavtalet' }).click();
    await page.getByRole('button', { name: 'Ja, godkänn avtalet' }).click();

    await expect(page.getByText('signerat')).toBeVisible()

    await page.getByRole('button', { name: 'Godkänn antidopingavtalet' }).click();
    await page.getByRole('button', { name: 'Ja, godkänn avtalet' }).click();

    await expect(page.getByText('signerat')).toBeHidden()
})
