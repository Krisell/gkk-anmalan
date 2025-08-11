import { expect } from '@playwright/test'
import { test, login, create } from './helpers.ts'

test('An admin can add a lifter to a competition', async ({ page }) => {
    await login(page, { role: 'admin' })

    const competition = await create(page, 'Competition')
    const user = await create(page, 'User')

    await page.goto('/competitions')
    await page.getByRole('button', { name: 'Klicka för att administrera' }).click()
    await page.getByText(competition.name).click()

    
    await page.getByRole('button', { name: 'Lägg till tävlande' }).click()
    await page.getByRole('combobox', { name: 'Välj tävlande' }).selectOption({ label: `${user.first_name} ${user.last_name}` })
    await page.locator('.w-11').first().click();
    await page.getByRole('button', { name: 'Lägg till' }).click();

    await expect(page.getByText(`${user.first_name} ${user.last_name}`)).toBeVisible()
    await expect(page.getByTestId('competition-table').getByText('KSL')).toBeVisible() // Verifies the event is displayed
    await page.getByText('Tillagd av admin').click();
})
