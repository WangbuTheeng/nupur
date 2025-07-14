import './bootstrap';

import Alpine from 'alpinejs';

// Make Alpine available globally before importing components
window.Alpine = Alpine;

console.log('🚀 [APP.JS] Alpine.js loaded and available globally');
console.log('🚀 [APP.JS] Alpine version:', Alpine.version);

// Import other components (navbar component is now registered in app.blade.php)
import './realtime-seat-map';
import './realtime-dashboard';
import './realtime-notifications';

// Start Alpine
Alpine.start();

console.log('🚀 [APP.JS] Alpine.start() called');
