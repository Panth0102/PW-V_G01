<?php
/**
 * SkillSwap Configuration File
 * Centralized configuration for the SkillSwap application
 */

// Local Database Configuration for MAMP
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root'); // MAMP default password
define('DB_NAME', 'skillswap');

// Application Configuration
define('APP_NAME', 'SkillSwap');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost:8888/PW-V_G01'); // Local MAMP URL

// Security Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('PASSWORD_MIN_LENGTH', 6);
define('MAX_LOGIN_ATTEMPTS', 5);

// File Upload Configuration
define('MAX_FILE_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf']);

// Email Configuration (if needed for future features)
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');

// Error Reporting (set to false in production)
define('DEBUG_MODE', true); // Local development mode

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone Configuration
date_default_timezone_set('America/New_York'); // Adjust as needed

// Session Configuration (only set if session not already started)
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
}

?>
