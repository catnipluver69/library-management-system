<?php
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/BorrowRecord.php';

if (!User::isLoggedIn() || User::getRole() !== 'manager') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recordId = $_POST['record_id'] ?? 0;
    $action = $_POST['action'] ?? '';

    if (empty($recordId) || empty($action)) {
        header('Location: ../views/manager/approvals.php?error=' . urlencode('Invalid request'));
        exit;
    }

    $borrowRecord = new BorrowRecord();
    $approved = ($action === 'approve');
    $result = $borrowRecord->managerApproval($recordId, $approved);

    if ($result['success']) {
        header('Location: ../views/manager/approvals.php?success=' . urlencode($result['message']));
    } else {
        header('Location: ../views/manager/approvals.php?error=' . urlencode($result['message']));
    }
    exit;
} else {
    header('Location: ../views/manager/approvals.php');
    exit;
}
