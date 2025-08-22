document.addEventListener('livewire:initialized', () => {
    Livewire.on('notify', (params) => {
        const message = typeof params === 'string' ? params : params.message;
        const type = typeof params === 'string' ? 'info' : (params.type || 'info');
        showNotification(message, type);
    });

    // Handle delayed redirect
    Livewire.on('delayed-redirect', (params) => {
        setTimeout(() => {
            window.location.href = params.url;
        }, 2000); // 2 second delay to show notification
    });
});

function showNotification(message, type = 'info') {
    // Create notification container if it doesn't exist
    let container = document.getElementById('global-notifications');
    if (!container) {
        container = document.createElement('div');
        container.id = 'global-notifications';
        container.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(container);
    }

    const notification = document.createElement('div');
    notification.className = `px-6 py-4 rounded-lg shadow-lg text-white transition-all duration-300 transform translate-x-full animate__animated animate__fadeInRight max-w-md
        ${type === 'success' ? 'bg-green-600' : 
          type === 'error' ? 'bg-red-600' : 
          type === 'warning' ? 'bg-yellow-600' : 
          'bg-blue-600'}`;

    notification.innerHTML = `
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-${
                    type === 'success' ? 'check-circle' : 
                    type === 'error' ? 'exclamation-triangle' : 
                    type === 'warning' ? 'exclamation-circle' : 
                    'info-circle'
                } text-xl"></i>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <div class="ml-4 flex-shrink-0">
                <button onclick="this.closest('.animate__animated').remove()" 
                        class="text-white hover:text-gray-200 focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;

    container.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 10);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.classList.add('translate-x-full', 'animate__fadeOutRight');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }
    }, 5000);
}

// Expose function globally for manual use
window.showNotification = showNotification;