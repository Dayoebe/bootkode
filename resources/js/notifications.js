document.addEventListener('livewire:initialized', () => {
    Livewire.on('notify', (params) => {
        const message = typeof params === 'string' ? params : params.message;
        const type = typeof params === 'string' ? 'info' : (params.type || 'info');
        showNotification(message, type);
    });
});

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `px-6 py-3 rounded-lg shadow-lg text-white transition-all duration-300 transform translate-x-full animate__animated animate__fadeInRight
        ${type === 'success' ? 'bg-green-600' : 
          type === 'error' ? 'bg-red-600' : 
          type === 'warning' ? 'bg-yellow-600' : 
          'bg-blue-600'}`;

    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${
                type === 'success' ? 'check' : 
                type === 'error' ? 'exclamation-triangle' : 
                type === 'warning' ? 'exclamation-circle' : 
                'info'
            } mr-2"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

    const container = document.getElementById('global-notifications');
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