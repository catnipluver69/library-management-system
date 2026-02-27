<?php
/**
 * BorrowRecord Class
 * Handles the complete borrowing logic including blacklist check, fine check, and approvals
 */

class BorrowRecord {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    /**
     * CRITICAL: Initiate borrow request with all validations
     * Returns: ['success' => bool, 'message' => string, 'requiresApproval' => string]
     */
    public function initiateBorrowRequest($studentId, $bookId) {
        try {
            // Step 1: Verify student is not blacklisted
            if ($this->isStudentBlacklisted($studentId)) {
                return [
                    'success' => false,
                    'message' => 'Your account is blacklisted. Please contact the administrator.',
                    'requiresApproval' => null
                ];
            }

            // Step 2: Check if book is available
            $book = new Book();
            if (!$book->isAvailable($bookId)) {
                // Add to waiting list
                $this->addToWaitingList($studentId, $bookId);
                return [
                    'success' => false,
                    'message' => 'Book is currently unavailable. You have been added to the waiting list.',
                    'requiresApproval' => null
                ];
            }

            // Step 3: Check for unpaid fines
            $fine = new Fine();
            if ($fine->hasUnpaidFines($studentId)) {
                // Create pending record that requires manager approval
                $recordId = $this->createPendingRecord($studentId, $bookId, 'manager');
                return [
                    'success' => false,
                    'message' => 'You have unpaid fines. Request sent to Manager for authorization.',
                    'requiresApproval' => 'manager',
                    'record_id' => $recordId
                ];
            }

            // Step 4: No fines - send to librarian for approval
            $recordId = $this->createPendingRecord($studentId, $bookId, 'librarian');
            return [
                'success' => true,
                'message' => 'Request sent to Librarian for approval.',
                'requiresApproval' => 'librarian',
                'record_id' => $recordId
            ];

        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'requiresApproval' => null
            ];
        }
    }

    /**
     * Check if student is blacklisted
     */
    private function isStudentBlacklisted($studentId) {
        $stmt = $this->conn->prepare("SELECT blacklist_status FROM student WHERE student_id = :student_id");
        $stmt->execute(['student_id' => $studentId]);
        $student = $stmt->fetch();
        return $student && $student['blacklist_status'] == 1;
    }

    /**
     * Create a pending borrow record
     */
    private function createPendingRecord($studentId, $bookId, $approverType) {
        $borrowDate = date('Y-m-d');
        $dueDate = date('Y-m-d', strtotime('+' . BORROW_DURATION . ' days'));

        $stmt = $this->conn->prepare("
            INSERT INTO borrow_record 
            (student_id, book_id, borrow_date, due_date, status, manager_approved, librarian_approved) 
            VALUES 
            (:student_id, :book_id, :borrow_date, :due_date, 'Pending', :manager_approved, :librarian_approved)
        ");

        $managerApproved = ($approverType === 'librarian') ? 1 : 0;
        $librarianApproved = 0;

        $stmt->execute([
            'student_id' => $studentId,
            'book_id' => $bookId,
            'borrow_date' => $borrowDate,
            'due_date' => $dueDate,
            'manager_approved' => $managerApproved,
            'librarian_approved' => $librarianApproved
        ]);

        return $this->conn->lastInsertId();
    }

    /**
     * Manager approves/rejects a borrow request
     */
    public function managerApproval($recordId, $approved) {
        if ($approved) {
            $stmt = $this->conn->prepare("
                UPDATE borrow_record 
                SET manager_approved = 1 
                WHERE record_id = :record_id
            ");
            $stmt->execute(['record_id' => $recordId]);
            return ['success' => true, 'message' => 'Request approved. Sent to Librarian for final approval.'];
        } else {
            $stmt = $this->conn->prepare("
                UPDATE borrow_record 
                SET status = 'Rejected' 
                WHERE record_id = :record_id
            ");
            $stmt->execute(['record_id' => $recordId]);
            return ['success' => true, 'message' => 'Request rejected.'];
        }
    }

    /**
     * Librarian approves a borrow request (final step)
     */
    public function librarianApproval($recordId) {
        try {
            $this->conn->beginTransaction();

            // Get record details
            $stmt = $this->conn->prepare("SELECT * FROM borrow_record WHERE record_id = :record_id");
            $stmt->execute(['record_id' => $recordId]);
            $record = $stmt->fetch();

            if (!$record) {
                throw new Exception("Record not found");
            }

            // Update record status
            $stmt = $this->conn->prepare("
                UPDATE borrow_record 
                SET status = 'Approved', librarian_approved = 1 
                WHERE record_id = :record_id
            ");
            $stmt->execute(['record_id' => $recordId]);

            // UPDATE book availability
            $stmt = $this->conn->prepare("
                UPDATE book 
                SET availability = 'Borrowed' 
                WHERE book_id = :book_id
            ");
            $stmt->execute(['book_id' => $record['book_id']]);

            $this->conn->commit();
            return ['success' => true, 'message' => 'Book issued successfully.'];

        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Get pending requests for manager (students with fines)
     */
    public function getPendingManagerApprovals() {
        $stmt = $this->conn->query("
            SELECT br.*, s.name, s.username, b.title, b.author
            FROM borrow_record br
            JOIN student s ON br.student_id = s.student_id
            JOIN book b ON br.book_id = b.book_id
            WHERE br.status = 'Pending' AND br.manager_approved = 0
            ORDER BY br.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Get pending requests for librarian
     */
    public function getPendingLibrarianApprovals() {
        $stmt = $this->conn->query("
            SELECT br.*, s.name, s.username, b.title, b.author
            FROM borrow_record br
            JOIN student s ON br.student_id = s.student_id
            JOIN book b ON br.book_id = b.book_id
            WHERE br.status = 'Pending' AND br.manager_approved = 1 AND br.librarian_approved = 0
            ORDER BY br.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Get borrowed books by student (current active borrows)
     */
    public function getBorrowedBooksByStudent($studentId) {
        $stmt = $this->conn->prepare("
            SELECT br.*, b.title, b.author, b.ISBN 
            FROM borrow_record br
            JOIN book b ON br.book_id = b.book_id
            WHERE br.student_id = :student_id AND br.status IN ('Approved', 'Pending') AND br.return_date IS NULL
            ORDER BY br.due_date
        ");
        $stmt->execute(['student_id' => $studentId]);
        return $stmt->fetchAll();
    }

    /**
     * Get all active borrows (for librarian)
     */
    public function getAllActiveBorrows() {
        $stmt = $this->conn->query("
            SELECT br.*, s.name, s.username, b.title, b.author, b.ISBN
            FROM borrow_record br
            JOIN student s ON br.student_id = s.student_id
            JOIN book b ON br.book_id = b.book_id
            WHERE br.status = 'Approved' AND br.return_date IS NULL
            ORDER BY br.due_date ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Add student to waiting list
     */
    private function addToWaitingList($studentId, $bookId) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO waiting_list (book_id, student_id) 
                VALUES (:book_id, :student_id)
            ");
            return $stmt->execute([
                'book_id' => $bookId,
                'student_id' => $studentId
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get waiting list for a book
     */
    public function getWaitingListForBook($bookId) {
        $stmt = $this->conn->prepare("
            SELECT wl.*, s.name, s.username
            FROM waiting_list wl
            JOIN student s ON wl.student_id = s.student_id
            WHERE wl.book_id = :book_id
            ORDER BY wl.request_date ASC
        ");
        $stmt->execute(['book_id' => $bookId]);
        return $stmt->fetchAll();
    }

    /**
     * Process book return
     */
    public function processReturn($recordId) {
        try {
            $this->conn->beginTransaction();

            // Get record details
            $stmt = $this->conn->prepare("SELECT * FROM borrow_record WHERE record_id = :record_id");
            $stmt->execute(['record_id' => $recordId]);
            $record = $stmt->fetch();

            if (!$record) {
                throw new Exception("Record not found");
            }

            $returnDate = date('Y-m-d');
            $fineAmount = 0;

            // Calculate fine if overdue
            if ($returnDate > $record['due_date']) {
                $daysLate = (strtotime($returnDate) - strtotime($record['due_date'])) / (60 * 60 * 24);
                $fineAmount = $daysLate * FINE_PER_DAY;

                // Create fine record
                $fine = new Fine();
                $fine->createFine($record['student_id'], $fineAmount);
            }

            // Update borrow record
            $stmt = $this->conn->prepare("
                UPDATE borrow_record 
                SET return_date = :return_date, fine_amount = :fine_amount, status = 'Returned'
                WHERE record_id = :record_id
            ");
            $stmt->execute([
                'return_date' => $returnDate,
                'fine_amount' => $fineAmount,
                'record_id' => $recordId
            ]);

            // UPDATE book availability
            $stmt = $this->conn->prepare("UPDATE book SET availability = 'Available' WHERE book_id = :book_id");
            $stmt->execute(['book_id' => $record['book_id']]);

            // Check waiting list
            $waitingList = $this->getWaitingListForBook($record['book_id']);

            $this->conn->commit();

            $message = 'Book returned successfully.';
            if ($fineAmount > 0) {
                $message .= ' Fine of $' . number_format($fineAmount, 2) . ' has been applied.';
            }
            if (!empty($waitingList)) {
                $message .= ' Waiting list has been notified.';
            }

            return ['success' => true, 'message' => $message, 'fine' => $fineAmount];

        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Get borrow history for a student
     */
    public function getBorrowHistory($studentId) {
        $stmt = $this->conn->prepare("
            SELECT br.*, b.title, b.author, b.ISBN
            FROM borrow_record br
            JOIN book b ON br.book_id = b.book_id
            WHERE br.student_id = :student_id
            ORDER BY br.borrow_date DESC
        ");
        $stmt->execute(['student_id' => $studentId]);
        return $stmt->fetchAll();
    }
}
