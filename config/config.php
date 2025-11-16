<?php

// Security Configuration
define('JWT_SECRET', 'your-secret-key-change-in-production-use-env-variable');
define('JWT_EXPIRATION', 86400); // 24 hours
define('PASSWORD_MIN_LENGTH', 8);

// Application Configuration
define('APP_NAME', 'Budget Tracker');
define('APP_URL', 'http://localhost');

// Error Reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Timezone
date_default_timezone_set('UTC');

// CORS Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

