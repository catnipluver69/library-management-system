<?php
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/Student.php';

if (!User::isLoggedIn() || User::getRole() !== 'manager') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = $_POST['student_id'] ?? 0;

    if (empty($studentId)) {
        header('Location: ../views/manager/blacklist.php?error=' . urlencode('Invalid student ID'));
        exit;
    }

    $student = new Student();
    $result = $student->toggleBlacklist($studentId);

    if ($result) {
        header('Location: ../views/manager/blacklist.php?success=' . urlencode('Blacklist status updated'));
    } else {
        header('Location: ../views/manager/blacklist.php?error=' . urlencode('Failed to update blacklist status'));
    }
    exit;
} else {
    header('Location: ../views/manager/blacklist.php');
    exit;
}
