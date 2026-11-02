(function () {
    if (window.bootkodeOffline) return;
    const OWNER_KEY = 'bootkode:offline:owner';
    const PACK_CACHE = 'bootkode-offline-packs-v1';
    let databasePromise;
    let syncing;
    let sessionRevoked = false;
    function ownerId() {
        if (sessionRevoked) return 0;
        const meta = document.querySelector('meta[name="bootkode-user-id"]');
        try {
            const owner = meta ? Number(meta.content || 0) : Number(localStorage.getItem(OWNER_KEY) || 0);
            if (meta) localStorage.setItem(OWNER_KEY, String(owner));
            return owner;
        } catch {
            // The online course remains usable when this secondary device feature is blocked.
            return meta ? Number(meta.content || 0) : 0;
        }
    }
    function readJson(key, fallback) {
        try { return JSON.parse(localStorage.getItem(key) || 'null') ?? fallback; }
        catch { return fallback; }
    }
    function writeJson(key, value) {
        try { localStorage.setItem(key, JSON.stringify(value)); }
        catch { throw new Error('Offline metadata could not be saved. Allow device storage or continue learning online.'); }
    }
    function packKey(slug) { return `${ownerId()}:${slug}`; }
    function metadataKey(slug) { return `bootkode:offline:metadata:${packKey(slug)}`; }
    function indexKey() { return `bootkode:offline:index:${ownerId()}`; }
    function packIndex() { return readJson(indexKey(), []); }
    function storedMetadata(slug) { return readJson(metadataKey(slug), null); }
    function changed() { window.dispatchEvent(new CustomEvent('bootkode-offline-packs-updated')); }
    function database() {
        if (!window.indexedDB) return Promise.reject(new Error('This browser does not support offline learning storage.'));
        if (!databasePromise) databasePromise = new Promise((resolve, reject) => {
            const request = indexedDB.open('bootkode-offline-learning', 1);
            request.onupgradeneeded = () => {
                request.result.createObjectStore('packs');
                request.result.createObjectStore('queues');
            };
            request.onsuccess = () => { request.result.onversionchange = () => request.result.close(); resolve(request.result); };
            request.onerror = () => { databasePromise = null; reject(new Error('Offline storage could not be opened. Check device storage permissions.')); };
        });
        return databasePromise;
    }
    async function readStore(store, key) {
        const db = await database();
        return new Promise((resolve, reject) => {
            const request = db.transaction(store).objectStore(store).get(key);
            request.onsuccess = () => resolve(request.result ?? null);
            request.onerror = () => reject(new Error('Offline data could not be read.'));
        });
    }
    async function writeStore(store, key, value) {
        const db = await database();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(store, 'readwrite');
            const table = tx.objectStore(store);
            value === null ? table.delete(key) : table.put(value, key);
            tx.oncomplete = () => resolve();
            tx.onerror = tx.onabort = () => reject(new Error('Offline changes could not be saved. Check available device storage.'));
        });
    }
    async function storedPack(slug) {
        const pack = await readStore('packs', packKey(slug));
        return pack && Number(pack.user_id) === ownerId() ? pack : null;
    }
    function metadata(pack) {
        return {user_id: pack.user_id, course: pack.course, installed_at: pack.installed_at,
            status: pack.status, pendingCount: pack.pendingCount || 0, estimated_size_mb: pack.estimated_size_mb,
            cached_asset_count: pack.cached_asset_count, cache_failures: pack.cache_failures || []};
    }
    async function savePack(pack) {
        if (!ownerId() || Number(pack.user_id) !== ownerId()) throw new Error('Sign in to the account that downloaded this pack.');
        pack.installed_at ||= new Date().toISOString();
        delete pack.csrf_token;
        pack.pendingCount = (await queueFor(pack.course.slug)).length;
        await writeStore('packs', packKey(pack.course.slug), pack);
        writeJson(metadataKey(pack.course.slug), metadata(pack));
        writeJson(indexKey(), [...new Set([...packIndex(), pack.course.slug])]);
        changed();
    }
    async function queueFor(slug) { return await readStore('queues', packKey(slug)) || []; }
    async function changeQueue(slug, update) {
        const db = await database();
        const key = packKey(slug);
        const events = await new Promise((resolve, reject) => {
            const tx = db.transaction('queues', 'readwrite');
            const table = tx.objectStore('queues');
            const read = table.get(key);
            let next;
            read.onsuccess = () => { next = update(read.result || []); table.put(next, key); };
            tx.oncomplete = () => resolve(next);
            tx.onerror = tx.onabort = () => reject(new Error('Offline progress could not be saved.'));
        });
        const info = storedMetadata(slug);
        if (info) { info.pendingCount = events.length; writeJson(metadataKey(slug), info); }
        changed();
        return events;
    }
    function getPacks() {
        if (!ownerId()) return [];
        return packIndex().map(storedMetadata).filter(pack => pack && Number(pack.user_id) === ownerId());
    }
    function absoluteUrl(url) { return new URL(url, window.location.origin).toString(); }
    function sameOrigin(url) {
        try { return new URL(url, window.location.origin).origin === window.location.origin; }
        catch { return false; }
    }
    function cacheableAsset(url) {
        if (!sameOrigin(url)) return false;
        const path = new URL(url, location.origin).pathname;
        return ['/offline.html', '/offline-learning.html', '/manifest.webmanifest'].includes(path)
            || ['/storage/', '/img/', '/images/', '/icons/', '/js/', '/build/'].some(prefix => path.startsWith(prefix));
    }

    async function cachePackUrls(urls, onProgress) {
        if (!('caches' in window)) {
            throw new Error('This browser does not support offline storage.');
        }

        const cache = await caches.open(PACK_CACHE);
        const requestedUrls = Array.from(new Set(urls.filter(Boolean).map(absoluteUrl)));
        const uniqueUrls = requestedUrls.filter(cacheableAsset);
        const failures = requestedUrls.filter(url => !cacheableAsset(url));
        let cached = 0;

        for (const url of uniqueUrls) {
            try {
                const request = new Request(url, { credentials: 'include' });
                const response = await fetch(request);
                if (response.ok && !response.redirected && !response.headers.get('cache-control')?.includes('no-store') && (!response.headers.get('content-type')?.includes('text/html') || ['/offline.html', '/offline-learning.html'].includes(new URL(url).pathname))) {
                    await cache.put(request, response.clone());
                    cached++;
                } else { failures.push(url); }
            } catch (error) {
                failures.push(url); // Text remains available; report incomplete resources truthfully.
            }

            if (typeof onProgress === 'function') {
                onProgress(cached, uniqueUrls.length);
            }
        }

        return {cached, failures};
    }

    async function installPack(options) {
        const manifestUrl = new URL(options.manifestUrl, window.location.origin);
        const storageLimitMb = Number(options.storageLimitMb || 500);
        const types = options.types && options.types.length ? options.types : ['lessons', 'documents', 'images', 'audio'];

        manifestUrl.searchParams.set('storage_limit_mb', storageLimitMb);
        manifestUrl.searchParams.set('types', types.join(','));

        const response = await fetch(manifestUrl.toString(), {
            credentials: 'include',
            headers: { Accept: 'application/json' },
        });

        if (!response.ok) {
            throw new Error('Could not prepare the offline pack.');
        }

        const manifest = await response.json();
        if (!ownerId() || Number(manifest.user_id) !== ownerId()) throw new Error('Sign in to the account that is downloading this pack.');

        if (Number(manifest.estimated_size_mb || 0) > storageLimitMb) {
            throw new Error(`This pack is ${manifest.estimated_size_mb} MB, above your ${storageLimitMb} MB limit.`);
        }

        if (navigator.storage?.estimate) {
            const estimate = await navigator.storage.estimate();
            const freeBytes = Math.max((estimate.quota || 0) - (estimate.usage || 0), 0);

            if (freeBytes && Number(manifest.estimated_bytes || 0) > freeBytes) {
                throw new Error('This device does not have enough browser storage for the selected pack.');
            }
        }

        const assets = await cachePackUrls(manifest.cache_urls || [], options.onProgress);
        manifest.cached_asset_count = assets.cached;
        manifest.cache_failures = assets.failures;
        manifest.status = assets.failures.length ? 'partial' : 'cached';
        manifest.local_completed_ids = manifest.completed_lesson_ids || [];
        await savePack(manifest);

        return manifest;
    }

    async function removePack(slug) {
        const pack = await storedPack(slug);

        if (pack && 'caches' in window) {
            const cache = await caches.open(PACK_CACHE);
            const others = await Promise.all(getPacks().filter(item => item.course.slug !== slug).map(item => storedPack(item.course.slug)));
            const retained = new Set(others.filter(Boolean).flatMap(item => item.cache_urls || []).filter(sameOrigin).map(absoluteUrl));
            for (const url of pack.cache_urls || []) {
                if (sameOrigin(url) && !retained.has(absoluteUrl(url))) {
                    await cache.delete(new Request(absoluteUrl(url), { credentials: 'include' }));
                }
            }
        }

        await writeStore('packs', packKey(slug), null);
        await writeStore('queues', packKey(slug), null);
        localStorage.removeItem(metadataKey(slug));
        writeJson(indexKey(), packIndex().filter(item => item !== slug));
        changed();
    }

    function openReader(slug) {
        window.location.href = `/offline-learning.html?course=${encodeURIComponent(slug)}`;
    }

    async function queueProgress(slug, event) {
        if (!ownerId() || !(await storedPack(slug))) throw new Error('Download this pack for the signed-in account first.');
        const identity = `${event.type || 'lesson_completed'}:${event.lesson_id}`;
        const eventId = crypto.randomUUID();
        await changeQueue(slug, events => [...events.filter(item => `${item.type || 'lesson_completed'}:${item.lesson_id}` !== identity),
            {...event, event_id: eventId, queued_at: new Date().toISOString()}]);
    }

    async function performSync() {
        if (!navigator.onLine) return {synced: 0, offline: true};
        if (!getPacks().some(pack => pack.pendingCount > 0)) return {synced: 0, pending: 0};
        const response = await fetch('/offline-learning/session', {credentials: 'include', cache: 'no-store', headers: {Accept: 'application/json'}});
        if (!response.ok) throw new Error('Sign in again to sync. Your offline progress remains on this device.');
        const session = await response.json();
        if (Number(session.user_id) !== ownerId()) throw new Error('Sign in to the account that downloaded these packs. Progress has been retained.');
        let synced = 0, pending = 0;
        for (const info of getPacks()) {
            const pack = await storedPack(info.course.slug);
            const queue = (await queueFor(info.course.slug)).slice(0, 50);
            if (!pack || !queue.length || !sameOrigin(pack.sync_url)) continue;
            const reply = await fetch(pack.sync_url, {
                method: 'POST', credentials: 'include', cache: 'no-store',
                headers: {Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': session.csrf_token},
                body: JSON.stringify({owner_id: session.user_id, events: queue}),
            });
            if (!reply.ok) throw new Error(reply.status === 401 || reply.status === 419
                ? 'Your session expired. Sign in again; pending progress has been retained.'
                : 'Progress could not be confirmed. Reconnect and retry; pending progress has been retained.');
            const result = await reply.json();
            if (!Array.isArray(result.accepted_event_ids)) throw new Error('Sync acknowledgement was incomplete. Pending progress has been retained.');
            const accepted = new Set(result.accepted_event_ids);
            const snapshot = new Set(queue.map(event => event.event_id));
            const remaining = await changeQueue(info.course.slug, current => current.filter(event => !(snapshot.has(event.event_id) && accepted.has(event.event_id))));
            synced += accepted.size;
            pending += remaining.length;
            pack.pendingCount = remaining.length;
            pack.status = remaining.length ? 'pending' : 'synced';
            pack.last_synced_at = new Date().toISOString();
            await savePack(pack);
        }
        return {synced, pending};
    }
    function syncQueuedProgress() {
        if (!syncing) syncing = (navigator.locks
            ? navigator.locks.request('bootkode-offline-sync', performSync) : performSync()).finally(() => { syncing = null; });
        return syncing;
    }

    function panel(options) {
        return {
            storageLimitMb: options.storageLimitMb || 500,
            selectedTypes: options.types || ['lessons', 'documents', 'images', 'audio'],
            status: '',
            progressText: '',
            downloading: false,
            installed: Boolean(storedMetadata(options.slug)),
            toggleType(type) {
                this.selectedTypes = this.selectedTypes.includes(type)
                    ? this.selectedTypes.filter((item) => item !== type)
                    : [...this.selectedTypes, type];
            },
            async download() {
                this.downloading = true;
                this.status = 'Preparing offline pack...';
                this.progressText = '';

                try {
                    const pack = await installPack({
                        manifestUrl: options.manifestUrl,
                        storageLimitMb: this.storageLimitMb,
                        types: this.selectedTypes,
                        onProgress: (cached, total) => {
                            this.progressText = `${cached}/${total} assets cached`;
                        },
                    });
                    this.installed = true;
                    this.status = pack.cache_failures?.length ? `Pack saved; ${pack.cache_failures.length} resource(s) need a retry or an online connection.` : `${pack.course.title}: selected materials saved for offline use.`;
                } catch (error) {
                    this.status = error.message || 'Offline pack failed.';
                } finally {
                    this.downloading = false;
                }
            },
            open() {
                openReader(options.slug);
            },
            async sync() {
                this.status = 'Syncing offline progress...';
                try {
                    const result = await syncQueuedProgress();
                    this.status = result.offline ? 'You are still offline. Progress is retained for reconnection.'
                        : (result.pending ? `${result.synced} confirmed; ${result.pending} pending. Complete required online assessments before retrying.` : `${result.synced} item(s) confirmed.`);
                } catch (error) { this.status = error.message; }
            },
            async remove() {
                await removePack(options.slug);
                this.installed = false;
                this.status = 'Local offline pack removed from this browser.';
            },
        };
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    async function readerState(slug) {
        const pack = await storedPack(slug);
        if (!pack) {
            return null;
        }

        pack.local_completed_ids = pack.local_completed_ids || pack.completed_lesson_ids || [];
        return pack;
    }

    async function renderReader() {
        const root = document.getElementById('offline-reader-root');
        if (!root) {
            return;
        }

        const slug = new URLSearchParams(window.location.search).get('course');
        if (!slug && getPacks().length) {
            root.innerHTML = `<main id="offline-lesson-content" tabindex="-1" class="empty"><div><h1>Choose a downloaded course</h1><p>Downloads on this device belong to the last signed-in account. Sign out before sharing your device.</p>${getPacks().map(pack => `<p><a class="online-link" href="/offline-learning.html?course=${encodeURIComponent(pack.course.slug)}">${escapeHtml(pack.course.title)}</a></p>`).join('')}</div></main>`;
            return;
        }
        const pack = await readerState(slug);

        if (!pack) {
            root.innerHTML = `
                <main id="offline-lesson-content" tabindex="-1" class="empty">
                    <div class="mark">BK</div>
                    <h1>No offline pack found</h1>
                    <p>Sign in and download this course for your account, then return here. Older unscoped packs must be downloaded again.</p>
                    <a href="/offline-learning">Back to Offline Learning</a>
                </main>
            `;
            return;
        }

        let activeLessonId = Number(localStorage.getItem(`bootkode:offline:active:${ownerId()}:${slug}`) || 0);
        const firstLesson = pack.sections.flatMap((section) => section.lessons)[0];
        if (!activeLessonId && firstLesson) {
            activeLessonId = Number(firstLesson.id);
        }

        function completedSet() {
            return new Set((pack.local_completed_ids || []).map(Number));
        }

        function allLessons() {
            return pack.sections.flatMap((section) => section.lessons.map((lesson) => ({ ...lesson, sectionTitle: section.title })));
        }

        function renderLessonList() {
            const completed = completedSet();
            return pack.sections.map((section) => `
                <section class="reader-section">
                    <h2>${escapeHtml(section.title)}</h2>
                    ${section.lessons.map((lesson) => `
                        <button class="lesson-link ${Number(lesson.id) === activeLessonId ? 'active' : ''}" aria-current="${Number(lesson.id) === activeLessonId ? 'true' : 'false'}" data-lesson-id="${lesson.id}">
                            <span>${escapeHtml(lesson.title)}</span>
                            ${completed.has(Number(lesson.id)) ? '<i>Done</i>' : ''}
                        </button>
                    `).join('')}
                </section>
            `).join('');
        }

        function resourceHtml(resource) {
            const name = escapeHtml(resource.name || resource.type);
            const url = escapeHtml(resource.url || '#');

            if (resource.type === 'image') {
                return `<figure class="resource-card"><img src="${url}" alt="${name}"><figcaption>${name}</figcaption></figure>`;
            }

            if (resource.type === 'audio') {
                return `<div class="resource-card"><strong>${name}</strong><audio controls src="${url}"></audio></div>`;
            }

            if (resource.type === 'video') {
                return `<div class="resource-card"><strong>${name}</strong><video controls src="${url}"></video></div>`;
            }

            if (resource.type === 'video_link') {
                return `<div class="resource-card"><strong>${name}</strong><p>This linked video needs internet access.</p><a href="${url}" target="_blank" rel="noreferrer">Open online video</a></div>`;
            }

            return `<a class="resource-card link" href="${url}" target="_blank" rel="noreferrer"><strong>${name}</strong><span>Open file</span></a>`;
        }

        function renderActiveLesson() {
            const lesson = allLessons().find((item) => Number(item.id) === activeLessonId) || firstLesson;
            if (!lesson) {
                return '<div class="lesson-panel"><h1>No lessons in this pack</h1></div>';
            }

            const completed = completedSet().has(Number(lesson.id));
            const resources = (lesson.resources || []).map(resourceHtml).join('');

            return `
                <article class="lesson-panel">
                    <div class="lesson-meta">${escapeHtml(lesson.sectionTitle)} · ${escapeHtml(lesson.duration_minutes || 'Self-paced')} min</div>
                    <h1 tabindex="-1">${escapeHtml(lesson.title)}</h1>
                    ${lesson.description ? `<p class="description">${escapeHtml(lesson.description)}</p>` : ''}
                    <div class="lesson-body">${lesson.content || '<p>No offline text was included for this lesson.</p>'}</div>
                    ${resources ? `<h2>Resources</h2><div class="resources">${resources}</div>` : ''}
                    <button class="complete-btn ${completed ? 'done' : ''}" data-complete="${lesson.id}">
                        ${completed ? 'Completed locally' : 'Mark complete offline'}
                    </button>
                </article>
            `;
        }

        async function renderShell() {
            const completed = completedSet().size;
            const total = allLessons().length || 1;
            const pending = (await queueFor(slug)).length;

            root.innerHTML = `
                <div class="reader-app">
                    <aside class="reader-nav">
                        <div class="brand">BK</div>
                        <h2>${escapeHtml(pack.course.title)}</h2>
                        <p>${completed}/${total} lessons complete · ${pending} pending sync</p>
                        <div class="progress"><span style="width: ${Math.round((completed / total) * 100)}%"></span></div>
                        <button class="sync-btn" data-sync>Sync Progress</button>
                        <a class="online-link" href="${escapeHtml(pack.course.online_url || '/offline-learning')}">Open online course</a>
                        <details class="reader-lessons" ${window.innerWidth >= 900 ? 'open' : ''}><summary>Lessons</summary><nav aria-label="Downloaded course lessons">${renderLessonList()}</nav></details>
                    </aside>
                    <main id="offline-lesson-content" tabindex="-1" class="reader-content">${renderActiveLesson()}<p id="offline-sync-status" role="status" aria-live="polite"></p></main>
                </div>
            `;

            root.querySelectorAll('[data-lesson-id]').forEach((button) => {
                button.addEventListener('click', async () => {
                    activeLessonId = Number(button.dataset.lessonId);
                    localStorage.setItem(`bootkode:offline:active:${ownerId()}:${slug}`, activeLessonId);
                    await renderShell();
                    root.querySelector('main h1')?.focus();
                });
            });

            root.querySelectorAll('[data-complete]').forEach((button) => {
                button.addEventListener('click', async () => {
                    const lessonId = Number(button.dataset.complete);
                    try {
                        await queueProgress(slug, {type: 'lesson_completed', lesson_id: lessonId, completed_at: new Date().toISOString(), time_spent_seconds: 0});
                        pack.local_completed_ids = [...new Set([...(pack.local_completed_ids || []), lessonId])];
                        await savePack(pack);
                        await renderShell();
                        if (navigator.onLine) {
                            try {
                                const result = await syncQueuedProgress();
                                document.getElementById('offline-sync-status').textContent = result.pending ? 'Progress saved locally. Required online assessments or pending items still need attention.' : 'Progress confirmed by BootKode.';
                            } catch (error) { document.getElementById('offline-sync-status').textContent = error.message; }
                        } else document.getElementById('offline-sync-status').textContent = 'Saved on this device. Reconnect to confirm progress with BootKode.';
                    } catch (error) { document.getElementById('offline-sync-status').textContent = error.message; }

                });
            });

            root.querySelector('[data-sync]')?.addEventListener('click', async () => {
                try {
                    const result = await syncQueuedProgress();
                    await renderShell();
                    document.getElementById('offline-sync-status').textContent = result.offline ? 'Still offline. Progress is retained.' : `${result.synced} confirmed; ${result.pending || 0} pending.`;
                } catch (error) { document.getElementById('offline-sync-status').textContent = error.message; }
            });
        }

        renderShell();
    }

    window.bootkodeOffline = {
        getPacks,
        installPack,
        removePack,
        openReader,
        queueProgress,
        syncQueuedProgress,
        panel,
    };

    window.addEventListener('online', () => {
        syncQueuedProgress().catch(error => {
            window.showNotification?.(error.message, 'error');
            const status = document.getElementById('offline-sync-status');
            if (status) status.textContent = error.message;
        });
    });

    const safeRender = () => renderReader().catch(error => { const root = document.getElementById('offline-reader-root'); if (root) root.textContent = error.message; });
    document.addEventListener('DOMContentLoaded', safeRender);
    document.addEventListener('livewire:navigated', () => { sessionRevoked = false; ownerId(); safeRender(); });
    window.addEventListener('storage', event => {
        if (event.key !== OWNER_KEY || Number(event.newValue)) return;
        sessionRevoked = true;
        databasePromise?.then(db => db.close());
        databasePromise = null;
        const root = document.getElementById('offline-reader-root');
        if (root) root.textContent = 'This account signed out. Sign in and download the course again to continue offline.';
    });
})();
