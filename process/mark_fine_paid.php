<?php
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/Fine.php';

if (!User::isLoggedIn() || User::getRole() !== 'manager') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fineId = $_POST['fine_id'] ?? 0;

    if (empty($fineId)) {
        header('Location: ../views/manager/fines.php?error=' . urlencode('Invalid fine ID'));
        exit;
    }

    $fine = new Fine();
    $result = $fine->markAsPaid($fineId);

    if ($result) {
        header('Location: ../views/manager/fines.php?success=' . urlencode('Fine marked as paid'));
    } else {
        header('Location: ../views/manager/fines.php?error=' . urlencode('Failed to UPDATE fine'));
    }
    exit;
} else {
    header('Location: ../views/manager/fines.php');
    exit;
}
