<?php
/**
 * Configuration File
 * Contains all application settings
 */

// Database Configuration
define('DB_HOST', 'sql303.infinityfree.com');
define('DB_NAME', 'if0_40608078_library');
define('DB_USER', 'if0_40608078');
define('DB_PASS', 'TJ0VORin3F');
define('DB_CHARSET', 'utf8mb4');

// Application Configuration
define('APP_NAME', 'Library Management System');
define('APP_URL', 'http://localhost/PHPFOADLIBARARY');

// Session Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour

// Fine Configuration
define('FINE_PER_DAY', 5.00); // Fine amount per day
define('BORROW_DURATION', 14); // Days allowed to borrow

// Paths
define('BASE_PATH', __DIR__);
define('CLASSES_PATH', BASE_PATH . '/classes');
define('VIEWS_PATH', BASE_PATH . '/views');

// Error Reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('UTC');
