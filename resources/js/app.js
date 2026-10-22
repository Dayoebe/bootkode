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
