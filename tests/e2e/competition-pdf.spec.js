import { expect } from '@playwright/test'
import { test, login, create, update } from './helpers.ts'

test('A competition with a PDF shows a link on the registration page', async ({ page }) => {
    await login(page, { role: 'admin' })

    const competition = await create(page, 'Competition', {
        date: '2030-06-15',
        name: 'PDF Test Competition',
        events: JSON.stringify({ ksl: true, kbp: true, sl: false, bp: false }),
        show_status: 'default',
        pdf_url: 'https://example.com/test.pdf',
    })

    await page.goto(`/competitions/${competition.id}`)
    await expect(page.getByText('PDF Test Competition')).toBeVisible()
    await expect(page.getByRole('link', { name: 'Visa tävlingsinformation (PDF)' })).toBeVisible()
    await expect(page.getByRole('link', { name: 'Visa tävlingsinformation (PDF)' })).toHaveAttribute('href', 'https://example.com/test.pdf')
    await expect(page.getByRole('link', { name: 'Visa tävlingsinformation (PDF)' })).toHaveAttribute('target', '_blank')
})

test('A competition without a PDF does not show a PDF link', async ({ page }) => {
    await login(page, { role: 'admin' })

    const competition = await create(page, 'Competition', {
        date: '2030-06-15',
        name: 'No PDF Competition',
        events: JSON.stringify({ ksl: true, kbp: true, sl: false, bp: false }),
        show_status: 'default',
    })

    await page.goto(`/competitions/${competition.id}`)
    await expect(page.getByText('No PDF Competition')).toBeVisible()
    await expect(page.getByRole('link', { name: 'Visa tävlingsinformation (PDF)' })).not.toBeVisible()
})

test('An admin can edit a competition and see the existing PDF', async ({ page }) => {
    await login(page, { role: 'admin' })

    await create(page, 'Competition', {
        date: '2030-06-15',
        name: 'Editable Competition',
        events: JSON.stringify({ ksl: true, kbp: true, sl: false, bp: false }),
        show_status: 'default',
        pdf_url: 'https://example.com/existing.pdf',
    })

    await page.goto('/admin/competitions')
    await expect(page.getByText('Editable Competition').first()).toBeVisible()

    await page.locator('tr', { hasText: 'Editable Competition' }).locator('svg').first().click();
    await expect(page.getByText('PDF bifogad')).toBeVisible()
    await expect(page.getByRole('link', { name: 'Visa PDF' })).toHaveAttribute('href', 'https://example.com/existing.pdf')
})
