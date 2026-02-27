<?php
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/Book.php';

if (!User::isLoggedIn() || User::getRole() !== 'librarian') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookId = $_POST['book_id'] ?? 0;
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $isbn = trim($_POST['isbn'] ?? '');

    if (empty($bookId) || empty($title) || empty($author) || empty($isbn)) {
        header('Location: ../views/librarian/inventory.php?error=' . urlencode('All fields are required'));
        exit;
    }

    $book = new Book();
    $result = $book->updateBook($bookId, $title, $author, $isbn);

    if ($result) {
        header('Location: ../views/librarian/inventory.php?success=' . urlencode('Book updated successfully'));
    } else {
        header('Location: ../views/librarian/inventory.php?error=' . urlencode('Failed to UPDATE book'));
    }
    exit;
} else {
    header('Location: ../views/librarian/inventory.php');
    exit;
}
