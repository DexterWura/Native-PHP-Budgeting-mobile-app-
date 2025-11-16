// Configuration file for the app
// This file allows easy configuration of API URL for different environments

// For local development/testing
const API_CONFIG = {
    // Use this for Android emulator (10.0.2.2 maps to host machine's localhost)
    // Use your computer's local IP (e.g., 192.168.1.100) for physical device testing
    // Use your production domain for production builds
    API_URL: window.location.origin + '/api',  // Auto-detect from current location
    
    // Or set manually:
    // API_URL: 'http://10.0.2.2/api',  // Android emulator
    // API_URL: 'http://192.168.1.100/api',  // Physical device (replace with your IP)
    // API_URL: 'https://your-domain.com/api',  // Production
};

// Make it available globally
window.API_CONFIG = API_CONFIG;

