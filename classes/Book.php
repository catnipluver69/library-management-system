<?php
/**
 * Book Class
 * Handles all book-related operations
 */

class Book {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    /**
     * Get all books
     */
    public function getAllBooks() {
        $stmt = $this->conn->query("SELECT * FROM book ORDER BY title");
        return $stmt->fetchAll();
    }

    /**
     * Get available books
     */
    public function getAvailableBooks() {
        $stmt = $this->conn->query("SELECT * FROM book WHERE availability = 'Available' ORDER BY title");
        return $stmt->fetchAll();
    }

    /**
     * Search books by title or ISBN
     */
    public function searchBooks($searchTerm) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM book WHERE title LIKE ? OR ISBN LIKE ? OR author LIKE ? ORDER BY title"
        );
        $searchParam = '%' . $searchTerm . '%';
        $stmt->execute([$searchParam, $searchParam, $searchParam]);
        return $stmt->fetchAll();
    }

    /**
     * Get book by ID
     */
    public function getBookById($bookId) {
        $stmt = $this->conn->prepare("SELECT * FROM book WHERE book_id = :book_id");
        $stmt->execute(['book_id' => $bookId]);
        return $stmt->fetch();
    }

    /**
     * Check if book is available
     */
    public function isAvailable($bookId) {
        $stmt = $this->conn->prepare("SELECT availability FROM book WHERE book_id = :book_id");
        $stmt->execute(['book_id' => $bookId]);
        $book = $stmt->fetch();
        return $book && $book['availability'] === 'Available';
    }

    /**
     * UPDATE book availability
     */
    public function updateAvailability($bookId, $status) {
        $stmt = $this->conn->prepare("UPDATE book SET availability = :status WHERE book_id = :book_id");
        return $stmt->execute([
            'status' => $status,
            'book_id' => $bookId
        ]);
    }

    /**
     * Add a new book
     */
    public function addBook($title, $author, $isbn) {
        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO book (title, author, ISBN, availability) VALUES (:title, :author, :isbn, 'Available')"
            );
            return $stmt->execute([
                'title' => $title,
                'author' => $author,
                'isbn' => $isbn
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * UPDATE book details
     */
    public function updateBook($bookId, $title, $author, $isbn) {
        try {
            $stmt = $this->conn->prepare(
                "UPDATE book SET title = :title, author = :author, ISBN = :isbn WHERE book_id = :book_id"
            );
            return $stmt->execute([
                'title' => $title,
                'author' => $author,
                'isbn' => $isbn,
                'book_id' => $bookId
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete a book
     */
    public function deleteBook($bookId) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM book WHERE book_id = :book_id");
            return $stmt->execute(['book_id' => $bookId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get book statistics
     */
    public function getStatistics() {
        $stmt = $this->conn->query("
            SELECT 
                COUNT(*) as total_books,
                SUM(CASE WHEN availability = 'Available' THEN 1 ELSE 0 END) as available_books,
                SUM(CASE WHEN availability = 'Borrowed' THEN 1 ELSE 0 END) as borrowed_books
            FROM book
        ");
        return $stmt->fetch();
    }
}
