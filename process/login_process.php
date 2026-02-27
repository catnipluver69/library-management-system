<?php
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        header('Location: ../login.php?error=' . urlencode('Please fill in all fields'));
        exit;
    }

    $user = new User();
    $result = $user->login($username, $password);

    if ($result) {
        $user->startSession($result);
        User::redirectToDashboard();
    } else {
        header('Location: ../login.php?error=' . urlencode('Invalid username or password'));
        exit;
    }
} else {
    header('Location: ../login.php');
    exit;
}
