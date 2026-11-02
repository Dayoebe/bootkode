// Local, synthetic *_test fixtures only. Uses real auth, Livewire and service worker paths.
import assert from 'node:assert/strict';
import {mkdir, readFile, writeFile} from 'node:fs/promises';
import {pathToFileURL} from 'node:url';
const {chromium} = await import(pathToFileURL(`${process.env.PLAYWRIGHT_CORE_PATH}/index.mjs`).href);
const fixtures = JSON.parse(await readFile(process.env.BROWSER_FIXTURES, 'utf8'));
const base = process.env.BROWSER_BASE_URL;
const output = process.env.BROWSER_OUTPUT_DIR;
const password = process.env.BROWSER_TEST_PASSWORD;
assert(base && output && password && ['127.0.0.1', 'localhost'].includes(new URL(base).hostname));
await mkdir(output, {recursive: true});
const browser = await chromium.launch({executablePath: process.env.CHROME_PATH || '/usr/bin/google-chrome', headless: true,
 args: ['--no-sandbox','--no-zygote','--single-process','--disable-dev-shm-usage','--disable-gpu','--disable-background-networking','--disable-extensions']});
const states = [], errors = [], profiles = [];
let lastPage;
async function login(context, email) {
 const response = await context.request.get(`${base}/login`);
 const token = (await response.text()).match(/name="csrf-token" content="([^"]+)"/)[1];
 const result = await context.request.post(`${base}/login`, {form: {email, password, _token: token}, maxRedirects: 0});
 assert.equal(result.status(), 302, 'Actual CSRF login accepted the synthetic fixture');
}
async function session(width, email = 'learner@example.test') {
 const context = await browser.newContext({viewport: {width, height: 1000}, reducedMotion: 'reduce'});
 const page = await context.newPage(); lastPage = page;
 page.setDefaultTimeout(45000);
 page.on('pageerror', error => errors.push(error.message));
 page.on('response', response => {
  if (response.url().startsWith(base) && response.status() >= 500) errors.push(`HTTP ${response.status()}: ${new URL(response.url()).pathname}`);
  if (response.headers()['x-bootkode-test-ms']) profiles.push({path: new URL(response.url()).pathname, serverMs: Number(response.headers()['x-bootkode-test-ms']), queries: Number(response.headers()['x-bootkode-test-queries'])});
 });
 await page.route('**/*', route => new URL(route.request().url()).origin === new URL(base).origin ? route.continue() : route.fulfill({status: 200, body: ''}));
 await page.addInitScript(() => {
  window.__bootkodeMetrics = {cls: 0, lcp: null};
  if (PerformanceObserver.supportedEntryTypes.includes('layout-shift')) new PerformanceObserver(list => {
   for (const entry of list.getEntries()) if (!entry.hadRecentInput) window.__bootkodeMetrics.cls += entry.value;
  }).observe({type: 'layout-shift', buffered: true});
  if (PerformanceObserver.supportedEntryTypes.includes('largest-contentful-paint')) new PerformanceObserver(list => {
   window.__bootkodeMetrics.lcp = list.getEntries().at(-1)?.startTime ?? null;
  }).observe({type: 'largest-contentful-paint', buffered: true});
 });
 await login(context, email);
 return {context, page};
}
async function capture(page, width, name) {
 await page.locator('h1:visible').first().waitFor();
 assert.equal(await page.locator('h1:visible').count(), 1, `${name}: one primary heading`);
 const bounds = await page.evaluate(() => ({viewport: document.documentElement.clientWidth, content: document.documentElement.scrollWidth, metrics: window.__bootkodeMetrics}));
 assert(bounds.content <= bounds.viewport + 1, `${width}/${name}: horizontal overflow`);
 await page.screenshot({path: `${output}/${width}-${name}.png`, animations: 'disabled'});
 states.push({width, name, ...bounds});
 console.log(`${width}: ${name}`);
}
try {
 for (const width of [360,390,430,768,1024,1440,1920]) {
  const {context, page} = await session(width);
  const requests = [];
  page.on('request', request => requests.push(request.url()));
  await page.goto(base + fixtures.dashboard);
  await page.waitForFunction(() => window.Livewire && window.Chart && window.bootkodeOffline);
  assert(!requests.some(url => /\/editors-[^/]+\.(js|css)/.test(url)), 'Learner dashboard does not fetch Trix');
  await capture(page, width, 'dashboard');
  await page.evaluate(() => {window.__navigationMarker = 'same-window';});
  await page.evaluate(path => window.Livewire.navigate(path), fixtures.profile);
  await page.waitForURL(base + fixtures.profile);
  await page.locator('h1:visible').waitFor();
  assert.equal(await page.evaluate(() => window.__navigationMarker), 'same-window', 'Livewire navigation preserves the JS realm');
  await page.goBack();
  await page.waitForURL(base + fixtures.dashboard);
  await capture(page, width, 'history-restored');
  await page.goto(base + fixtures.course);
  await page.waitForFunction(() => !!window.bootkodeOffline);
  await capture(page, width, 'online-course');
  await page.evaluate(() => navigator.serviceWorker.ready);
  await page.waitForFunction(() => !!navigator.serviceWorker.controller);
  const result = await page.evaluate(async slug => {
   const pack = await window.bootkodeOffline.installPack({manifestUrl: `/offline-learning/packs/${slug}/manifest`, types: ['lessons'], storageLimitMb: 50});
   return {owner: pack.user_id, status: pack.status, failed: pack.cache_failures.length};
  }, fixtures.slug);
  assert.equal(result.owner, fixtures.owner_id);
  assert.equal(result.failed, 0);
  assert.equal(result.status, 'cached');
  const cached = await page.evaluate(async () => {
   const requests = await (await caches.open('bootkode-offline-packs-v1')).keys();
   return requests.map(request => new URL(request.url).pathname);
  });
  assert(!cached.some(path => path.startsWith('/course/') || path.includes('/wallet') || path.includes('/dashboard')), 'No private application HTML in pack cache');
  await context.setOffline(true);
  await page.goto(`${base}/offline-learning.html?course=${fixtures.slug}`);
  await page.locator('[data-complete]').click();
  await page.getByText('Saved on this device. Reconnect to confirm progress with BootKode.').waitFor();
  assert.equal(await page.locator('h1').textContent(), 'Practice lesson 1');
  await capture(page, width, 'offline-reader');
  await page.goto(`${base}/dashboard/wallet`);
  await page.getByRole('heading', {name: /offline/i}).waitFor();
  assert(!(await page.textContent('body')).includes('Synthetic learner'), 'Offline navigation never replays account HTML');
  await page.goto(`${base}/offline-learning.html?course=${fixtures.slug}`);
  await context.setOffline(false);
  await page.locator('[data-sync]').click();
  await page.waitForFunction(() => window.bootkodeOffline.getPacks().every(pack => pack.pendingCount === 0));
  const manifest = await context.request.get(`${base}/offline-learning/packs/${fixtures.slug}/manifest`);
  assert((await manifest.json()).completed_lesson_ids.includes(fixtures.lesson_ids[0]), 'Backend confirmed the offline completion');
  await context.close();
 }
 // Exercise writes made while an acknowledgement is in flight and a lost reply.
 const race = await session(390);
 const page = race.page;
 await page.goto(base + fixtures.course);
 await page.evaluate(async slug => window.bootkodeOffline.installPack({manifestUrl: `/offline-learning/packs/${slug}/manifest`, types: ['lessons'], storageLimitMb: 50}), fixtures.slug);
 const event = {type: 'lesson_completed', lesson_id: fixtures.lesson_ids[0], time_spent_seconds: 31};
 await page.evaluate(async ({slug,event}) => window.bootkodeOffline.queueProgress(slug,event), {slug: fixtures.slug,event});
 let intercepted = false;
 await page.route('**/offline-learning/packs/*/sync', async route => {
  if (!intercepted) {
   intercepted = true;
   await page.evaluate(async ({slug,event}) => window.bootkodeOffline.queueProgress(slug,event), {slug: fixtures.slug,event: {...event,time_spent_seconds: 45}});
  }
  await route.continue();
 });
 const first = await page.evaluate(() => window.bootkodeOffline.syncQueuedProgress());
 assert.equal(first.pending, 1, 'Newer event survived the earlier acknowledgement');
 await page.unroute('**/offline-learning/packs/*/sync');
 const second = await page.evaluate(() => window.bootkodeOffline.syncQueuedProgress());
 assert.equal(second.pending, 0);
 await page.evaluate(async ({slug,event}) => window.bootkodeOffline.queueProgress(slug,event), {slug: fixtures.slug,event});
 await page.route('**/offline-learning/packs/*/sync', async route => {await route.fetch(); await route.abort('failed');});
 const failure = await page.evaluate(async () => {try {await window.bootkodeOffline.syncQueuedProgress(); return false;} catch {return true;}});
 assert(failure, 'Lost acknowledgement is reported as failure');
 assert.equal(await page.evaluate(() => window.bootkodeOffline.getPacks()[0].pendingCount), 1);
 await page.unroute('**/offline-learning/packs/*/sync');
 await page.evaluate(() => window.bootkodeOffline.syncQueuedProgress());
 assert.equal(await page.evaluate(() => window.bootkodeOffline.getPacks()[0].pendingCount), 0);
 await capture(page,390,'acknowledgement-recovery');
 // Session expiry must retain queued work and another account must not receive it.
 await page.evaluate(async ({slug,event}) => window.bootkodeOffline.queueProgress(slug,event), {slug: fixtures.slug,event});
 await race.context.clearCookies();
 const expired = await page.evaluate(async () => {try {await window.bootkodeOffline.syncQueuedProgress(); return false;} catch {return true;}});
 assert(expired);
 assert.equal(await page.evaluate(() => window.bootkodeOffline.getPacks()[0].pendingCount), 1);
 await login(race.context,'other@example.test');
 await page.goto(base + fixtures.dashboard);
 assert.equal(await page.evaluate(() => window.bootkodeOffline.getPacks().length), 0, 'Another account sees no private pack metadata');
 await capture(page,390,'account-boundary');
 // Remove the browser cleanup header from a real logout response to test the JS fallback too.
 await page.route('**/logout', async route => {
  const response = await route.fetch({maxRedirects: 0});
  const headers = {...response.headers()}; delete headers['clear-site-data'];
  await route.fulfill({response, headers});
 });
 await Promise.all([page.waitForURL(base + '/'), page.evaluate(() => {
  const form = document.createElement('form'); form.method = 'POST'; form.action = '/logout';
  const token = document.createElement('input'); token.name = '_token'; token.value = document.querySelector('meta[name="csrf-token"]').content;
  form.append(token); document.body.append(form); form.submit();
 })]);
 await page.waitForFunction(async () => !(await indexedDB.databases()).some(db => db.name === 'bootkode-offline-learning'));
 assert.equal(await page.evaluate(() => Object.keys(localStorage).some(key => key.startsWith('bootkode:offline:metadata:'))), false, 'Confirmed logout fallback cleared private pack metadata');
 await race.context.close();
 // Storage failure is isolated to the optional offline feature, not the whole course.
 const blocked = await session(390);
 await blocked.page.addInitScript(() => {
  const get = Storage.prototype.getItem, set = Storage.prototype.setItem;
  Storage.prototype.getItem = function(key) {if (String(key).startsWith('bootkode:offline:')) throw new Error('Synthetic offline storage denial'); return get.call(this,key);};
  Storage.prototype.setItem = function(key,value) {if (String(key).startsWith('bootkode:offline:')) throw new Error('Synthetic offline storage denial'); return set.call(this,key,value);};
 });
 await blocked.page.goto(base + fixtures.course);
 await blocked.page.locator('h1:visible').waitFor();
 const unavailable = await blocked.page.evaluate(async slug => {
  try {await window.bootkodeOffline.installPack({manifestUrl: `/offline-learning/packs/${slug}/manifest`, types: ['lessons'], storageLimitMb: 50}); return '';}
  catch(error) {return error.message;}
 }, fixtures.slug);
 assert(unavailable.includes('Offline metadata could not be saved'), 'Blocked storage has an explicit recoverable result');
 await blocked.context.close();
 for (const width of [390,1440]) {
  const {context,page} = await session(width,'reviewer@example.test');
  await page.goto(`${base}${fixtures.builder}?lesson=${fixtures.lesson_ids[0]}`);
  await page.locator('trix-editor').waitFor();
  await page.waitForFunction(() => !!document.querySelector('trix-editor')?.editor);
  assert(await page.evaluate(() => customElements.get('trix-editor') !== undefined));
  const outline = await page.locator('input[id^="outline-new-lesson-"]').first().evaluate(input => {
   const field = input.getBoundingClientRect(), form = input.closest('form').getBoundingClientRect();
   return {right: field.right, parentRight: form.right, width: field.width};
  });
  assert(outline.width > 0 && outline.right <= outline.parentRight + 1, 'Lesson creation field stays inside its outline panel');
  await capture(page,width,'lesson-editor');
  await page.evaluate(path => window.Livewire.navigate(path), fixtures.blog);
  await page.waitForURL(base + fixtures.blog);
  await page.waitForFunction(() => !!document.querySelector('trix-editor')?.editor);
  await capture(page,width,'blog-editor');
  await page.goto(base + fixtures.qa);
  await page.getByRole('button',{name:'Check Media',exact:true}).first().click();
  await page.getByText('Media check queued.',{exact:false}).waitFor();
  await capture(page,width,'queued-media-check');
  await page.goto(`${base}/offline-learning`);
  await page.getByRole('heading', {name: 'Offline Learning Packs'}).waitFor();
  await capture(page,width,'offline-library');
  await context.close();
 }
 assert.deepEqual(errors,[],'No uncaught JavaScript or local server errors');
 await writeFile(`${output}/results.json`,JSON.stringify({states,errors,profiles,limits:'Synthetic loopback fixtures; external analytics/fonts/MathJax excluded; no field INP, live providers or production load claim.'},null,2));
 console.log(`Passed ${states.length} authenticated responsive states plus offline/retry/account invariants.`);
} catch(error) {
 await lastPage?.screenshot({path:`${output}/failure.png`}).catch(()=>{});
 await writeFile(`${output}/failure.json`,JSON.stringify({error:error.stack,states,errors,profiles},null,2));
 throw error;
} finally {await browser.close();}
