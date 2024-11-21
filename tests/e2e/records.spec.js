import { expect } from '@playwright/test'
import { test, create } from './helpers.ts'

test('Records are visible', async ({ page }) => {
    await create(page, 'Result', { 
        gender: 'F',
        competition_date: '2021-01-01',
        weight_class: '63',
        event: 'Knäböj',
        name: 'Some Good Lifter',
        result: '100'
    })

    await create(page, 'Result', { 
        gender: 'M',
        competition_date: '2021-01-01',
        weight_class: '83',
        event: 'Total',
        name: 'Other Good Lifter',
        result: '900'
    })

    await page.goto('/klubbrekord')

    await expect(page.locator('body')).toContainText('Kvinnor')
    await expect(page.locator('body')).toContainText('Män')

    await expect(page.getByRole('cell', { name: 'Some Good Lifter' })).toBeVisible()
    await expect(page.getByRole('cell', { name: '100' }).locator('div').first()).toBeVisible()

    await expect(page.getByRole('cell', { name: 'Other Good Lifter' })).toBeVisible()
    await expect(page.getByRole('cell', { name: '900' }).locator('div').first()).toBeVisible()
})

test('Only the best result is visible', async ({ page }) => {
    await create(page, 'Result', { 
        gender: 'M',
        competition_date: '2021-01-01',
        weight_class: '74',
        event: 'Bänkpress',
        name: 'Previous record holder',
        result: '150'
    })

    await create(page, 'Result', { 
        gender: 'M',
        competition_date: '2022-03-20',
        weight_class: '74',
        event: 'Bänkpress',
        name: 'New record holder',
        result: '200'
    })

    await page.goto('/klubbrekord')

    await expect(page.getByRole('cell', { name: 'Previous record holder' })).not.toBeVisible()
    await expect(page.getByRole('cell', { name: '150' }).locator('div').first()).not.toBeVisible()

    await expect(page.getByRole('cell', { name: 'New record holder' })).toBeVisible()
    await expect(page.getByRole('cell', { name: '200' }).locator('div').first()).toBeVisible()
})