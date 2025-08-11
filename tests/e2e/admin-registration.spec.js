import { expect } from '@playwright/test'
import { test, login, create, first } from './helpers.ts'

test('A competition registration can be seen and edited', async ({ page }) => {
    await login(page, { role: 'admin' })

    const competition = await create(page, 'Competition', { date: '2030-01-01' })
    const registration = await create(page, 'CompetitionRegistration', { competition_id: competition.id })
    const user = await first(page, 'User', { id: registration.user_id })

    await page.goto('/admin/competitions')

    await page.getByText(competition.name).click();
    await expect(page.getByText(`${user.first_name} ${user.last_name}`)).toBeVisible()

    await page.getByRole('cell', { name: '' }).locator('i').click();
    await page.getByPlaceholder('Ev. ytterligare info').fill('Some admin comment');
    await page.getByRole('button', { name: 'Uppdatera' }).click();

    const updatedRegistration = await first(page, 'CompetitionRegistration', { id: registration.id })
    expect(updatedRegistration.comment).toBe('Some admin comment') 
})

test('A superadmin can edit events for a registration', async ({ page }) => {
    await login(page, { role: 'admin' })

    const competition = await create(page, 'Competition', { date: '2030-01-01' })
    const registration = await create(page, 'CompetitionRegistration', { competition_id: competition.id })
    const user = await first(page, 'User', { id: registration.user_id })

    await page.goto('/admin/competitions')

    await page.getByText(competition.name).click();

    await expect(page.getByText(`${user.first_name} ${user.last_name}`)).toBeVisible()
    await expect(page.getByTestId('competition-table').getByText('KSL')).toBeVisible()
    await expect(page.getByTestId('competition-table').getByText('KBP')).toBeVisible()

    await page.getByRole('cell', { name: '' }).locator('i').click();
    await page.locator('div:nth-child(2) > .inline-flex > .w-11').click();
    await page.getByRole('button', { name: 'Uppdatera' }).click();
    
    await expect(page.getByTestId('competition-table').getByText('KSL')).toBeVisible()
    await expect(page.getByTestId('competition-table').getByText('KBP')).toBeHidden()
})

test('An event registration can be seen and edited', async ({ page }) => {
    await login(page, { role: 'admin' })

    const event = await create(page, '\\App\\Models\\Event', { date: '2030-01-01' })
    const registration = await create(page, 'EventRegistration', { event_id: event.id })
    const user = await first(page, 'User', { id: registration.user_id })

    expect(registration.presence_confirmed).toBeFalsy()

    await page.goto('/admin/events')

    await page.getByText(event.name).click();
    await expect(page.getByText(`${user.first_name} ${user.last_name}`)).toBeVisible()
    await page.getByRole('row', { name: ` ${user.first_name} ${user.last_name}` }).locator('label div').click();
    
    const updatedRegistration = await first(page, 'EventRegistration', { id: registration.id })
    expect(updatedRegistration.presence_confirmed).toBe(true)
})