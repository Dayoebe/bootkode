import Chart from 'chart.js/auto';

// Requested through Livewire @assets only by views that render charts.
window.Chart = Chart;

window.bootkodeDashboardCharts = {
    hasData(rows, keys) {
        if (!Array.isArray(rows) || rows.length === 0) {
            return false;
        }

        return rows.some((row) => keys.some((key) => {
            const value = Number(row?.[key] ?? 0);

            return Number.isFinite(value) && value !== 0;
        }));
    },

    hasDatasetData(chartData) {
        const datasets = chartData?.datasets ?? [];

        return Array.isArray(datasets) && datasets.some((dataset) => {
            const values = Array.isArray(dataset?.data) ? dataset.data : Object.values(dataset?.data ?? {});

            return values.some((value) => {
                const numericValue = Number(value ?? 0);

                return Number.isFinite(numericValue) && numericValue !== 0;
            });
        });
    },

    showEmpty(canvasOrId, message = 'No chart data yet') {
        const canvas = typeof canvasOrId === 'string' ? document.getElementById(canvasOrId) : canvasOrId;

        if (!canvas) {
            return;
        }

        canvas.style.display = 'none';

        const parent = canvas.parentElement;
        if (!parent) {
            return;
        }

        parent.classList.add('relative');

        let emptyState = parent.querySelector(`[data-chart-empty-for="${canvas.id}"]`);
        if (!emptyState) {
            emptyState = document.createElement('div');
            emptyState.dataset.chartEmptyFor = canvas.id;
            emptyState.className = 'flex h-full min-h-[160px] flex-col items-center justify-center rounded-lg border border-dashed border-themed-primary bg-themed-tertiary px-4 py-6 text-center text-sm text-themed-secondary';

            const icon = document.createElement('i');
            icon.className = 'fas fa-chart-line mb-3 text-2xl text-themed-tertiary';
            icon.setAttribute('aria-hidden', 'true');

            const label = document.createElement('span');
            label.dataset.chartEmptyLabel = '';

            emptyState.append(icon, label);
            parent.appendChild(emptyState);
        }

        emptyState.querySelector('[data-chart-empty-label]').textContent = message;
    },

    shouldRender(canvasId, rows, keys) {
        const canvas = document.getElementById(canvasId);

        if (!canvas) {
            return false;
        }

        if (!window.Chart) {
            this.showEmpty(canvas, 'Chart library could not load');

            return false;
        }

        if (!this.hasData(rows, keys)) {
            this.showEmpty(canvas);

            return false;
        }

        canvas.style.display = '';
        canvas.parentElement?.querySelector(`[data-chart-empty-for="${canvas.id}"]`)?.remove();

        return true;
    },

    shouldRenderDataset(canvasId, chartData) {
        const canvas = document.getElementById(canvasId);

        if (!canvas) {
            return false;
        }

        if (!window.Chart) {
            this.showEmpty(canvas, 'Chart library could not load');

            return false;
        }

        if (!this.hasDatasetData(chartData)) {
            this.showEmpty(canvas);

            return false;
        }

        canvas.style.display = '';
        canvas.parentElement?.querySelector(`[data-chart-empty-for="${canvas.id}"]`)?.remove();

        return true;
    },
};

window.bootkodeInterviewTrendChart = (points) => {
    let chart = null;
    let themeObserver = null;

    return {
        init() {
            this.$nextTick(() => {
                const styles = getComputedStyle(document.documentElement);
                const color = name => `rgb(${styles.getPropertyValue(name).trim()})`;
                chart = new Chart(this.$refs.canvas, {
                    type: 'line',
                    data: { labels: points.map(point => point.date), datasets: [{
                        label: 'Interviews', data: points.map(point => point.count),
                        borderColor: color('--accent-primary'), tension: 0.2,
                    }] },
                    options: {
                        animation: false, responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { ticks: { color: color('--text-primary') }, grid: { color: color('--border-primary') } },
                            y: { beginAtZero: true, ticks: { precision: 0, color: color('--text-primary') }, grid: { color: color('--border-primary') } },
                        },
                    },
                });
                themeObserver = new MutationObserver(() => {
                    chart.data.datasets[0].borderColor = color('--accent-primary');
                    for (const scale of Object.values(chart.options.scales)) {
                        scale.ticks.color = color('--text-primary');
                        scale.grid.color = color('--border-primary');
                    }
                    chart.update('none');
                });
                themeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class', 'data-theme'] });
            });
        },
        destroy() {
            themeObserver?.disconnect();
            chart?.destroy();
        },
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
