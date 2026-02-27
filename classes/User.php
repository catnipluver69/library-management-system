<?php
/**
 * User Authentication Class
 * Handles login, logout, and session management for all user types
 */

class User {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    /**
     * Login user based on credentials
     * @param string $username
     * @param string $password
     * @return array|bool Returns user data and role or false
     */
    public function login($username, $password) {
        // Check in Manager table
        $manager = $this->checkManager($username, $password);
        if ($manager) {
            return [
                'role' => 'manager',
                'user_id' => $manager['manager_id'],
                'username' => $manager['username']
            ];
        }

        // Check in Librarian table
        $librarian = $this->checkLibrarian($username, $password);
        if ($librarian) {
            return [
                'role' => 'librarian',
                'user_id' => $librarian['librarian_id'],
                'username' => $librarian['username']
            ];
        }

        // Check in Student table
        $student = $this->checkStudent($username, $password);
        if ($student) {
            return [
                'role' => 'student',
                'user_id' => $student['student_id'],
                'username' => $student['username'],
                'name' => $student['name'],
                'blacklist_status' => $student['blacklist_status']
            ];
        }

        return false;
    }

    /**
     * Check manager credentials
     */
    private function checkManager($username, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM manager WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $manager = $stmt->fetch();

        if ($manager && $password === $manager['password']) {
            return $manager;
        }
        return false;
    }

    /**
     * Check librarian credentials
     */
    private function checkLibrarian($username, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM librarian WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $librarian = $stmt->fetch();

        if ($librarian && $password === $librarian['password']) {
            return $librarian;
        }
        return false;
    }

    /**
     * Check student credentials
     */
    private function checkStudent($username, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM student WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $student = $stmt->fetch();

        if ($student && $password === $student['password']) {
            return $student;
        }
        return false;
    }

    /**
     * Register a new student
     */
    public function registerStudent($name, $username, $password) {
        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO student (name, username, password) VALUES (:name, :username, :password)"
            );
            return $stmt->execute([
                'name' => $name,
                'username' => $username,
                'password' => $password
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Start user session
     */
    public function startSession($userData) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $userData['user_id'];
        $_SESSION['username'] = $userData['username'];
        $_SESSION['role'] = $userData['role'];
        
        if ($userData['role'] === 'student') {
            $_SESSION['name'] = $userData['name'];
            $_SESSION['blacklist_status'] = $userData['blacklist_status'];
        }
        
        $_SESSION['last_activity'] = time();
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            return false;
        }

        // Check session timeout
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
            self::logout();
            return false;
        }

        $_SESSION['last_activity'] = time();
        return true;
    }

    /**
     * Check user role
     */
    public static function getRole() {
        return $_SESSION['role'] ?? null;
    }

    /**
     * Logout user
     */
    public static function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
    }

    /**
     * Redirect based on role
     */
    public static function redirectToDashboard() {
        $role = self::getRole();
        $baseUrl = '/';
        switch ($role) {
            case 'manager':
                header('Location: ' . $baseUrl . 'views/manager/dashboard.php');
                break;
            case 'librarian':
                header('Location: ' . $baseUrl . 'views/librarian/dashboard.php');
                break;
            case 'student':
                header('Location: ' . $baseUrl . 'views/student/dashboard.php');
                break;
            default:
                header('Location: ' . $baseUrl . 'login.php');
        }
        exit;
    }
}
