// Local, synthetic *_test fixtures only. Uses real auth, Livewire and service worker paths.
import assert from 'node:assert/strict';
import {mkdir, readFile, writeFile} from 'node:fs/promises';
import {pathToFileURL} from 'node:url';
const {chromium} = await import(pathToFileURL(`${process.env.PLAYWRIGHT_CORE_PATH}/index.mjs`).href);
const fixtures = JSON.parse(await readFile(process.env.BROWSER_FIXTURES, 'utf8'));
const base = process.env.BROWSER_BASE_URL;
const output = process.env.BROWSER_OUTPUT_DIR;
const password = process.env.BROWSER_TEST_PASSWORD;
const widths = process.env.BROWSER_WIDTHS ? process.env.BROWSER_WIDTHS.split(',').map(Number) : [360,390,430,768,1024,1440,1920];
assert(base && output && password && ['127.0.0.1', 'localhost'].includes(new URL(base).hostname));
await mkdir(output, {recursive: true});
const browser = await chromium.launch({executablePath: process.env.CHROME_PATH || '/usr/bin/google-chrome', headless: true,
 args: ['--no-sandbox','--no-zygote','--single-process','--disable-dev-shm-usage','--disable-gpu','--disable-background-networking','--disable-extensions']});
const states = [], errors = [], profiles = [], accessibility = [];
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
  window.__bootkodeMetrics = {cls: 0, lcp: null, shifts: []};
  if (PerformanceObserver.supportedEntryTypes.includes('layout-shift')) new PerformanceObserver(list => {
   for (const entry of list.getEntries()) if (!entry.hadRecentInput) {
    window.__bootkodeMetrics.cls += entry.value;
    if (entry.value >= 0.01 && window.__bootkodeMetrics.shifts.length < 8) {
     window.__bootkodeMetrics.shifts.push({
      value: entry.value,
      sources: (entry.sources || []).slice(0, 5).map(source => {
       const node = source.node;
       return {
        node: node ? `${node.tagName?.toLowerCase() || ''}${node.id ? `#${node.id}` : ''}${node.classList?.length ? `.${[...node.classList].slice(0, 3).join('.')}` : ''}` : 'unknown',
        previousRect: source.previousRect,
        currentRect: source.currentRect,
       };
      }),
     });
    }
   }
  }).observe({type: 'layout-shift', buffered: true});
  if (PerformanceObserver.supportedEntryTypes.includes('largest-contentful-paint')) new PerformanceObserver(list => {
   window.__bootkodeMetrics.lcp = list.getEntries().at(-1)?.startTime ?? null;
  }).observe({type: 'largest-contentful-paint', buffered: true});
 });
 await login(context, email);
 return {context, page};
}
async function resetMetrics(page) {
 await page.evaluate(() => { window.__bootkodeMetrics = {cls: 0, lcp: null, shifts: []}; });
}
async function capture(page, width, name) {
 await page.locator('h1:visible').first().waitFor();
 assert.equal(await page.locator('h1:visible').count(), 1, `${name}: one primary heading`);
 const bounds = await page.evaluate(() => ({viewport: document.documentElement.clientWidth, content: document.documentElement.scrollWidth, metrics: window.__bootkodeMetrics}));
 assert(bounds.content <= bounds.viewport + 1, `${width}/${name}: horizontal overflow`);
 assert(bounds.metrics.cls <= 0.1, `${width}/${name}: CLS ${bounds.metrics.cls.toFixed(4)} exceeded 0.10`);
 await page.screenshot({path: `${output}/${width}-${name}.png`, animations: 'disabled'});
 states.push({width, name, ...bounds});
 console.log(`${width}: ${name}`);
}
async function visit(page, url) {
 await page.goto(url, {waitUntil: 'domcontentloaded'});
}
async function auditDashboardAccessibility(page, width) {
 const result = await page.evaluate(() => {
  const visible = element => {
   const style = getComputedStyle(element), rect = element.getBoundingClientRect();
   return style.visibility !== 'hidden' && style.display !== 'none' && rect.width > 0 && rect.height > 0;
  };
  const ids = [...document.querySelectorAll('[id]')].map(element => element.id).filter(Boolean);
  const duplicateIds = [...new Set(ids.filter((id, index) => ids.indexOf(id) !== index))];
  const unnamedButtons = [...document.querySelectorAll('button')].filter(visible).filter(button =>
   !(button.getAttribute('aria-label') || button.getAttribute('title') || button.textContent.trim()));
  const unlabeledFields = [...document.querySelectorAll('input:not([type="hidden"]), select, textarea')].filter(visible).filter(field =>
   !(field.getAttribute('aria-label') || field.getAttribute('aria-labelledby') || field.closest('label') || (field.id && document.querySelector(`label[for="${CSS.escape(field.id)}"]`))));
  const shortTargets = [...document.querySelectorAll('button, .bk-button, .bk-bottom-nav a')].filter(visible).filter(element => element.getBoundingClientRect().height < 43).map(element => ({tag: element.tagName, text: element.textContent.trim().slice(0, 60), classes: element.className, height: element.getBoundingClientRect().height}));
  const root = document.documentElement;
  const parse = name => getComputedStyle(root).getPropertyValue(name).trim().split(/\s+/).map(Number);
  const luminance = rgb => {
   const values = rgb.map(value => { const channel = value / 255; return channel <= .03928 ? channel / 12.92 : ((channel + .055) / 1.055) ** 2.4; });
   return .2126 * values[0] + .7152 * values[1] + .0722 * values[2];
  };
  const contrast = (a, b) => { const [bright, dark] = [luminance(a), luminance(b)].sort((x, y) => y - x); return (bright + .05) / (dark + .05); };
  const themes = ['light', 'dark', 'sepia', 'ocean', 'forest'].map(theme => {
   root.classList.remove('light', 'dark', 'sepia', 'ocean', 'forest'); root.classList.add(theme);
   return {theme, primary: contrast(parse('--text-primary'), parse('--bg-secondary')), secondary: contrast(parse('--text-secondary'), parse('--bg-secondary')), accent: contrast(parse('--accent-primary'), parse('--bg-secondary')), action: contrast(parse('--bk-action-text'), parse('--bk-action'))};
  });
  root.classList.remove('light', 'dark', 'sepia', 'ocean', 'forest'); root.classList.add('light');
  return {duplicateIds, unnamedButtons: unnamedButtons.length, unlabeledFields: unlabeledFields.length, shortTargets, themes, skipLink: Boolean(document.querySelector('a[href="#main-content"]'))};
 });
 assert.deepEqual(result.duplicateIds, [], 'Dashboard has no duplicate IDs');
 assert.equal(result.unnamedButtons, 0, 'Visible dashboard buttons have accessible names');
 assert.equal(result.unlabeledFields, 0, 'Visible dashboard fields have accessible labels');
 assert.deepEqual(result.shortTargets, [], `Core dashboard controls meet the 44px touch target: ${JSON.stringify(result.shortTargets)}`);
 assert(result.skipLink, 'Dashboard provides a skip link');
 for (const theme of result.themes) for (const metric of ['primary','secondary','accent','action']) assert(theme[metric] >= 4.5, `${theme.theme} ${metric} contrast is ${theme[metric].toFixed(2)}`);
 await page.keyboard.press('Tab');
 const focus = await page.evaluate(() => { const style = getComputedStyle(document.activeElement); return {tag: document.activeElement.tagName, outline: parseFloat(style.outlineWidth), transition: style.transitionDuration}; });
 assert(focus.outline >= 2, 'Keyboard focus is visibly outlined');
 accessibility.push({width, ...result, focus});
}
try {
 for (const width of widths) {
  const {context, page} = await session(width);
  const requests = [];
  page.on('request', request => requests.push(request.url()));
  await visit(page, base + fixtures.dashboard);
  await page.waitForFunction(() => window.Livewire && window.Chart && window.bootkodeOffline);
  assert(!requests.some(url => /\/editors-[^/]+\.(js|css)/.test(url)), 'Learner dashboard does not fetch Trix');
  assert(!requests.some(url => /mathjax|googletagmanager/i.test(url)), 'Dashboard omits route-specific math and unconsented analytics assets');
  await capture(page, width, 'dashboard');
  if ([390,1440].includes(width)) await auditDashboardAccessibility(page, width);
  if (width === 390) {
   await page.evaluate(() => window.dispatchEvent(new CustomEvent('bootkode:pwa-install-state', {detail: {available: true}})));
   await page.getByText('Install BootKode', {exact: true}).waitFor();
   await page.getByRole('button', {name: 'Dismiss app prompt'}).click();
  }
  await page.evaluate(() => {window.__navigationMarker = 'same-window';});
  await page.evaluate(path => window.Livewire.navigate(path), fixtures.profile);
  await page.waitForURL(base + fixtures.profile);
  await page.locator('h1:visible').waitFor();
  assert.equal(await page.evaluate(() => window.__navigationMarker), 'same-window', 'Livewire navigation preserves the JS realm');
  await resetMetrics(page);
  const historyRestored = page.evaluate(() => new Promise(resolve => document.addEventListener('livewire:navigated', resolve, {once: true})));
  await page.goBack();
  await page.waitForURL(base + fixtures.dashboard);
  await historyRestored;
  await capture(page, width, 'history-restored');
  await resetMetrics(page);
  await visit(page, base + fixtures.course);
  await page.waitForFunction(() => !!window.bootkodeOffline);
  await capture(page, width, 'online-course');
  if (width === 390) {
   await page.evaluate(() => navigator.serviceWorker.ready);
   await page.waitForFunction(() => !!navigator.serviceWorker.controller);
   assert.equal(await page.evaluate(async () => (await caches.keys()).filter(key => key.startsWith('bootkode-pwa-') && key.endsWith('-static')).length), 1, 'Only the current app-shell cache remains');
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
   await visit(page, `${base}/offline-learning.html?course=${fixtures.slug}`);
   await page.locator('[data-complete]').click();
   await page.getByText('Saved on this device. Reconnect to confirm progress with BootKode.').waitFor();
   assert.equal(await page.locator('h1').textContent(), 'Practice lesson 1');
   await capture(page, width, 'offline-reader');
   await visit(page, `${base}/dashboard/wallet`);
   await page.getByRole('heading', {name: /offline/i}).waitFor();
   assert(!(await page.textContent('body')).includes('Synthetic learner'), 'Offline navigation never replays account HTML');
   await visit(page, `${base}/offline-learning.html?course=${fixtures.slug}`);
   await context.setOffline(false);
   await page.locator('[data-sync]').click();
   await page.waitForFunction(() => window.bootkodeOffline.getPacks().every(pack => pack.pendingCount === 0));
   const manifest = await context.request.get(`${base}/offline-learning/packs/${fixtures.slug}/manifest`);
   assert((await manifest.json()).completed_lesson_ids.includes(fixtures.lesson_ids[0]), 'Backend confirmed the offline completion');
  }
  await context.close();
 }
 // Exercise writes made while an acknowledgement is in flight and a lost reply.
 const race = await session(390);
 const page = race.page;
 await visit(page, base + fixtures.course);
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
 await visit(page, base + fixtures.dashboard);
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
 await visit(blocked.page, base + fixtures.course);
 await blocked.page.locator('h1:visible').waitFor();
 const unavailable = await blocked.page.evaluate(async slug => {
  try {await window.bootkodeOffline.installPack({manifestUrl: `/offline-learning/packs/${slug}/manifest`, types: ['lessons'], storageLimitMb: 50}); return '';}
  catch(error) {return error.message;}
 }, fixtures.slug);
 assert(unavailable.includes('Offline metadata could not be saved'), 'Blocked storage has an explicit recoverable result');
 await blocked.context.close();
 for (const width of [390,1440]) {
  const {context,page} = await session(width,'reviewer@example.test');
  await visit(page, `${base}${fixtures.builder}?lesson=${fixtures.lesson_ids[0]}`);
  await page.locator('trix-editor').waitFor();
  await page.waitForFunction(() => !!document.querySelector('trix-editor')?.editor);
  assert(await page.evaluate(() => customElements.get('trix-editor') !== undefined));
  const outline = await page.locator('input[id^="outline-new-lesson-"]').first().evaluate(input => {
   const field = input.getBoundingClientRect(), form = input.closest('form').getBoundingClientRect();
   return {right: field.right, parentRight: form.right, width: field.width};
  });
  assert(outline.width > 0 && outline.right <= outline.parentRight + 1, 'Lesson creation field stays inside its outline panel');
  await capture(page,width,'lesson-editor');
  await resetMetrics(page);
  await page.evaluate(path => window.Livewire.navigate(path), fixtures.blog);
  await page.waitForURL(base + fixtures.blog);
  await page.waitForFunction(() => !!document.querySelector('trix-editor')?.editor);
  await capture(page,width,'blog-editor');
  await visit(page, base + fixtures.qa);
  await page.getByRole('button',{name:'Check Media',exact:true}).first().click();
  await page.getByText('Media check queued.',{exact:false}).waitFor();
  await capture(page,width,'queued-media-check');
  await visit(page, `${base}/offline-learning`);
  await page.getByRole('heading', {name: 'Offline Learning Packs'}).waitFor();
  await capture(page,width,'offline-library');
  await context.close();
 }
 assert.deepEqual(errors,[],'No uncaught JavaScript or local server errors');
 await writeFile(`${output}/results.json`,JSON.stringify({states,errors,profiles,accessibility,limits:'Synthetic loopback fixtures; external analytics/fonts/MathJax excluded; no field INP, live providers or production load claim.'},null,2));
 console.log(`Passed ${states.length} authenticated responsive states plus offline/retry/account invariants.`);
} catch(error) {
 await lastPage?.screenshot({path:`${output}/failure.png`}).catch(()=>{});
 await writeFile(`${output}/failure.json`,JSON.stringify({error:error.stack,states,errors,profiles},null,2));
 throw error;
} finally {await browser.close();}
