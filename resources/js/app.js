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
