Alpine.plugin(persist);
Alpine.plugin(collapse);

import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import collapse from '@alpinejs/collapse';


// window.Alpine = Alpine;
// Alpine.start();


document.addEventListener('DOMContentLoaded', function() {
    // Listen for bookmark events
    Livewire.on('bookmarkUpdated', () => {
        // You could update counters or other UI elements here
        alert('Bookmark was updated');
    });
    
    // For the login modal trigger
    Livewire.on('show-login-modal', () => {
        // Show your login modal
        alert('Please login to save resources');
        // Or trigger your auth modal component
        // Livewire.emit('openModal', 'auth.login');
    });
});

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
        Livewire.dispatch('offline-mode-activated');
    }
}

window.addEventListener('online', updateOnlineStatus);
window.addEventListener('offline', updateOnlineStatus);
updateOnlineStatus();

// import EditorJS from '@editorjs/editorjs';


// const editor = new EditorJS();

// Initialize EditorJS
// import EditorJS from '@editorjs/editorjs';
// import Header from '@editorjs/header';
// import List from '@editorjs/list';
// import ImageTool from '@editorjs/image';
// import Delimiter from '@editorjs/delimiter';
// import Table from '@editorjs/table';

// window.EditorJS = EditorJS;
// window.EditorJSHeader = Header;
// window.EditorJSList = List;
// window.EditorJSImage = ImageTool;
// window.EditorJSDelimiter = Delimiter;
// window.EditorJSTable = Table;


// Handle certificate downloads from Livewire events
document.addEventListener('DOMContentLoaded', function() {
    Livewire.on('download-certificate', (data) => {
        // This will trigger the download method in the MyCertificates component
        window.location.href = `/certificates/download/${data.certificateId}`;
    });
});

// QR Code Scanner
import { Html5QrcodeScanner } from 'html5-qrcode';

window.Html5QrcodeScanner = Html5QrcodeScanner;