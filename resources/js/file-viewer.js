const root = document.getElementById('bootkode-file-viewer');
const metadataNode = document.getElementById('file-viewer-metadata');

if (root && metadataNode) {
    const metadata = JSON.parse(metadataNode.textContent || '{}');
    const stage = root.querySelector('[data-viewer-stage]') || document.getElementById('file-viewer-stage');
    const surface = root.querySelector('[data-viewer-surface]');
    const loading = root.querySelector('[data-viewer-loading]');
    const error = root.querySelector('[data-viewer-error]');
    const errorMessage = root.querySelector('[data-viewer-error-message]');
    const status = root.querySelector('[data-viewer-status]');
    const pagination = root.querySelector('[data-control="pagination"]');
    const zoomControls = root.querySelector('[data-control="zoom"]');
    const searchForm = root.querySelector('[data-viewer-search]');
    const pageInput = root.querySelector('[data-viewer-page]');
    const pageCount = root.querySelector('[data-viewer-page-count]');
    const zoomLabel = root.querySelector('[data-viewer-zoom]');
    const state = { current: 1, total: 1, zoom: 100, fit: 'width', render: null, search: null };

    const announce = message => {
        if (status) status.textContent = message;
    };

    const setReady = () => {
        loading?.setAttribute('hidden', '');
        error?.setAttribute('hidden', '');
        surface?.removeAttribute('hidden');
    };

    const fail = exception => {
        loading?.setAttribute('hidden', '');
        surface?.setAttribute('hidden', '');
        if (errorMessage) errorMessage.textContent = exception?.message || 'The preview could not be loaded.';
        error?.removeAttribute('hidden');
        announce('Preview unavailable.');
    };

    const enableControls = ({ pages = false, zoom = false, search = false } = {}) => {
        if (pagination) pagination.hidden = !pages;
        if (zoomControls) zoomControls.hidden = !zoom;
        if (searchForm) searchForm.hidden = !search;
    };

    const updatePosition = () => {
        if (pageInput) {
            pageInput.value = state.current;
            pageInput.max = state.total;
        }
        if (pageCount) pageCount.textContent = String(state.total);
        if (zoomLabel) zoomLabel.textContent = `${Math.round(state.zoom)}%`;
        root.querySelector('[data-viewer-action="previous"]')?.toggleAttribute('disabled', state.current <= 1);
        root.querySelector('[data-viewer-action="next"]')?.toggleAttribute('disabled', state.current >= state.total);
    };

    const fetchFile = async (mode = 'arrayBuffer') => {
        const response = await fetch(metadata.contentUrl, { credentials: 'same-origin', headers: { Accept: '*/*' } });
        if (!response.ok) throw new Error(response.status === 403 ? 'You no longer have access to this file.' : 'The file could not be loaded.');
        return mode === 'text' ? response.text() : response.arrayBuffer();
    };

    const safeHtml = async html => {
        const { default: DOMPurify } = await import('dompurify');
        return DOMPurify.sanitize(html, {
            USE_PROFILES: { html: true },
            FORBID_TAGS: ['script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'video', 'audio'],
            FORBID_ATTR: ['style', 'srcset'],
            ALLOW_UNKNOWN_PROTOCOLS: false,
        });
    };

    async function renderPdf() {
        const pdfjs = await import('pdfjs-dist/build/pdf.mjs');
        pdfjs.GlobalWorkerOptions.workerSrc = new URL('pdfjs-dist/build/pdf.worker.min.mjs', import.meta.url).toString();
        const pdfDocument = await pdfjs.getDocument({ data: await fetchFile(), isEvalSupported: false }).promise;
        state.total = pdfDocument.numPages;
        enableControls({ pages: true, zoom: true, search: true });

        const renderPage = async number => {
            state.current = Math.max(1, Math.min(state.total, Number(number) || 1));
            state.render?.cancel?.();
            const page = await pdfDocument.getPage(state.current);
            const natural = page.getViewport({ scale: 1 });
            const availableWidth = Math.max(280, stage.clientWidth - 40);
            const availableHeight = Math.max(300, stage.clientHeight - 40);
            const fitted = state.fit === 'page'
                ? Math.min(availableWidth / natural.width, availableHeight / natural.height)
                : availableWidth / natural.width;
            const scale = Math.max(.25, Math.min(4, fitted * (state.zoom / 100)));
            const viewport = page.getViewport({ scale });
            const wrapper = document.createElement('div');
            wrapper.className = 'bk-file-viewer-page';
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d', { alpha: false });
            const outputScale = Math.min(window.devicePixelRatio || 1, 2);
            canvas.width = Math.floor(viewport.width * outputScale);
            canvas.height = Math.floor(viewport.height * outputScale);
            canvas.style.width = `${Math.floor(viewport.width)}px`;
            canvas.style.height = `${Math.floor(viewport.height)}px`;
            wrapper.append(canvas);
            const textLayer = document.createElement('div');
            textLayer.className = 'bk-file-viewer-text-layer';
            textLayer.style.width = `${Math.floor(viewport.width)}px`;
            textLayer.style.height = `${Math.floor(viewport.height)}px`;
            wrapper.append(textLayer);
            surface.replaceChildren(wrapper);
            state.render = page.render({ canvasContext: context, viewport, transform: outputScale === 1 ? null : [outputScale, 0, 0, outputScale, 0, 0] });
            await state.render.promise;
            const textContent = await page.getTextContent();
            await new pdfjs.TextLayer({ textContentSource: textContent, container: textLayer, viewport }).render();
            updatePosition();
            setReady();
            announce(`Page ${state.current} of ${state.total} loaded.`);
        };

        state.go = renderPage;
        state.search = async query => {
            const needle = query.trim().toLocaleLowerCase();
            if (!needle) return;
            const matches = [];
            for (let pageNumber = 1; pageNumber <= pdfDocument.numPages; pageNumber += 1) {
                const page = await pdfDocument.getPage(pageNumber);
                const content = await page.getTextContent();
                if (content.items.map(item => item.str || '').join(' ').toLocaleLowerCase().includes(needle)) matches.push(pageNumber);
            }
            if (matches.length) await renderPage(matches[0]);
            announce(matches.length ? `${matches.length} matching page${matches.length === 1 ? '' : 's'}; showing page ${matches[0]}.` : 'No matches found.');
        };
        await renderPage(1);
    }

    async function renderDocx() {
        const mammoth = await import('mammoth/mammoth.browser.js');
        const result = await mammoth.convertToHtml({ arrayBuffer: await fetchFile() }, {
            includeDefaultStyleMap: true,
            ignoreEmptyParagraphs: true,
            convertImage: mammoth.images.imgElement(image => image.read('base64').then(data => ({ src: `data:${image.contentType};base64,${data}` }))),
        });
        const article = document.createElement('article');
        article.className = 'bk-file-viewer-document';
        article.innerHTML = await safeHtml(result.value);
        surface.replaceChildren(article);
        enableControls({ zoom: true, search: true });
        state.search = query => searchText(article, query);
        state.zoomTarget = article;
        setReady();
        announce(result.messages.length ? 'Document loaded with some formatting limitations.' : 'Document loaded.');
    }

    async function renderText(markdown = false) {
        const text = await fetchFile('text');
        const article = document.createElement('article');
        article.className = 'bk-file-viewer-document';
        if (markdown) {
            const { marked } = await import('marked');
            article.innerHTML = await safeHtml(await marked.parse(text, { gfm: true, breaks: false }));
        } else {
            const pre = document.createElement('pre');
            pre.className = 'whitespace-pre-wrap break-words bg-transparent p-0 text-sm text-slate-900';
            pre.textContent = text;
            article.append(pre);
        }
        surface.replaceChildren(article);
        enableControls({ zoom: true, search: true });
        state.search = query => searchText(article, query);
        state.zoomTarget = article;
        setReady();
        announce('Text document loaded.');
    }

    function searchText(container, query) {
        const needle = query.trim().toLocaleLowerCase();
        if (!needle) return;
        const haystack = container.textContent.toLocaleLowerCase();
        const count = haystack.split(needle).length - 1;
        if (count && window.find) window.find(query, false, false, true, false, false, false);
        announce(count ? `${count} text match${count === 1 ? '' : 'es'} found.` : 'No matches found.');
    }

    async function renderPresentation() {
        const { PptxViewer, RECOMMENDED_ZIP_LIMITS } = await import('@aiden0z/pptx-renderer');
        const host = document.createElement('div');
        host.className = 'bk-file-viewer-page';
        surface.replaceChildren(host);
        const viewer = await PptxViewer.open(await fetchFile(), host, {
            renderMode: 'slide', fitMode: 'contain', zipLimits: RECOMMENDED_ZIP_LIMITS,
            lazySlides: true, lazyMedia: true, externalMedia: 'block', pdfjs: false,
        });
        state.total = viewer.slideCount;
        state.go = async number => {
            state.current = Math.max(1, Math.min(state.total, Number(number) || 1));
            await viewer.goToSlide(state.current - 1, { behavior: 'instant' });
            updatePosition();
            announce(`Slide ${state.current} of ${state.total} loaded.`);
        };
        state.search = async query => {
            const matches = viewer.searchText(query.trim());
            if (matches.length) {
                state.current = matches[0].slideIndex + 1;
                await viewer.goToSlide(matches[0].slideIndex, { behavior: 'instant' });
                await viewer.highlightSearchResult(matches[0]);
                updatePosition();
            }
            announce(matches.length ? `${matches.length} match${matches.length === 1 ? '' : 'es'} found; showing slide ${state.current}.` : 'No matches found.');
        };
        state.presentation = viewer;
        enableControls({ pages: true, zoom: true, search: true });
        updatePosition();
        setReady();
        announce(`Presentation loaded with ${state.total} slides.`);
    }

    async function renderSpreadsheet() {
        const module = await import('xlsx');
        const XLSX = module.default || module;
        const workbook = XLSX.read(await fetchFile(), { type: 'array', cellFormula: false, cellHTML: false, cellStyles: false, dense: true });
        const wrapper = document.createElement('section');
        wrapper.className = 'bk-file-viewer-sheet';
        wrapper.setAttribute('aria-label', 'Spreadsheet preview');
        const tabs = document.createElement('div');
        tabs.className = 'bk-file-viewer-sheet-tabs';
        tabs.setAttribute('role', 'tablist');
        const tableHost = document.createElement('div');
        wrapper.append(tabs, tableHost);
        surface.replaceChildren(wrapper);
        let activeSheet = 0;
        let visibleRows = 250;

        const draw = () => {
            const sheetName = workbook.SheetNames[activeSheet];
            const rows = XLSX.utils.sheet_to_json(workbook.Sheets[sheetName], { header: 1, raw: false, defval: '' });
            const bounded = rows.slice(0, visibleRows).map(row => row.slice(0, 100));
            const width = Math.max(1, ...bounded.map(row => row.length));
            const table = document.createElement('table');
            const thead = document.createElement('thead');
            const headingRow = document.createElement('tr');
            const corner = document.createElement('th');
            corner.className = 'bk-row-heading';
            corner.scope = 'col';
            headingRow.append(corner);
            for (let column = 0; column < width; column += 1) {
                const th = document.createElement('th');
                th.scope = 'col';
                th.textContent = XLSX.utils.encode_col(column);
                headingRow.append(th);
            }
            thead.append(headingRow);
            const tbody = document.createElement('tbody');
            bounded.forEach((row, rowIndex) => {
                const tr = document.createElement('tr');
                const number = document.createElement('th');
                number.className = 'bk-row-heading';
                number.scope = 'row';
                number.textContent = String(rowIndex + 1);
                tr.append(number);
                for (let column = 0; column < width; column += 1) {
                    const td = document.createElement('td');
                    td.textContent = String(row[column] ?? '');
                    td.title = td.textContent;
                    tr.append(td);
                }
                tbody.append(tr);
            });
            table.append(thead, tbody);
            tableHost.replaceChildren(table);
            if (rows.length > visibleRows) {
                const more = document.createElement('button');
                more.type = 'button';
                more.className = 'bk-button bk-button-secondary bk-file-viewer-load-more';
                more.textContent = `Load ${Math.min(250, rows.length - visibleRows)} more rows`;
                more.addEventListener('click', () => { visibleRows += 250; draw(); });
                tableHost.append(more);
            }
            announce(`${sheetName} loaded. Showing ${Math.min(rows.length, visibleRows)} of ${rows.length} rows and at most 100 columns.`);
        };

        workbook.SheetNames.forEach((name, index) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'bk-file-viewer-sheet-tab';
            button.setAttribute('role', 'tab');
            button.setAttribute('aria-selected', String(index === 0));
            button.textContent = name;
            button.addEventListener('click', () => {
                activeSheet = index;
                visibleRows = 250;
                tabs.querySelectorAll('[role="tab"]').forEach((tab, tabIndex) => tab.setAttribute('aria-selected', String(tabIndex === index)));
                draw();
            });
            tabs.append(button);
        });
        draw();
        enableControls({ search: true });
        state.search = query => searchText(tableHost, query);
        setReady();
    }

    async function renderImage() {
        const wrapper = document.createElement('div');
        wrapper.className = 'bk-file-viewer-image-wrap';
        const image = document.createElement('img');
        image.className = 'bk-file-viewer-image';
        image.alt = metadata.name;
        image.src = metadata.contentUrl;
        image.addEventListener('load', () => { setReady(); announce('Image loaded.'); });
        image.addEventListener('error', () => fail(new Error('The image could not be decoded by this browser.')));
        wrapper.append(image);
        surface.replaceChildren(wrapper);
        enableControls({ zoom: true });
        state.zoomTarget = image;
    }

    async function renderMedia(kind) {
        const wrapper = document.createElement('div');
        wrapper.className = 'bk-file-viewer-media';
        const media = document.createElement(kind);
        media.controls = true;
        media.preload = 'metadata';
        media.src = metadata.contentUrl;
        media.setAttribute('controlsList', 'nodownload');
        if (kind === 'video') media.playsInline = true;
        media.addEventListener('loadedmetadata', () => { setReady(); announce(`${kind === 'video' ? 'Video' : 'Audio'} ready.`); });
        media.addEventListener('error', () => fail(new Error(`This browser could not play the ${kind} file.`)));
        wrapper.append(media);
        surface.replaceChildren(wrapper);
    }

    function changeZoom(delta) {
        state.zoom = Math.max(25, Math.min(400, state.zoom + delta));
        if (state.presentation) state.presentation.setZoom(state.zoom);
        else if (state.zoomTarget) state.zoomTarget.style.transform = `scale(${state.zoom / 100})`;
        else state.go?.(state.current);
        updatePosition();
        announce(`Zoom ${state.zoom} percent.`);
    }

    root.addEventListener('click', event => {
        const action = event.target.closest('[data-viewer-action]')?.dataset.viewerAction;
        if (!action) return;
        if (action === 'close') window.parent !== window ? window.parent.postMessage({ type: 'bootkode:close-file-preview' }, window.location.origin) : window.location.assign(metadata.returnUrl);
        if (action === 'fullscreen') (document.fullscreenElement ? document.exitFullscreen() : root.requestFullscreen())?.catch?.(() => announce('Full screen is unavailable.'));
        if (action === 'previous') state.go?.(state.current - 1);
        if (action === 'next') state.go?.(state.current + 1);
        if (action === 'zoom-in') changeZoom(25);
        if (action === 'zoom-out') changeZoom(-25);
        if (action === 'fit-width' || action === 'fit-page') {
            state.fit = action === 'fit-page' ? 'page' : 'width';
            state.zoom = 100;
            if (state.presentation) state.presentation.setFitMode('contain');
            else if (state.zoomTarget) state.zoomTarget.style.transform = 'scale(1)';
            else state.go?.(state.current);
            updatePosition();
        }
    });

    pageInput?.addEventListener('change', () => state.go?.(pageInput.value));
    searchForm?.addEventListener('submit', event => {
        event.preventDefault();
        const query = searchForm.querySelector('input').value;
        if (query.trim()) Promise.resolve(state.search?.(query)).catch(fail);
    });

    const initialize = async () => {
        if (!metadata.previewable) return;
        if (metadata.kind === 'pdf') await renderPdf();
        else if (metadata.kind === 'document') await renderDocx();
        else if (metadata.kind === 'text') await renderText(false);
        else if (metadata.kind === 'markdown') await renderText(true);
        else if (metadata.kind === 'presentation') await renderPresentation();
        else if (metadata.kind === 'spreadsheet') await renderSpreadsheet();
        else if (metadata.kind === 'image') await renderImage();
        else if (metadata.kind === 'audio' || metadata.kind === 'video') await renderMedia(metadata.kind);
        else throw new Error('This file format does not have an installed preview renderer.');
    };

    initialize().catch(fail);
}
