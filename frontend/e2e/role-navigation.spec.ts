import { expect, test, type Page } from '@playwright/test';

const password = process.env.E2E_PASSWORD || 'Password123!';

const accounts = {
    student: process.env.E2E_STUDENT_EMAIL || 'e2e.student@example.com',
    hr: process.env.E2E_HR_EMAIL || 'e2e.hr@example.com',
    admin: process.env.E2E_ADMIN_EMAIL || 'e2e.admin@example.com',
    mentor: process.env.E2E_MENTOR_EMAIL || 'e2e.mentor@example.com',
    unverified: process.env.E2E_UNVERIFIED_EMAIL || 'e2e.unverified@example.com',
};

async function login(page: Page, email: string, expectedPath: RegExp) {
    await page.context().clearCookies();
    await page.goto('/login', { waitUntil: 'networkidle' });
    await expect(page.locator('#email')).toBeVisible();
    await page.locator('#email').fill(email);
    await page.locator('input[type="password"]').fill(password);
    await expect(page.locator('#email')).toHaveValue(email);
    await expect(page.locator('input[type="password"]')).toHaveValue(password);
    await page.locator('form').getByRole('button', { name: /masuk|login|sign in/i }).click();

    await page.waitForURL(expectedPath);
    await expect(page).not.toHaveURL(/\/login/);
}

async function logout(page: Page) {
    await page.goto('/');
    await page.context().clearCookies();
}

test.describe('role based browser navigation', () => {
    test('HR can open dashboard, applicant list, and applicant detail', async ({ page }) => {
        await login(page, accounts.hr, /\/hr\/dashboard$/);
        await expect(page.getByRole('heading', { name: /E2E Talent Lab|Dashboard HR/i })).toBeVisible();

        await page.goto('/hr/applications');
        await expect(page).toHaveURL(/\/hr\/applications$/);
        await expect(page.locator('tbody tr').filter({ hasText: 'E2E Student' }).first()).toBeVisible();
        await expect(page.locator('tbody tr').filter({ hasText: 'E2E Product Design Intern' }).first()).toBeVisible();

        await page.locator('tbody tr').filter({ hasText: 'E2E Growth Marketing Intern' }).first().click();
        await expect(page).toHaveURL(/\/hr\/applications\/\d+$/);
        await expect(page.getByText('Saya tertarik mengikuti program E2E Growth Marketing Intern.')).toBeVisible();
    });

    test('admin can open admin dashboard directly after login', async ({ page }) => {
        await login(page, accounts.admin, /\/admin\/dashboard$/);

        await expect(page.getByRole('heading', { name: /audit system/i })).toBeVisible();
        await expect(page.getByRole('link', { name: /view all logs/i })).toBeVisible();

        await page.reload({ waitUntil: 'networkidle' });
        await expect(page).toHaveURL(/\/admin\/dashboard$/);
        await expect(page).not.toHaveURL(/\/login/);
    });

    test('mentor can open mentee list and mentee detail', async ({ page }) => {
        await login(page, accounts.mentor, /\/mentor\/dashboard$/);
        await expect(page.getByText(/mentor|mentee/i).first()).toBeVisible();

        await page.goto('/mentor/mentees');
        await expect(page).toHaveURL(/\/mentor\/mentees$/);
        await expect(page.locator('tbody tr').filter({ hasText: 'E2E Student' }).first()).toBeVisible();
        await expect(page.locator('tbody tr').filter({ hasText: 'E2E Product Design Intern' }).first()).toBeVisible();

        await page.getByRole('link', { name: /detail/i }).first().click();
        await expect(page).toHaveURL(/\/mentor\/mentees\/\d+$/);
        await expect(page.getByRole('heading', { name: /E2E Student/i })).toBeVisible();
        await expect(page.getByText('E2E Product Design Intern').first()).toBeVisible();
    });
});

test.describe('student application and auth recovery browser flows', () => {
    test('student can apply from internship detail and open application detail', async ({ page }) => {
        await login(page, accounts.student, /\/dashboard$/);

        await page.goto('/my-applications');
        const existingApplication = page.getByText('E2E Growth Marketing Intern').first();

        if (!(await existingApplication.isVisible().catch(() => false))) {
            await page.goto('/internships/e2e-growth-marketing-intern');
            await expect(page.getByRole('heading', { name: /E2E Growth Marketing Intern/i })).toBeVisible();

            await page.getByRole('button', { name: /lamar sekarang|apply now/i }).click();
            await expect(page.getByText(/kirim lamaran magang/i)).toBeVisible();
            await page.locator('textarea').fill('Saya tertarik mengikuti program E2E Growth Marketing Intern.');
            await page.getByRole('button', { name: /kirim lamaran|submit application/i }).click();

            await page.waitForURL(/\/my-applications$/, { timeout: 5000 }).catch(() => null);
            if (!/\/my-applications$/.test(page.url()) && await page.getByText(/sudah melamar posisi ini/i).isVisible().catch(() => false)) {
                await page.goto('/my-applications');
            }
        }

        await expect(page).toHaveURL(/\/my-applications$/);
        await page.reload({ waitUntil: 'networkidle' });
        await expect(page).toHaveURL(/\/my-applications$/);
        await expect(page.getByText('E2E Growth Marketing Intern').first()).toBeVisible();
        await page
            .locator('.bg-white', { hasText: 'E2E Growth Marketing Intern' })
            .getByRole('link', { name: /lihat detail|view detail/i })
            .click();
        await expect(page).toHaveURL(/\/my-applications\/\d+$/);
        await expect(page.getByText('Status Lamaran Anda')).toBeVisible();
        await expect(page.getByRole('heading', { name: /E2E Growth Marketing Intern/i })).toBeVisible();
    });

    test('forgot password and verify email pages render through the browser', async ({ page }) => {
        await page.goto('/forgot-password');
        await expect(page.getByRole('heading', { name: /lupa password|forgot password/i })).toBeVisible();
        await page.locator('#email').fill(accounts.student);
        await page.getByRole('button', { name: /kirim kode otp|send otp code/i }).click();
        await expect(page.getByText(/otp|email/i).first()).toBeVisible();

        await logout(page);
        await login(page, accounts.unverified, /\/verify-email$/);
        await expect(page.getByRole('heading', { name: /verifikasi email|verify your email/i })).toBeVisible();
        await expect(page.getByRole('button', { name: /verifikasi akun|verify account/i })).toBeVisible();
    });
});
