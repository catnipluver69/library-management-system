<?php
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';

User::logout();
header('Location: ../login.php?success=' . urlencode('Logged out successfully'));
exit;
