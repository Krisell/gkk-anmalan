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

    await page.getByRole('row', { name: name }).locator('label div').click();

    await expect(page.getByText(`Medlemsavgiften för ${name} har markerats som betald`)).toBeVisible()

    await page.reload()
    await page.getByRole('row', { name: name }).locator('label div').click();
    await expect(page.getByText(`Medlemsavgiften för ${name} har markerats som obetald`)).toBeVisible()
})

test('The admin can inactivate a member', async ({ page }) => {
    await login(page, { role: 'admin' })
    const user = await create(page, 'User')

    await page.goto('/admin/accounts')
    
    await page.getByTestId(`inactivate-${user.id}`).click();

    await page.getByRole('button', { name: 'Inaktivera utan enkät' }).click();
    await page.getByTestId('inactive-accounts-table').getByText(`${user.first_name} ${user.last_name}`).click();
})

test('The admin can inactivate a member with survey email', async ({ page }) => {
    await login(page, { role: 'admin' })
    const user = await create(page, 'User')

    await page.goto('/admin/accounts')
    
    const requestPromise = page.waitForRequest(request => {
        return request.url().includes(`/admin/accounts/inactivate/${user.id}`) && request.method() === 'POST'
    });
    
    await page.getByTestId(`inactivate-${user.id}`).click();
    await page.getByRole('button', { name: 'Inaktivera och skicka enkät' }).click();
    
    const request = await requestPromise;
    const postData = JSON.parse(request.postData());
    expect(postData).toEqual({ sendSurveyEmail: true });
    
    await page.getByTestId('inactive-accounts-table').getByText(`${user.first_name} ${user.last_name}`).click();
})

test('The admin can inactivate a member without survey email', async ({ page }) => {
    await login(page, { role: 'admin' })
    const user = await create(page, 'User')

    await page.goto('/admin/accounts')
    
    const requestPromise = page.waitForRequest(request => 
        request.url().includes(`/admin/accounts/inactivate/${user.id}`) && 
        request.method() === 'POST'
    );
    
    await page.getByTestId(`inactivate-${user.id}`).click();
    await page.getByRole('button', { name: 'Inaktivera utan enkät' }).click();
    
    const request = await requestPromise;
    const postData = JSON.parse(request.postData());
    expect(postData).toEqual({ sendSurveyEmail: false });
    
    console.log(`${user.first_name} ${user.last_name}`)
    await page.pause()
    
    await page.getByTestId('inactive-accounts-table').getByText(`${user.first_name} ${user.last_name}`).click();
})
