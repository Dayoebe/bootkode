// Requires synthetic fixtures in an independent *_test database and temporary provider stubs.
import assert from 'node:assert/strict';
import { mkdir, writeFile } from 'node:fs/promises';
import { pathToFileURL } from 'node:url';
const { chromium } = await import(pathToFileURL(`${process.env.PLAYWRIGHT_CORE_PATH}/index.mjs`).href);
const base = process.env.BROWSER_BASE_URL;
const output = process.env.BROWSER_OUTPUT_DIR;
const password = process.env.BROWSER_TEST_PASSWORD;
const jobId = process.env.BROWSER_JOB_ID;
assert(base && output && password && jobId && process.env.BROWSER_REVIEWER_EMAIL && process.env.BROWSER_LEARNER_EMAIL);
assert(['localhost','127.0.0.1'].includes(new URL(base).hostname));
await mkdir(output,{recursive:true});
const browser = await chromium.launch({executablePath:process.env.CHROME_PATH || '/usr/bin/google-chrome',headless:true,args:['--no-sandbox','--no-zygote','--single-process','--disable-dev-shm-usage','--disable-gpu','--renderer-process-limit=1','--disable-background-networking','--disable-extensions']});
const records = [], errors = [];
let currentPage;
async function session(width,email) {
 const context = await browser.newContext({viewport:{width,height:1000},reducedMotion:'reduce',serviceWorkers:'block'});
 const page = await context.newPage(); currentPage = page; page.setDefaultTimeout(60000); page.setDefaultNavigationTimeout(60000);
 page.on('pageerror',e=>errors.push({width,message:e.message}));
 page.on('response',r=>{if(r.url().startsWith(base) && r.status()>=500)errors.push({width,status:r.status(),url:r.url()});});
 page.on('dialog',dialog=>dialog.accept());
 await page.route('**/*',route=>new URL(route.request().url()).origin===new URL(base).origin?route.continue():route.fulfill({status:200,contentType:route.request().resourceType()==='stylesheet'?'text/css':'application/javascript',body:''}));
 const loginPage=await context.request.get(`${base}/login`);
 const loginHtml=await loginPage.text();
 const token=loginHtml.match(/name="csrf-token" content="([^"]+)"/)[1];
 const login=await context.request.post(`${base}/login`,{form:{email,password,_token:token},maxRedirects:0});
 assert.equal(login.status(),302,'The actual login endpoint accepted the synthetic account');
 return {context,page};
}
async function snapshot(page,width,state) {
 currentPage=page;
 assert.equal(await page.locator('h1:visible').count(),1,`${state}: one h1`);
 assert(await page.locator('h1').evaluate(el=>getComputedStyle(el).fontFamily.includes('Sora')),'Built shared styles loaded');
 const bounds=await page.evaluate(()=>({viewport:document.documentElement.clientWidth,content:document.documentElement.scrollWidth}));
 const target = {'skills-gap':'#career-opportunity','pending-evidence':'#career-evidence','evidence-review':'#career-evidence-review','requirements-review':'#career-requirements','eligible':'#career-opportunity','advisor-and-history':'#career-question'}[state];
 if(target) await page.locator(target).evaluate(el=>window.scrollTo(0,el.getBoundingClientRect().top+scrollY-96));
 else await page.evaluate(()=>window.scrollTo(0,0));
 await page.screenshot({path:`${output}/${width}-${state}.png`,fullPage:false,animations:'disabled'});
 assert(bounds.content<=bounds.viewport+1,`${width}/${state}: overflow ${JSON.stringify(bounds)}`);
 records.push({width,state,...bounds});
 console.log(`${width}: ${state}`);
}
try {
 for(const width of [390,768,1024,1440]) {
  let learner=await session(width,process.env.BROWSER_LEARNER_EMAIL);
  let page=learner.page;
  await page.goto(`${base}/career-centre?job=${jobId}`,{waitUntil:'commit'});
  await page.getByRole('heading',{name:'Career Centre',exact:true}).waitFor();
  const reassess=page.getByRole('button',{name:'Request reassessment',exact:true});
  if(await reassess.count()) { await reassess.first().click(); await page.getByText('Reassessment requested.',{exact:false}).waitFor(); }
  await page.getByRole('heading',{name:'Assessment or evidence review required',exact:true}).waitFor();
  await page.getByRole('link',{name:'SQL practical foundations',exact:true}).waitFor();
  await snapshot(page,width,'skills-gap');
  if(!(await page.locator('#career-evidence article').count())) {
   await page.locator('#evidence-label').fill('SQL');
   await page.locator('#evidence-reference').fill('Synthetic browser source for SQL competency review; independently checked in this test only.');
   await page.getByRole('button',{name:'Request evidence review',exact:true}).click();
   await page.getByText('Evidence request saved for review.',{exact:false}).waitFor();
  }
  await snapshot(page,width,'pending-evidence');
  await learner.context.close();
  const reviewer=await session(width,process.env.BROWSER_REVIEWER_EMAIL);
  const review=reviewer.page;
  await review.goto(`${base}/admin/career-review`,{waitUntil:'commit'});
  await review.getByRole('button',{name:/Inspect evidence/}).first().click();
  await review.locator('#career-evidence-review').waitFor();
  await review.locator('#evidence-review-reason').fill('Synthetic source and relevance checked for the browser acceptance scenario.');
  await snapshot(review,width,'evidence-review');
  await review.getByRole('button',{name:'Verify evidence',exact:true}).click();
  await review.getByText('Evidence decision recorded.',{exact:false}).waitFor();
  await review.getByRole('button',{name:/Review requirements for Browser SQL Analyst/}).click();
  await review.locator('#career-requirements').waitFor();
  assert.equal(await review.locator('input[wire\\:model="items.1.mandatory"]').isChecked(),false,'Preferred Python stays optional');
  await review.locator('#requirements-reason').fill('Reviewed the synthetic vacancy source, including its mandatory and preferred distinction.');
  await snapshot(review,width,'requirements-review');
  await review.getByRole('button',{name:'Publish reviewed requirements',exact:true}).click();
  await review.getByText('Reviewed requirements saved.',{exact:false}).waitFor();
  await reviewer.context.close();
  learner=await session(width,process.env.BROWSER_LEARNER_EMAIL); page=learner.page;
  await page.goto(`${base}/career-centre?job=${jobId}`,{waitUntil:'commit'});
  await page.getByRole('heading',{name:'Preferred skills to develop',exact:true}).waitFor();
  await snapshot(page,width,'eligible');
  if(width===390 && await page.getByRole('link',{name:'Prepare application',exact:true}).count()) {
   await page.getByRole('link',{name:'Prepare application',exact:true}).click();
   const dialog=page.getByRole('dialog',{name:'Apply for a job',exact:true}); await dialog.waitFor();
   await page.locator('#job-cover-letter').fill('Synthetic browser application describing actual reviewed evidence for this isolated test only.');
   await snapshot(page,width,'application-form');
   await page.keyboard.press('Escape'); await dialog.waitFor({state:'hidden'});
   await page.goto(`${base}/career-centre?job=${jobId}`,{waitUntil:'commit'});
   await page.getByRole('link',{name:'Prepare application',exact:true}).click(); await dialog.waitFor();
   await page.locator('#job-cover-letter').fill('Synthetic browser application describing actual reviewed evidence for this isolated test only.');
   await page.getByRole('button',{name:'Submit Application',exact:true}).click();
   await page.getByText('Application received.',{exact:false}).waitFor();
  }
  await page.goto(`${base}/career-centre?job=${jobId}`,{waitUntil:'commit'});
  await page.locator('#career-question').fill('How can I prepare for a SQL interview?');
  await page.getByRole('checkbox',{name:/For this request, share my learning goal/}).check();
  await page.getByRole('button',{name:'Ask career advisor',exact:true}).click();
  await page.getByText('Suggestion: practise SQL joins with your own sample data.',{exact:false}).waitFor();
  await snapshot(page,width,'advisor-and-history');
  await page.goto(`${base}/search/job`,{waitUntil:'commit'});
  await page.getByRole('heading',{name:'Provider Browser Analyst',exact:true}).waitFor();
  const link=page.getByRole('link',{name:'Open on Adzuna',exact:true});
  assert.equal(await link.getAttribute('href'),'https://www.adzuna.co.uk/jobs/land/ad/98765?tracking=original');
  assert.equal(await link.getAttribute('rel'),'noopener noreferrer');
  assert(! (await page.locator('body').innerText()).includes('% Match'));
  await snapshot(page,width,'external-provider');
  await learner.context.close();
 }
 assert.deepEqual(errors,[]);
 await writeFile(`${output}/results.json`,JSON.stringify({records,errors,provider:'controlled synthetic responses; no live AI/Adzuna request'},null,2));
 console.log(`${records.length} responsive career states passed.`);
} catch(error) {
 await writeFile(`${output}/failure.json`,JSON.stringify({records,errors,failure:error.stack},null,2));
 if(currentPage&&!currentPage.isClosed()){
  try{await writeFile(`${output}/failure.html`,await currentPage.content());}catch{}
  try{await currentPage.screenshot({path:`${output}/failure.png`,fullPage:false,timeout:5000});}catch{}
 }
 throw error;
} finally {await browser.close();}
