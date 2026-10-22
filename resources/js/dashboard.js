import Chart from 'chart.js/auto';
import 'trix';
import 'trix/dist/trix.css';

// Dashboard charts and the existing course/blog editors share this entry point.
window.Chart = Chart;

// Retain focus when a recorded-mark action replaces its own form or control.
window.addEventListener('bootkode-assessment-focus', (event) => {
    requestAnimationFrame(() => document.getElementById(event.detail?.id)?.focus());
});

// Available before Livewire mounts a newly selected lesson editor.
window.lessonEditorManager = function () {
    return {
        activeTab: 'content',
        autoSaveStatus: 'saved',
        showAutoSaveStatus: false,
        isSaving: false,
        hasUnsavedContent: false,
        titleChanged: false,
        slugChanged: false,
        descriptionChanged: false,
        contentChanged: false,
        autoSaveTimeout: null,
        trixInitialized: false,
        lastSavedContent: '',
        slugGenerationTimeout: null,
        slugManuallyEdited: false,
        trixEditor: null,
        debounceTimer: null,
        cleanupListeners: [],
        beforeUnloadHandler: null,

        init() {
            this.setupEventListeners();
            this.$nextTick(() => {
                this.initializeTrixEditor();
            });
        },

        setupEventListeners() {
            // Listen for Livewire events
            this.cleanupListeners.push(Livewire.on('lesson-autosaved', () => this.handleSuccessfulSave()));

            this.cleanupListeners.push(Livewire.on('lesson-saved', () => this.handleSuccessfulSave()));

            // Warn before leaving with unsaved changes
            this.beforeUnloadHandler = (e) => {
                if (this.hasUnsavedContent) {
                    e.preventDefault();
                    e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                }
            };
            window.addEventListener('beforeunload', this.beforeUnloadHandler);
        },

        destroy() {
            [this.autoSaveTimeout, this.slugGenerationTimeout, this.debounceTimer].forEach(clearTimeout);
            this.cleanupListeners.forEach(cleanup => cleanup());
            window.removeEventListener('beforeunload', this.beforeUnloadHandler);
        },

        initializeTrixEditor() {
            const editor = this.$refs.trixEditor;
            if (!editor || this.trixInitialized) return;

            this.trixEditor = editor;
            this.trixInitialized = true;
            this.lastSavedContent = editor.value || '';

            // Prevent file attachments
            editor.addEventListener('trix-file-accept', (e) => {
                e.preventDefault();
                alert('File uploads are not supported in the editor. Use the Media tab instead.');
            });

            // Handle content changes with debouncing
            editor.addEventListener('trix-change', (e) => {
                this.handleTrixChange(e);
            });

        },

        // CRITICAL FIX: Handle Trix changes with proper debouncing
        handleTrixChange(event) {
            const newContent = event.target.value;

            // Mark as changed immediately for UI feedback
            if (newContent !== this.lastSavedContent) {
                this.markFieldChanged('content');
            }

            // Debounce the Livewire update to prevent excessive calls
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                // Update Livewire property without triggering re-render
                this.$wire.$set('content', newContent, false);
            }, 500); // 500ms debounce
        },

        switchTab(tab) {
            if (!this.isSaving) {
                this.activeTab = tab;
            }
        },

        markFieldChanged(field) {
            this.hasUnsavedContent = true;
            this[field + 'Changed'] = true;
            this.scheduleAutoSave();
        },

        scheduleAutoSave() {
            clearTimeout(this.autoSaveTimeout);
            this.autoSaveTimeout = setTimeout(() => {
                this.performAutoSave();
            }, 5000); // Auto-save after 5 seconds of inactivity
        },

        scheduleSlugGeneration() {
            if (this.slugManuallyEdited) return;

            clearTimeout(this.slugGenerationTimeout);
            this.slugGenerationTimeout = setTimeout(() => {
                this.generateSlugFromTitle();
            }, 1500);
        },

        async generateSlugFromTitle() {
            if (this.slugManuallyEdited) return;

            try {
                await this.$wire.call('generateSlug');
                this.slugChanged = true;
            } catch (error) {
                console.error('Slug generation failed:', error);
            }
        },

        async manualSlugGeneration() {
            try {
                await this.$wire.call('generateSlug');
                this.slugManuallyEdited = false;
                this.slugChanged = true;
            } catch (error) {
                console.error('Manual slug generation failed:', error);
            }
        },

        async performAutoSave() {
            if (this.isSaving || !this.hasUnsavedContent) return;

            this.autoSaveStatus = 'saving';
            this.showAutoSaveStatus = true;

            try {
                // Get current Trix content
                if (this.trixEditor) {
                    const currentContent = this.trixEditor.value;
                    // Set content silently
                    await this.$wire.set('content', currentContent, false);
                }

                const result = await this.$wire.call('autoSave');

                if (result && result.success) {
                    this.handleSuccessfulSave();
                    if (this.trixEditor) {
                        this.lastSavedContent = this.trixEditor.value;
                    }
                } else {
                    this.autoSaveStatus = 'error';
                    setTimeout(() => this.showAutoSaveStatus = false, 3000);
                }
            } catch (error) {
                console.error('Auto-save error:', error);
                this.autoSaveStatus = 'error';
                setTimeout(() => this.showAutoSaveStatus = false, 3000);
            }
        },

        async performQuickSave() {
            if (this.isSaving) return;

            this.isSaving = true;
            this.autoSaveStatus = 'saving';
            this.showAutoSaveStatus = true;

            try {
                if (this.trixEditor) {
                    const currentContent = this.trixEditor.value;
                    await this.$wire.set('content', currentContent, false);
                }

                const result = await this.$wire.call('quickSave');

                if (result && result.success) {
                    this.handleSuccessfulSave();
                    if (this.trixEditor) {
                        this.lastSavedContent = this.trixEditor.value;
                    }
                } else {
                    this.autoSaveStatus = 'error';
                    setTimeout(() => this.showAutoSaveStatus = false, 3000);
                }
            } catch (error) {
                console.error('Quick save error:', error);
                this.autoSaveStatus = 'error';
                setTimeout(() => this.showAutoSaveStatus = false, 3000);
            } finally {
                this.isSaving = false;
            }
        },

        handleSuccessfulSave() {
            this.autoSaveStatus = 'saved';
            this.hasUnsavedContent = false;
            this.titleChanged = false;
            this.slugChanged = false;
            this.descriptionChanged = false;
            this.contentChanged = false;

            setTimeout(() => {
                this.showAutoSaveStatus = false;
            }, 2000);
        },

        async retrySave() {
            await this.performQuickSave();
        },

        insertTemplate(type) {
            const templates = {
                intro: '<h2>Introduction</h2><p>Welcome to this lesson. In this section, we will explore...</p>',
                objectives: '<h2>Learning Objectives</h2><ul><li>Understand the key concepts</li><li>Apply the knowledge practically</li><li>Master the fundamentals</li></ul>'
            };

            if (this.trixEditor && templates[type]) {
                this.trixEditor.editor.insertHTML(templates[type]);
                this.markFieldChanged('content');
            }
        }
    };
};

