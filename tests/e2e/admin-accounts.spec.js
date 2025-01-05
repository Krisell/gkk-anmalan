import { expect } from '@playwright/test'
import { test, login, create } from './helpers.ts'

test('The list of users can be seen', async ({ page }) => {
    const user = await login(page, { role: 'admin' })

    const otherUser = await create(page, 'User', { licence_number: 'SOME-LICENSE-NUMBER-123' })

    await page.goto('/insidan')
    await page.locator('div').filter({ hasText: /^Administrera konton$/ }).first().click()
  
    await expect(page.getByText(`${user.first_name} ${user.last_name}`)).toBeVisible()
    await expect(page.getByText(`${otherUser.first_name} ${otherUser.last_name}`)).toBeVisible()
    await expect(page.getByText('SOME-LICENSE-NUMBER-123')).toBeVisible()
})

test('The admin can mark and unmark payments', async ({ page }) => {
    const user = await login(page, { role: 'admin' })

    const otherUser = await create(page, 'User', { licence_number: '1234567890' })
    await create(page, 'Payment', { 
        user_id: otherUser.id, 
        type: 'MEMBERSHIP', 
        year: new Date().getFullYear() 
    })

    await page.goto('/admin/accounts')

    const name = `${otherUser.first_name} ${otherUser.last_name}`

    await page.locator('td:nth-child(9) > .inline-flex > .w-11').click();

    await expect(page.getByText(`Medlemsavgiften för ${name} har markerats som betald`)).toBeVisible()

    await page.reload()
    await page.locator('td:nth-child(9) > .inline-flex > .w-11').click();
    await expect(page.getByText(`Medlemsavgiften för ${name} har markerats som obetald`)).toBeVisible()
})

test('The admin can inactivated a member', async ({ page }) => {
    await login(page, { role: 'admin' })
    const user = await create(page, 'User')

    await page.goto('/admin/accounts')

    await page.getByRole('row', { name: `${user.first_name} ${user.last_name}` }).locator('i').nth(2).click();
    await page.getByRole('button', { name: 'Ja, inaktivera' }).click();
    await page.getByRole('heading', { name: '1 inaktiverade konton' }).click();
})
