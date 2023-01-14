const { test, expect } = require('@playwright/test')

test('A user can register', async ({ page }) => {
    await page.goto('/insidan')

    await page.getByText('Skapa konto som medlem').click()
    await page.getByPlaceholder('Förnamn').fill('Test User')
    await page.getByPlaceholder('Efternamn').fill('Test Usersson')
    await page.getByPlaceholder('Epost').fill('test@example.com')
    await page.locator('input[name="password"]').fill('password')
    await page.getByPlaceholder('Bekräfta lösenord').fill('password')
    await page.getByRole('button', { name: 'Skapa konto' }).click();
    await page.getByText('Godkänn medlemsavtalet').click()
    await page.getByText('Godkänn antidopingavtalet').click()
    await page.getByText('Innan du kan börja använda systemet behöver ditt konto godkännas av administratören.')

    await page.getByText('Logga ut').click()
    await page.getByText('Logga in som medlem').click()

    await page.getByPlaceholder('Epost').fill('test@example.com')
    await page.getByPlaceholder('Lösenord').fill('password')
    await page.getByRole('button', { name: 'Logga in' }).click()

    await expect(page.getByText('Välkommen till GKK!')).toBeVisible()
    await page.getByText('Profil').click()
    await expect(page.getByRole('main').getByText('Inloggad som test@example.com')).toBeVisible()
})
