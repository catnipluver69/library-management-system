-- ============================================
-- Library Management System Database Schema
-- ============================================

-- Create database
CREATE DATABASE IF NOT EXISTS library_management_system;
USE library_management_system;

-- ============================================
-- Table: Manager
-- ============================================
CREATE TABLE IF NOT EXISTS Manager (
    manager_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Table: Librarian
-- ============================================
CREATE TABLE IF NOT EXISTS Librarian (
    librarian_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Table: Student
-- ============================================
CREATE TABLE IF NOT EXISTS Student (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    blacklist_status BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Table: Book
-- ============================================
CREATE TABLE IF NOT EXISTS Book (
    book_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    ISBN VARCHAR(20) NOT NULL UNIQUE,
    availability ENUM('Available', 'Borrowed') DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_isbn (ISBN),
    INDEX idx_title (title),
    INDEX idx_availability (availability)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Table: Borrow_Record
-- ============================================
CREATE TABLE IF NOT EXISTS Borrow_Record (
    record_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    book_id INT NOT NULL,
    borrow_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE DEFAULT NULL,
    fine_amount DECIMAL(10, 2) DEFAULT 0.00,
    status ENUM('Pending', 'Approved', 'Rejected', 'Returned') DEFAULT 'Pending',
    manager_approved BOOLEAN DEFAULT FALSE,
    librarian_approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES Student(student_id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES Book(book_id) ON DELETE CASCADE,
    INDEX idx_student (student_id),
    INDEX idx_book (book_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Table: Fine
-- ============================================
CREATE TABLE IF NOT EXISTS Fine (
    fine_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    status ENUM('Paid', 'Unpaid') DEFAULT 'Unpaid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    paid_at TIMESTAMP NULL,
    FOREIGN KEY (student_id) REFERENCES Student(student_id) ON DELETE CASCADE,
    INDEX idx_student (student_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Table: Waiting_List
-- ============================================
CREATE TABLE IF NOT EXISTS Waiting_List (
    queue_id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    student_id INT NOT NULL,
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notified BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (book_id) REFERENCES Book(book_id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES Student(student_id) ON DELETE CASCADE,
    INDEX idx_book (book_id),
    INDEX idx_student (student_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Insert Default Data
-- ============================================

-- Default Manager (username: admin, password: admin123)
INSERT INTO Manager (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Default Librarian (username: librarian, password: librarian123)
INSERT INTO Librarian (username, password) VALUES 
('librarian', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Sample Students (password for all: student123)
INSERT INTO Student (name, username, password, blacklist_status) VALUES 
('John Doe', 'john.doe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', FALSE),
('Jane Smith', 'jane.smith', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', FALSE),
('Bob Johnson', 'bob.johnson', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE);

-- Sample Books
INSERT INTO Book (title, author, ISBN, availability) VALUES 
('Introduction to Algorithms', 'Thomas H. Cormen', '978-0262033848', 'Available'),
('Clean Code', 'Robert C. Martin', '978-0132350884', 'Available'),
('Design Patterns', 'Erich Gamma', '978-0201633612', 'Available'),
('The Pragmatic Programmer', 'Andrew Hunt', '978-0135957059', 'Available'),
('Code Complete', 'Steve McConnell', '978-0735619678', 'Borrowed');

-- ============================================
-- End of Schema
-- ============================================
