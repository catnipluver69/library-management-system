<?php
/**
 * Student Class
 * Handles student-specific operations
 */

class Student {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    /**
     * Get student details by ID
     */
    public function getStudentById($studentId) {
        $stmt = $this->conn->prepare("SELECT * FROM student WHERE student_id = :student_id");
        $stmt->execute(['student_id' => $studentId]);
        return $stmt->fetch();
    }

    /**
     * Get all students
     */
    public function getAllStudents() {
        $stmt = $this->conn->query("SELECT * FROM student ORDER BY name");
        return $stmt->fetchAll();
    }

    /**
     * Toggle blacklist status
     */
    public function toggleBlacklist($studentId) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE student 
                SET blacklist_status = NOT blacklist_status 
                WHERE student_id = :student_id
            ");
            return $stmt->execute(['student_id' => $studentId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Set blacklist status
     */
    public function setBlacklistStatus($studentId, $status) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE student 
                SET blacklist_status = :status 
                WHERE student_id = :student_id
            ");
            return $stmt->execute([
                'status' => $status,
                'student_id' => $studentId
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get blacklisted students
     */
    public function getBlacklistedStudents() {
        $stmt = $this->conn->query("
            SELECT * FROM student 
            WHERE blacklist_status = 1 
            ORDER BY name
        ");
        return $stmt->fetchAll();
    }

    /**
     * Get student's waiting list entries
     */
    public function getWaitingList($studentId) {
        $stmt = $this->conn->prepare("
            SELECT wl.*, b.title, b.author, b.ISBN
            FROM waiting_list wl
            JOIN book b ON wl.book_id = b.book_id
            WHERE wl.student_id = :student_id
            ORDER BY wl.request_date ASC
        ");
        $stmt->execute(['student_id' => $studentId]);
        return $stmt->fetchAll();
    }

    /**
     * Get student statistics
     */
    public function getStatistics() {
        $stmt = $this->conn->query("
            SELECT 
                COUNT(*) as total_students,
                SUM(CASE WHEN blacklist_status = 1 THEN 1 ELSE 0 END) as blacklisted_count
            FROM student
        ");
        return $stmt->fetch();
    }
}
