// Run against an isolated *_test database with synthetic accounts only.
// PLAYWRIGHT_CORE_PATH points to a temporary installation; no app dependency is needed.
import assert from 'node:assert/strict';
import { mkdir, writeFile } from 'node:fs/promises';
import { pathToFileURL } from 'node:url';

const { chromium } = await import(pathToFileURL(`${process.env.PLAYWRIGHT_CORE_PATH}/index.mjs`).href);
const base = process.env.BROWSER_BASE_URL;
const email = process.env.BROWSER_LEARNER_EMAIL;
const password = process.env.BROWSER_LEARNER_PASSWORD;
const output = process.env.BROWSER_OUTPUT_DIR;
assert(base && email && password && output, 'Provide browser URL, synthetic learner credentials and output directory.');
assert(['localhost', '127.0.0.1'].includes(new URL(base).hostname), 'Use a local isolated test server.');
await mkdir(output, { recursive: true });
const browser = await chromium.launch({ executablePath: process.env.CHROME_PATH || '/usr/bin/google-chrome', headless: true, args: ['--no-sandbox', '--disable-dev-shm-usage'] });
const records = [];
const errors = [];
let currentPage;
try {
    for (const width of [390, 768, 1024, 1440]) {
        const context = await browser.newContext({ viewport: { width, height: 1000 }, reducedMotion: 'reduce', serviceWorkers: 'block' });
        const page = await context.newPage();
        currentPage = page;
        page.setDefaultTimeout(15000);
        page.on('pageerror', error => errors.push({ width, message: error.message }));
        page.on('response', response => { if (response.url().startsWith(base) && response.status() >= 500) errors.push({ width, status: response.status(), url: response.url() }); });
        // External fonts, analytics and MathJax are outside this journey's acceptance scope.
        await page.route('**/*', route => new URL(route.request().url()).origin === new URL(base).origin
            ? route.continue()
            : route.fulfill({ status: 200, contentType: route.request().resourceType() === 'stylesheet' ? 'text/css' : 'application/javascript', body: '' }));
        async function snapshot(state) {
            assert.equal(await page.locator('h1:visible').count(), 1, `${width}/${state}: one page heading`);
            assert(await page.locator('h1').evaluate(element => getComputedStyle(element).fontFamily.includes('Sora')), 'Built design-system assets must be applied.');
            const bounds = await page.evaluate(() => ({ viewport: document.documentElement.clientWidth, content: document.documentElement.scrollWidth }));
            await page.evaluate(() => window.scrollTo(0, 0));
            await page.screenshot({ path: `${output}/${width}-${state}.png`, fullPage: true });
            if (bounds.content > bounds.viewport + 1) {
                const overflow = await page.evaluate(() => [...document.querySelectorAll('main *')].filter(element => element.getBoundingClientRect().right > innerWidth + 1).map(element => ({ tag: element.tagName, class: element.className, width: element.getBoundingClientRect().width, right: element.getBoundingClientRect().right })).slice(0, 20));
                throw new Error(`${width}/${state}: horizontal overflow ${JSON.stringify({ bounds, overflow })}`);
            }
            records.push({ width, state, ...bounds });
        }
        await page.goto(`${base}/login`, { waitUntil: 'domcontentloaded' });
        await page.locator('#email').fill(email);
        await page.locator('#password').fill(password);
        await page.getByRole('button', { name: 'Sign in', exact: false }).click();
        await page.waitForURL(/\/student\/dashboard/);
        await page.goto(`${base}/learning-profile`, { waitUntil: 'domcontentloaded' });
        await page.getByRole('heading', { name: 'What would you like to learn?' }).waitFor();
        await page.locator('#category-search').fill('Business');
        await page.getByRole('checkbox', { name: 'Communication and Languages', exact: true }).waitFor({ state: 'hidden' });
        await page.getByRole('checkbox', { name: 'Business and Entrepreneurship', exact: true }).check();
        await page.locator('#category-search').fill('Languages');
        await page.getByRole('checkbox', { name: 'Business and Entrepreneurship', exact: true }).waitFor({ state: 'hidden' });
        await page.getByRole('checkbox', { name: 'Communication and Languages', exact: true }).check();
        await page.locator('#custom-topics').fill('Accounting\nPublic speaking');
        await snapshot('interests');
        await page.getByRole('button', { name: 'Continue', exact: true }).click();
        await page.getByRole('heading', { name: 'Where are you starting from?' }).waitFor();
        await page.waitForFunction(() => document.activeElement?.textContent.includes('Where are you starting from?'));
        await page.locator('#starting-level').selectOption('beginner');
        await page.locator('#skills').fill('Spreadsheets');
        await page.locator('#completed-courses').fill('Introductory accounting');
        await page.locator('#experience').fill('Helped prepare a budget');
        await page.locator('#practical-experience').fill('Organised a fictional project');
        await snapshot('starting-point');
        await page.getByRole('button', { name: 'Back', exact: true }).click();
        await page.getByRole('heading', { name: 'What would you like to learn?' }).waitFor();
        await page.locator('#category-search').fill('Business');
        await page.getByRole('checkbox', { name: 'Business and Entrepreneurship', exact: true }).waitFor();
        assert(await page.getByRole('checkbox', { name: 'Business and Entrepreneurship', exact: true }).isChecked());
        await page.getByRole('button', { name: 'Continue', exact: true }).click();
        await page.locator('#skills').waitFor();
        assert.equal(await page.locator('#skills').inputValue(), 'Spreadsheets');
        await page.getByRole('button', { name: 'Continue', exact: true }).click();
        await page.getByRole('heading', { name: 'What would you like to achieve?' }).waitFor();
        await page.locator('#learning-goal').fill('');
        await page.getByRole('button', { name: 'Save learning profile', exact: true }).click();
        await page.locator('#learning-goal-error').waitFor();
        assert.equal(await page.locator('#learning-goal').getAttribute('aria-invalid'), 'true');
        await snapshot('validation');
        await page.locator('#learning-goal').fill(`Learn accounting and communication (${width})`);
        await page.locator('#pace').selectOption('steady');
        await page.locator('#minutes-per-week').fill('120');
        await page.locator('#target-weeks').fill('12');
        await page.locator('#language').fill('English');
        await page.getByRole('checkbox', { name: 'Reading', exact: true }).check();
        await page.getByRole('checkbox', { name: 'Practical activities', exact: true }).check();
        await snapshot('goal');
        await page.getByRole('button', { name: 'Save learning profile', exact: true }).click();
        await page.getByRole('heading', { name: 'Your profile is saved' }).waitFor();
        await page.waitForFunction(() => document.activeElement?.textContent.includes('Your profile is saved'));
        await snapshot('saved');
        await page.getByRole('link', { name: 'Edit my profile', exact: true }).click();
        await page.getByRole('heading', { name: 'What would you like to learn?' }).waitFor();
        await page.waitForFunction(() => document.querySelector('#custom-topics')?.value === 'Accounting\nPublic speaking');
        assert.equal(await page.locator('#custom-topics').inputValue(), 'Accounting\nPublic speaking');
        await page.getByRole('button', { name: 'Continue', exact: true }).click();
        await page.locator('#skills').waitFor();
        assert.equal(await page.locator('#skills').inputValue(), 'Spreadsheets');
        await page.getByRole('button', { name: 'Continue', exact: true }).click();
        await page.locator('#learning-goal').waitFor();
        assert.equal(await page.locator('#learning-goal').inputValue(), `Learn accounting and communication (${width})`);
        await page.goto(`${base}/student/dashboard`, { waitUntil: 'domcontentloaded' });
        await page.getByRole('link', { name: 'Update learning profile', exact: true }).waitFor();
        await snapshot('dashboard');
        await context.close();
    }
    assert.deepEqual(errors, [], 'No uncaught JavaScript or local server errors.');
    await writeFile(`${output}/results.json`, JSON.stringify({ records, errors }, null, 2));
    console.log(`${records.length} responsive state checks passed across four widths; back navigation, validation, focus, save and reload passed.`);
} catch (error) {
    if (currentPage && !currentPage.isClosed()) {
        await currentPage.screenshot({ path: `${output}/failure.png`, fullPage: true });
        await writeFile(`${output}/failure.html`, await currentPage.content());
    }
    await writeFile(`${output}/failure.json`, JSON.stringify({ records, errors, failure: error.stack }, null, 2));
    throw error;
} finally {
    await browser.close();
}
