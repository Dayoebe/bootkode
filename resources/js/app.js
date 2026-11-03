// Confirmed logout also clears explicit downloads in browsers without Clear-Site-Data.
// Expired sessions do not trigger cleanup: their pending progress must survive sign-in.
function clearSignedOutDownloads() {
    if (document.querySelector('meta[name="bootkode-signed-out"]')?.content !== '1') return;
    const warning = message => {
        const notice = document.createElement('p');
        notice.className = 'bk-feedback bk-feedback-error';
        notice.setAttribute('role', 'alert');
        notice.textContent = message;
        document.body.prepend(notice);
    };
    try {
        localStorage.setItem('bootkode:offline:owner', '0');
        Object.keys(localStorage).filter(key => key.startsWith('bootkode:offline:')).forEach(key => localStorage.removeItem(key));
        if ('indexedDB' in window) {
            const cleanup = indexedDB.deleteDatabase('bootkode-offline-learning');
            cleanup.onerror = () => warning('Device downloads could not be removed. Clear site storage before sharing this device.');
            cleanup.onblocked = () => warning('Close other BootKode tabs to finish removing device downloads.');
        }
        if ('caches' in window) caches.delete('bootkode-offline-packs-v1').catch(() => {
            warning('Browser download cleanup needs attention. Clear site storage before sharing this device.');
        });
    } catch {
        warning('Browser storage could not be cleared. Clear site storage before sharing this device.');
    }
}
clearSignedOutDownloads();
document.addEventListener('livewire:navigated', clearSignedOutDownloads);

const pwaState = {
    installPrompt: null,
    registration: null,
    updateRequested: false,
};

window.bootkodePwa = {
    canInstall: () => Boolean(pwaState.installPrompt),
    hasUpdate: () => Boolean(pwaState.registration?.waiting),
    async install() {
        if (!pwaState.installPrompt) return false;
        await pwaState.installPrompt.prompt();
        const choice = await pwaState.installPrompt.userChoice;
        pwaState.installPrompt = null;
        window.dispatchEvent(new CustomEvent('bootkode:pwa-install-state', {detail: {available: false}}));
        return choice.outcome === 'accepted';
    },
    activateUpdate() {
        if (!pwaState.registration?.waiting) return false;
        pwaState.updateRequested = true;
        pwaState.registration.waiting.postMessage({type: 'SKIP_WAITING'});
        return true;
    },
};

window.addEventListener('beforeinstallprompt', event => {
    event.preventDefault();
    pwaState.installPrompt = event;
    window.dispatchEvent(new CustomEvent('bootkode:pwa-install-state', {detail: {available: true}}));
});

window.addEventListener('appinstalled', () => {
    pwaState.installPrompt = null;
    window.dispatchEvent(new CustomEvent('bootkode:pwa-install-state', {detail: {available: false, installed: true}}));
});

if ('serviceWorker' in navigator) {
    window.addEventListener('load', async () => {
        try {
            const registration = await navigator.serviceWorker.register('/service-worker.js');
            pwaState.registration = registration;

            const announceUpdate = () => window.dispatchEvent(new CustomEvent('bootkode:pwa-update-state', {
                detail: {available: Boolean(registration.waiting)},
            }));

            announceUpdate();
            registration.addEventListener('updatefound', () => {
                const worker = registration.installing;
                worker?.addEventListener('statechange', () => {
                    if (worker.state === 'installed' && navigator.serviceWorker.controller) announceUpdate();
                });
            });
        } catch {
            window.dispatchEvent(new CustomEvent('bootkode:pwa-error'));
        }
    });

    navigator.serviceWorker.addEventListener('controllerchange', () => {
        if (pwaState.updateRequested) window.location.reload();
    });
}

// Check online status
function updateOnlineStatus() {
    const statusElement = document.getElementById('connection-status');
    if (navigator.onLine) {
        if (statusElement) {
            statusElement.innerHTML = '<i class="fas fa-wifi text-green-500"></i> Online';
            statusElement.className = 'text-green-500';
        }
    } else {
        if (statusElement) {
            statusElement.innerHTML = '<i class="fas fa-wifi-slash text-red-500"></i> Offline';
            statusElement.className = 'text-red-500';
        }
        
        // Show offline content notification
        window.Livewire?.dispatch('offline-mode-activated');
    }
}

window.addEventListener('online', updateOnlineStatus);
window.addEventListener('offline', updateOnlineStatus);
updateOnlineStatus();


document.addEventListener('livewire:initialized', () => {
    Livewire.on('bookmarkUpdated', () => window.showNotification?.('Bookmark updated', 'success'));
    Livewire.on('show-login-modal', () => window.showNotification?.('Sign in to save resources', 'info'));
    Livewire.on('download-certificate', (data) => {
        const id = encodeURIComponent(data.certificateId);
        window.location.href = `/certificates/download/${id}`;
    });
});

document.addEventListener('click', (e) => {
    if (e.target.closest('nav button, nav a') && 'vibrate' in navigator) {
        navigator.vibrate(10); // Short pulse
    }
});
