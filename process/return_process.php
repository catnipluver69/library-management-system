<?php
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/BorrowRecord.php';

if (!User::isLoggedIn() || User::getRole() !== 'librarian') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recordId = $_POST['record_id'] ?? 0;

    if (empty($recordId)) {
        header('Location: ../views/librarian/returns.php?error=' . urlencode('Invalid record'));
        exit;
    }

    $borrowRecord = new BorrowRecord();
    $result = $borrowRecord->processReturn($recordId);

    if ($result['success']) {
        header('Location: ../views/librarian/returns.php?success=' . urlencode($result['message']));
    } else {
        header('Location: ../views/librarian/returns.php?error=' . urlencode($result['message']));
    }
    exit;
} else {
    header('Location: ../views/librarian/returns.php');
    exit;
}
