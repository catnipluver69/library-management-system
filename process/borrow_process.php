<?php
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/Book.php';
require_once '../classes/BorrowRecord.php';
require_once '../classes/Fine.php';
require_once '../classes/Student.php';

if (!User::isLoggedIn() || User::getRole() !== 'student') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = $_SESSION['user_id'];
    $bookId = $_POST['book_id'] ?? 0;

    if (empty($bookId)) {
        header('Location: ../views/student/search_books.php?error=' . urlencode('Invalid book selection'));
        exit;
    }

    $borrowRecord = new BorrowRecord();
    $result = $borrowRecord->initiateBorrowRequest($studentId, $bookId);

    if ($result['success']) {
        header('Location: ../views/student/dashboard.php?success=' . urlencode($result['message']));
    } else {
        header('Location: ../views/student/search_books.php?error=' . urlencode($result['message']));
    }
    exit;
} else {
    header('Location: ../views/student/search_books.php');
    exit;
}
