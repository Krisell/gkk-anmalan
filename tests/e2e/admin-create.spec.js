import { expect } from '@playwright/test'
import { test, login, create } from './helpers.ts'

test('An admin can create a competition', async ({ page }) => {
    await login(page, { role: 'admin' })

    await page.goto('/competitions')
    await page.getByRole('button', { name: 'Klicka för att administrera' }).click();
    await page.getByRole('button', { name: 'Ny tävling' }).click();

    await page.locator('input[name="name"]').fill('Some Competition');
    await page.locator('input[name="date"]').first().fill('2030-11-21');
    await page.getByPlaceholder('Plats, ex Friskis Majorna').fill('Friskis Majorna');
    await page.getByPlaceholder('Ev. ytterligare info').fill('Some additional info');
    await page.getByRole('button', { name: 'Skapa tävling' }).click();
    await expect(page.getByText('Admin - Tävlingsanmälan')).toBeVisible()
    await expect(page.getByText('Some Competition').first()).toBeVisible()

    await page.getByRole('link', { name: ' Tävlingsanmälan' }).click();
    await expect(page.getByText('Some Competition')).toBeVisible()
    await expect(page.getByText('Some additional info')).toBeVisible()
})

test('An admin can create an event', async ({ page }) => {
    await login(page, { role: 'admin' })

    await page.goto('/events')
    await page.getByRole('button', { name: 'Klicka för att administrera' }).click();
    await page.getByRole('button', { name: 'Nytt event' }).click();

    await page.locator('input[name="name"]').fill('Some Event');
    await page.locator('input[name="date"]').first().fill('2030-11-21');
    await page.getByPlaceholder('Plats, ex Friskis Majorna').fill('Friskis Majorna');
    await page.getByPlaceholder('Ev. ytterligare info').fill('Some additional info');

    await page.locator('.w-11').first().click();
    await page.getByRole('button', { name: 'Skapa event' }).click();
    
    await expect(page.getByText('Admin - Funktionärsanmälningar')).toBeVisible()
    await expect(page.getByText('Some Event').first()).toBeVisible()
    
    await page.getByRole('link', { name: ' Funktionärsanmälan' }).click();
    await expect(page.getByText('Some Event')).toBeVisible()
    await expect(page.getByText('Some additional info')).toBeVisible()
})