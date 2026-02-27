<?php
/**
 * Library Management System - Main Entry Point
 * Redirects users to appropriate pages based on login status
 */

require_once 'config/config.php';
require_once 'classes/Database.php';
require_once 'classes/User.php';

// Check if user is already logged in
if (User::isLoggedIn()) {
    User::redirectToDashboard();
} else {
    header('Location: login.php');
    exit;
}
