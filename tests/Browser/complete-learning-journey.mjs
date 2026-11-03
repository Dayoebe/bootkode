// Synthetic *_test database only. Exercises the real browser, Livewire, persistence and certificate flow.
import assert from 'node:assert/strict';
import {mkdir, readFile, writeFile} from 'node:fs/promises';
import {pathToFileURL} from 'node:url';

const {chromium} = await import(pathToFileURL(`${process.env.PLAYWRIGHT_CORE_PATH}/index.mjs`).href);
const fixtures = JSON.parse(await readFile(process.env.BROWSER_JOURNEY_FIXTURES, 'utf8'));
const base = process.env.BROWSER_BASE_URL;
const output = process.env.BROWSER_OUTPUT_DIR;
const password = process.env.BROWSER_TEST_PASSWORD;
assert(base && output && password && ['127.0.0.1', 'localhost'].includes(new URL(base).hostname));
await mkdir(output, {recursive: true});

const browser = await chromium.launch({
    executablePath: process.env.CHROME_PATH || '/usr/bin/google-chrome',
    headless: true,
    args: ['--no-sandbox', '--no-zygote', '--single-process', '--disable-dev-shm-usage', '--disable-gpu'],
});
const context = await browser.newContext({viewport: {width: 390, height: 844}, reducedMotion: 'reduce'});
const page = await context.newPage();
page.setDefaultTimeout(45000);
const errors = [];
page.on('pageerror', error => errors.push(error.message));
page.on('response', response => {
    if (response.url().startsWith(base) && response.status() >= 500) errors.push(`HTTP ${response.status()}: ${new URL(response.url()).pathname}`);
});
await page.route('**/*', route => new URL(route.request().url()).origin === new URL(base).origin ? route.continue() : route.fulfill({status: 200, body: ''}));

async function login() {
    const response = await context.request.get(`${base}/login`);
    const token = (await response.text()).match(/name="csrf-token" content="([^"]+)"/)[1];
    const result = await context.request.post(`${base}/login`, {form: {email: fixtures.email, password, _token: token}, maxRedirects: 0});
    assert.equal(result.status(), 302);
}

try {
    await login();
    await page.goto(base + fixtures.course, {waitUntil: 'domcontentloaded'});
    await page.getByRole('heading', {name: fixtures.course_title}).waitFor();

    const note = 'Browser journey note: verify server boundaries.';
    await page.getByLabel(/Private notes for/).fill(note);
    await page.getByRole('button', {name: 'Save note'}).click();
    await page.getByText('Lesson note saved.', {exact: true}).waitFor();
    await page.getByRole('button', {name: 'Bookmark lesson'}).click();
    await page.getByRole('button', {name: 'Bookmarked'}).waitFor();

    await page.getByRole('button', {name: 'Start assessment'}).click();
    await page.getByLabel(fixtures.correct_answer).check();
    await page.getByText('Answer saved.', {exact: true}).waitFor();

    await page.reload({waitUntil: 'domcontentloaded'});
    await page.getByRole('button', {name: 'Resume attempt'}).click();
    assert(await page.getByLabel(fixtures.correct_answer).isChecked(), 'Saved assessment answer survives reload and resume');
    await page.getByRole('button', {name: 'Review submission'}).click();
    await page.getByRole('dialog', {name: 'Submit lesson assessment'}).getByRole('button', {name: 'Submit assessment'}).click();
    await page.getByText('Passed', {exact: true}).waitFor();

    const firstNoteId = await page.getByLabel(/Private notes for/).getAttribute('id');
    await page.getByRole('button', {name: 'Continue', exact: true}).click();
    await page.waitForFunction(id => document.querySelector('textarea[id^="lesson-notes-"]')?.id !== id, firstNoteId);
    assert.equal(await page.getByLabel(/Private notes for/).inputValue(), '', 'Notes are scoped to one lesson');
    await page.getByRole('button', {name: 'Complete Course'}).click();
    await page.waitForURL(url => url.pathname === fixtures.certificates);
    await page.getByRole('heading', {name: 'My Certificates'}).waitFor();
    await page.getByText(fixtures.course_title, {exact: true}).waitFor();

    await page.screenshot({path: `${output}/complete-learning-journey.png`, fullPage: true, animations: 'disabled'});
    assert.deepEqual(errors, []);
    await writeFile(`${output}/journey-results.json`, JSON.stringify({
        course: fixtures.course,
        lessons: fixtures.lesson_ids.length,
        assessmentResume: true,
        notePersisted: true,
        bookmarkPersisted: true,
        certificateVisible: true,
        errors,
        limits: 'Synthetic loopback *_test fixture; live email, payment, AI and production load are not exercised.',
    }, null, 2));
    console.log('Passed the complete learner journey from course workspace through certificate.');
} catch (error) {
    await page.screenshot({path: `${output}/journey-failure.png`, fullPage: true}).catch(() => {});
    await writeFile(`${output}/journey-failure.json`, JSON.stringify({error: error.stack, errors}, null, 2));
    throw error;
} finally {
    await browser.close();
}
