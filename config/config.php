<?php
/**
 * SkillSwap Configuration File
 * Centralized configuration for the SkillSwap application
 */

// Database Configuration - Works for both local and Heroku
// Heroku will use environment variables, local can use defaults
$cleardb_url = parse_url(getenv("CLEARDB_DATABASE_URL") ?: "mysql://root:@localhost/skillswap");

define('DB_HOST', $cleardb_url["host"] ?? 'localhost');
define('DB_USERNAME', $cleardb_url["user"] ?? 'root');
define('DB_PASSWORD', $cleardb_url["pass"] ?? '');
define('DB_NAME', substr($cleardb_url["path"], 1) ?? 'skillswap');

// Application Configuration
define('APP_NAME', 'SkillSwap');
define('APP_VERSION', '1.0.0');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost'); // Will be set by Heroku

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
define('DEBUG_MODE', getenv('DEBUG_MODE') === 'true'); // Set via Heroku config vars

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
