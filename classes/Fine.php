<?php
/**
 * Fine Class
 * Handles fine management operations
 */

class Fine {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    /**
     * Check if student has unpaid fines
     */
    public function hasUnpaidFines($studentId) {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as fine_count 
            FROM fine 
            WHERE student_id = :student_id AND status = 'Unpaid'
        ");
        $stmt->execute(['student_id' => $studentId]);
        $result = $stmt->fetch();
        return $result['fine_count'] > 0;
    }

    /**
     * Get total unpaid fines for a student
     */
    public function getTotalUnpaidFines($studentId) {
        $stmt = $this->conn->prepare("
            SELECT COALESCE(SUM(amount), 0) as total 
            FROM fine 
            WHERE student_id = :student_id AND status = 'Unpaid'
        ");
        $stmt->execute(['student_id' => $studentId]);
        $result = $stmt->fetch();
        return $result['total'];
    }

    /**
     * Get all fines for a student
     */
    public function getStudentFines($studentId) {
        $stmt = $this->conn->prepare("
            SELECT * FROM fine 
            WHERE student_id = :student_id 
            ORDER BY created_at DESC
        ");
        $stmt->execute(['student_id' => $studentId]);
        return $stmt->fetchAll();
    }

    /**
     * Get all unpaid fines (for manager)
     */
    public function getAllUnpaidFines() {
        $stmt = $this->conn->query("
            SELECT f.*, s.name, s.username
            FROM fine f
            JOIN student s ON f.student_id = s.student_id
            WHERE f.status = 'Unpaid'
            ORDER BY f.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Create a new fine
     */
    public function createFine($studentId, $amount) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO fine (student_id, amount, status) 
                VALUES (:student_id, :amount, 'Unpaid')
            ");
            return $stmt->execute([
                'student_id' => $studentId,
                'amount' => $amount
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Mark fine as paid
     */
    public function markAsPaid($fineId) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE fine 
                SET status = 'Paid', paid_at = NOW() 
                WHERE fine_id = :fine_id
            ");
            return $stmt->execute(['fine_id' => $fineId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get fine statistics
     */
    public function getStatistics() {
        $stmt = $this->conn->query("
            SELECT 
                COUNT(*) as total_fines,
                SUM(CASE WHEN status = 'Unpaid' THEN 1 ELSE 0 END) as unpaid_count,
                SUM(CASE WHEN status = 'Paid' THEN 1 ELSE 0 END) as paid_count,
                COALESCE(SUM(CASE WHEN status = 'Unpaid' THEN amount ELSE 0 END), 0) as total_unpaid_amount,
                COALESCE(SUM(CASE WHEN status = 'Paid' THEN amount ELSE 0 END), 0) as total_paid_amount
            FROM fine
        ");
        return $stmt->fetch();
    }
}