window.learningActivityChart = function (activity) {
    let chart;
    return {
        init() {
            this.$nextTick(() => {
                const style = getComputedStyle(document.documentElement);
                const accent = `rgb(${style.getPropertyValue('--accent-primary').trim()})`;
                const ink = `rgb(${style.getPropertyValue('--text-secondary').trim()})`;
                chart = new Chart(this.$refs.canvas, {
                    type: 'line',
                    data: { labels: activity.map(day => day.date), datasets: [
                        { label: 'Completed lessons', data: activity.map(day => day.lessons), borderColor: accent, yAxisID: 'lessons' },
                        { label: 'Recorded session minutes', data: activity.map(day => day.minutes), borderColor: ink, yAxisID: 'minutes' },
                    ] },
                    options: { responsive: true, maintainAspectRatio: false, animation: false,
                        plugins: { legend: { labels: { color: ink } } },
                        scales: { x: { ticks: { color: ink, maxTicksLimit: 7 } }, lessons: { min: 0, ticks: { precision: 0, color: ink } }, minutes: { min: 0, position: 'right', ticks: { color: ink }, grid: { drawOnChartArea: false } } },
                    },
                });
            });
        },
        destroy() { chart?.destroy(); },
    };
};

// Livewire can replace a moved row; keep keyboard focus on that item's control.
window.addEventListener('bootkode-list-moved', (event) => {
    const { list, item, direction } = event.detail;
    requestAnimationFrame(() => {
        const root = [...document.querySelectorAll('[data-move-list]')]
            .find(element => element.dataset.moveList === String(list));
        if (!root) return;
        const controls = [...root.querySelectorAll('button[data-move-key]')]
            .filter(button => button.dataset.moveKey === String(item) && !button.disabled);
        (controls.find(button => button.dataset.moveDirection === direction) ?? controls[0])?.focus();
    });
});
