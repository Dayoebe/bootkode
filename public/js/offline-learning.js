(function () {
    const PACK_INDEX_KEY = 'bootkode:offline:packs';
    const PACK_KEY_PREFIX = 'bootkode:offline:pack:';
    const QUEUE_KEY_PREFIX = 'bootkode:offline:queue:';
    const PACK_CACHE = 'bootkode-offline-packs-v1';

    function readJson(key, fallback) {
        try {
            const value = localStorage.getItem(key);
            return value ? JSON.parse(value) : fallback;
        } catch (error) {
            return fallback;
        }
    }

    function writeJson(key, value) {
        localStorage.setItem(key, JSON.stringify(value));
    }

    function packKey(slug) {
        return PACK_KEY_PREFIX + slug;
    }

    function queueKey(slug) {
        return QUEUE_KEY_PREFIX + slug;
    }

    function absoluteUrl(url) {
        return new URL(url, window.location.origin).toString();
    }

    function sameOrigin(url) {
        try {
            return new URL(url, window.location.origin).origin === window.location.origin;
        } catch (error) {
            return false;
        }
    }

    function csrfToken(pack) {
        return pack?.csrf_token || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    function packIndex() {
        return readJson(PACK_INDEX_KEY, []);
    }

    function savePackIndex(index) {
        writeJson(PACK_INDEX_KEY, Array.from(new Set(index.filter(Boolean))));
        window.dispatchEvent(new CustomEvent('bootkode-offline-packs-updated'));
    }

    function storedPack(slug) {
        return readJson(packKey(slug), null);
    }

    function savePack(pack) {
        pack.installed_at = pack.installed_at || new Date().toISOString();
        writeJson(packKey(pack.course.slug), pack);
        savePackIndex([...packIndex(), pack.course.slug]);
    }

    function queueFor(slug) {
        return readJson(queueKey(slug), []);
    }

    function saveQueue(slug, events) {
        writeJson(queueKey(slug), events);
        const pack = storedPack(slug);
        if (pack) {
            pack.pendingCount = events.length;
            writeJson(packKey(slug), pack);
        }
        window.dispatchEvent(new CustomEvent('bootkode-offline-packs-updated'));
    }

    function getPacks() {
        return packIndex()
            .map((slug) => storedPack(slug))
            .filter(Boolean)
            .map((pack) => ({
                ...pack,
                pendingCount: queueFor(pack.course.slug).length,
            }));
    }

    async function cachePackUrls(urls, onProgress) {
        if (!('caches' in window)) {
            throw new Error('This browser does not support offline storage.');
        }

        const cache = await caches.open(PACK_CACHE);
        const uniqueUrls = Array.from(new Set(urls.filter(Boolean).map(absoluteUrl))).filter(sameOrigin);
        let cached = 0;

        for (const url of uniqueUrls) {
            try {
                const request = new Request(url, { credentials: 'include' });
                const response = await fetch(request);
                if (response.ok) {
                    await cache.put(request, response.clone());
                    cached++;
                }
            } catch (error) {
                // Keep going. One missing PDF should not block the whole course pack.
            }

            if (typeof onProgress === 'function') {
                onProgress(cached, uniqueUrls.length);
            }
        }

        return cached;
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

        manifest.cached_asset_count = await cachePackUrls(manifest.cache_urls || [], options.onProgress);
        manifest.status = 'cached';
        manifest.local_completed_ids = manifest.completed_lesson_ids || [];
        savePack(manifest);

        return manifest;
    }

    async function removePack(slug) {
        const pack = storedPack(slug);

        if (pack && 'caches' in window) {
            const cache = await caches.open(PACK_CACHE);
            for (const url of pack.cache_urls || []) {
                if (sameOrigin(url)) {
                    await cache.delete(new Request(absoluteUrl(url), { credentials: 'include' }));
                }
            }
        }

        localStorage.removeItem(packKey(slug));
        localStorage.removeItem(queueKey(slug));
        savePackIndex(packIndex().filter((item) => item !== slug));
    }

    function openReader(slug) {
        window.location.href = `/offline-learning.html?course=${encodeURIComponent(slug)}`;
    }

    function queueProgress(slug, event) {
        const events = queueFor(slug);
        const eventKey = `${event.type || 'lesson_completed'}:${event.lesson_id}`;
        const filtered = events.filter((item) => `${item.type || 'lesson_completed'}:${item.lesson_id}` !== eventKey);
        filtered.push({
            ...event,
            queued_at: new Date().toISOString(),
        });
        saveQueue(slug, filtered);
    }

    async function syncQueuedProgress() {
        if (!navigator.onLine) {
            return { synced: 0, offline: true };
        }

        let synced = 0;

        for (const pack of getPacks()) {
            const queue = queueFor(pack.course.slug);
            if (!queue.length || !pack.sync_url) {
                continue;
            }

            const response = await fetch(pack.sync_url, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(pack),
                },
                body: JSON.stringify({ events: queue }),
            });

            if (response.ok) {
                const result = await response.json();
                synced += Number(result.synced || queue.length);
                saveQueue(pack.course.slug, []);
                pack.status = 'synced';
                pack.last_synced_at = new Date().toISOString();
                writeJson(packKey(pack.course.slug), pack);
            }
        }

        window.dispatchEvent(new CustomEvent('bootkode-offline-packs-updated'));
        return { synced };
    }

    function panel(options) {
        return {
            storageLimitMb: options.storageLimitMb || 500,
            selectedTypes: options.types || ['lessons', 'documents', 'images', 'audio'],
            status: '',
            progressText: '',
            downloading: false,
            installed: Boolean(storedPack(options.slug)),
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
                    this.status = `${pack.course.title} is ready offline.`;
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
                const result = await syncQueuedProgress();
                this.status = result.offline
                    ? 'You are still offline. Progress will sync when you reconnect.'
                    : (result.synced > 0 ? `${result.synced} item(s) synced.` : 'No pending progress to sync.');
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

    function readerState(slug) {
        const pack = storedPack(slug);
        if (!pack) {
            return null;
        }

        pack.local_completed_ids = pack.local_completed_ids || pack.completed_lesson_ids || [];
        return pack;
    }

    function renderReader() {
        const root = document.getElementById('offline-reader-root');
        if (!root) {
            return;
        }

        const slug = new URLSearchParams(window.location.search).get('course');
        const pack = readerState(slug);

        if (!pack) {
            root.innerHTML = `
                <main class="empty">
                    <div class="mark">BK</div>
                    <h1>No offline pack found</h1>
                    <p>Open the online course once, download its offline learning pack, then return here.</p>
                    <a href="/offline-learning">Back to Offline Learning</a>
                </main>
            `;
            return;
        }

        let activeLessonId = Number(localStorage.getItem(`bootkode:offline:active:${slug}`) || 0);
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
                        <button class="lesson-link ${Number(lesson.id) === activeLessonId ? 'active' : ''}" data-lesson-id="${lesson.id}">
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
                    <h1>${escapeHtml(lesson.title)}</h1>
                    ${lesson.description ? `<p class="description">${escapeHtml(lesson.description)}</p>` : ''}
                    <div class="lesson-body">${lesson.content || '<p>No offline text was included for this lesson.</p>'}</div>
                    ${resources ? `<h2>Resources</h2><div class="resources">${resources}</div>` : ''}
                    <button class="complete-btn ${completed ? 'done' : ''}" data-complete="${lesson.id}">
                        ${completed ? 'Completed locally' : 'Mark complete offline'}
                    </button>
                </article>
            `;
        }

        function renderShell() {
            const completed = completedSet().size;
            const total = allLessons().length || 1;
            const pending = queueFor(slug).length;

            root.innerHTML = `
                <div class="reader-app">
                    <aside class="reader-nav">
                        <div class="brand">BK</div>
                        <h1>${escapeHtml(pack.course.title)}</h1>
                        <p>${completed}/${total} lessons complete · ${pending} pending sync</p>
                        <div class="progress"><span style="width: ${Math.round((completed / total) * 100)}%"></span></div>
                        <button class="sync-btn" data-sync>Sync Progress</button>
                        <a class="online-link" href="${escapeHtml(pack.course.online_url || '/offline-learning')}">Open online course</a>
                        <nav>${renderLessonList()}</nav>
                    </aside>
                    <main class="reader-content">${renderActiveLesson()}</main>
                </div>
            `;

            root.querySelectorAll('[data-lesson-id]').forEach((button) => {
                button.addEventListener('click', () => {
                    activeLessonId = Number(button.dataset.lessonId);
                    localStorage.setItem(`bootkode:offline:active:${slug}`, activeLessonId);
                    renderShell();
                });
            });

            root.querySelectorAll('[data-complete]').forEach((button) => {
                button.addEventListener('click', () => {
                    const lessonId = Number(button.dataset.complete);
                    pack.local_completed_ids = Array.from(new Set([...(pack.local_completed_ids || []), lessonId]));
                    writeJson(packKey(slug), pack);
                    queueProgress(slug, {
                        type: 'lesson_completed',
                        lesson_id: lessonId,
                        completed_at: new Date().toISOString(),
                        time_spent_seconds: 0,
                    });
                    renderShell();
                    if (navigator.onLine) {
                        syncQueuedProgress().then(renderShell).catch(() => {});
                    }
                });
            });

            root.querySelector('[data-sync]')?.addEventListener('click', async () => {
                await syncQueuedProgress();
                renderShell();
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
        syncQueuedProgress().catch(() => {});
    });

    document.addEventListener('DOMContentLoaded', renderReader);
})();
