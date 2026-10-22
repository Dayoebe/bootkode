// Status messages are text, including when the source contains user input.
export function showNotification(message, type = 'info') {
    if (typeof message !== 'string' || !message.trim()) return;
    let container = document.getElementById('global-notifications');
    if (!container) {
        container = document.createElement('div');
        container.id = 'global-notifications';
        container.className = 'fixed right-4 top-4 z-[60] w-[calc(100%-2rem)] max-w-md space-y-3';
        container.setAttribute('aria-live', 'polite');
        document.body.append(container);
    }
    const notification = document.createElement('div');
    notification.className = `bk-feedback flex items-start gap-3 shadow-lg ${type === 'error' ? 'bk-feedback-error' : ''}`;
    notification.setAttribute('role', type === 'error' ? 'alert' : 'status');
    const copy = document.createElement('p');
    copy.className = 'min-w-0 flex-1 break-words text-sm';
    copy.textContent = message;
    const close = document.createElement('button');
    close.type = 'button';
    close.className = 'grid h-11 w-11 shrink-0 place-items-center rounded-lg border border-line';
    close.setAttribute('aria-label', 'Dismiss notification');
    close.textContent = '×';
    close.addEventListener('click', () => notification.remove());
    notification.append(copy, close);
    container.append(notification);
    // Errors remain until dismissed; other statuses allow time to read.
    if (type !== 'error') window.setTimeout(() => notification.remove(), 10000);
}

window.showNotification = showNotification;
document.addEventListener('livewire:initialized', () => {
    Livewire.on('notify', (payload) => {
        const params = Array.isArray(payload) ? payload[0] : payload;
        showNotification(typeof params === 'string' ? params : params?.message, params?.type || 'info');
    });
    Livewire.on('delayed-redirect', (params) => {
        const target = new URL(params.url, window.location.origin);
        if (target.origin === window.location.origin) window.setTimeout(() => window.location.assign(target), 2000);
    });
});
