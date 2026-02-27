# Library Management System

A comprehensive web-based library management system developed as a **Software Engineering College Project**. The system facilitates efficient management of library resources, book borrowing, returns, fines, and user management with role-based access control.

🌐 **Live Demo**: [fouadlibrary.gt.tc](http://fouadlibrary.gt.tc)

## 🚀 Hosting

This project is hosted on **InfinityFree**, a free web hosting platform that provides PHP and MySQL support.

## 📋 Overview

The Library Management System is a full-stack web application built with PHP and MySQL that streamlines library operations through an intuitive interface for students, librarians, and managers. The system implements a multi-level approval workflow for book borrowing and automated fine calculation for overdue returns.

## ✨ Features

### 🎓 Student Features
- **Book Search**: Browse and search available books by title, author, or ISBN
- **Borrow Books**: Request to borrow books with automated approval workflow
- **View Borrowed Books**: Track currently borrowed books and due dates
- **Return Books**: Submit return requests for borrowed books
- **Fine Management**: View unpaid fines and payment status

### 📚 Librarian Features
- **Book Inventory Management**: Add, edit, and manage book records
- **Borrow Approvals**: Approve or reject student borrow requests
- **Return Processing**: Process book returns and calculate late fines
- **Dashboard**: Overview of pending approvals and library statistics

### 👔 Manager Features
- **Borrow Request Approvals**: Second-level approval for borrow requests
- **Blacklist Management**: Add/remove students from blacklist
- **Fine Management**: Mark fines as paid after payment verification
- **System Overview**: Comprehensive dashboard with system analytics

## 🏗️ System Architecture

### Role-Based Access Control
The system implements three distinct user roles:
1. **Student**: Can browse, borrow, and return books
2. **Librarian**: Manages inventory and processes transactions
3. **Manager**: Oversees operations and handles administrative tasks

### Approval Workflow
Book borrowing requires dual approval:
1. **Librarian Approval**: First level verification
2. **Manager Approval**: Final authorization
- Both approvals are required before a book is marked as borrowed

### Automated Fine System
- Fines are automatically calculated for overdue books
- Fine rate: Configured per day of delay
- Students must pay fines before borrowing additional books
- Managers verify and mark fines as paid

## 🗂️ Project Structure

```
library-management-system/
├── index.php                 # Main entry point (redirects based on login)
├── login.php                 # User authentication page
├── schema_export.sql         # Database export
│
├── assets/
│   └── css/
│       └── style.css         # Application styling
│
├── classes/                  # Object-Oriented PHP classes
│   ├── Book.php             # Book entity and operations
│   ├── BorrowRecord.php     # Borrow record management
│   ├── Database.php         # Database connection handler
│   ├── Fine.php             # Fine calculation and management
│   ├── Student.php          # Student entity and operations
│   └── User.php             # Authentication and session management
│
├── config/
│   └── config.php           # Database configuration
│
├── database/
│   └── schema.sql           # Database schema definition
│
├── process/                  # Backend processing scripts
│   ├── add_book.php         # Add new books
│   ├── borrow_process.php   # Handle borrow requests
│   ├── edit_book.php        # Edit book details
│   ├── librarian_approval.php   # Librarian approval logic
│   ├── login_process.php    # Login authentication
│   ├── logout.php           # Session termination
│   ├── manager_approval.php # Manager approval logic
│   ├── mark_fine_paid.php   # Fine payment processing
│   ├── return_process.php   # Book return handling
│   └── toggle_blacklist.php # Blacklist management
│
└── views/                    # User interface views
    ├── librarian/
    │   ├── approvals.php    # Manage borrow approvals
    │   ├── dashboard.php    # Librarian dashboard
    │   ├── inventory.php    # Book inventory management
    │   └── returns.php      # Process returns
    │
    ├── manager/
    │   ├── approvals.php    # Manager approval interface
    │   ├── blacklist.php    # Blacklist management
    │   ├── dashboard.php    # Manager dashboard
    │   └── fines.php        # Fine management
    │
    └── student/
        ├── dashboard.php    # Student home page
        ├── my_books.php     # View borrowed books
        └── search_books.php # Search and borrow books
```

## 💾 Database Schema

The system uses MySQL with the following main tables:

- **Manager**: Manager accounts and credentials
- **Librarian**: Librarian accounts and credentials
- **Student**: Student information and blacklist status
- **Book**: Book inventory with ISBN, title, author, and availability
- **Borrow_Record**: Tracks all borrowing transactions with approval status
- **Fine**: Student fines for overdue returns
- **Waiting_List**: Queue system for unavailable books

### Key Relationships
- Students can have multiple borrow records and fines
- Books can have multiple borrow records
- Borrow records link students to books with approval tracking

## 🛠️ Technology Stack

- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP (Object-Oriented)
- **Database**: MySQL
- **Hosting**: InfinityFree
- **Version Control**: Git

## 📦 Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- phpMyAdmin (optional, for database management)

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd library-management-system
   ```

2. **Import the database**
   - Create a new MySQL database
   - Import `database/schema.sql` using phpMyAdmin or MySQL CLI:
     ```bash
     mysql -u username -p database_name < database/schema.sql
     ```

3. **Configure database connection**
   - Edit `config/config.php`
   - Update database credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'library_management_system');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     ```

4. **Set up web server**
   - Point document root to the project folder
   - Ensure PHP is enabled
   - Configure URL rewriting if needed

5. **Access the application**
   - Navigate to `http://localhost/` or your configured domain
   - Login with default credentials (check database for initial users)

## 🔐 Security Features

- Password hashing using PHP's `password_hash()`
- SQL injection prevention with prepared statements
- Session-based authentication
- Role-based access control
- Input validation and sanitization

## 🎯 Use Cases

1. **Student borrows a book**
   - Student searches for available books
   - Submits borrow request
   - Librarian reviews and approves
   - Manager provides final approval
   - Book status changes to "Borrowed"

2. **Book return with fine**
   - Student submits return request
   - Librarian processes return
   - System calculates fine if overdue
   - Manager marks fine as paid after payment
   - Book becomes available again

3. **Blacklist management**
   - Manager identifies problematic students
   - Adds student to blacklist
   - Blacklisted students cannot borrow books
   - Manager can remove from blacklist when resolved

## 🤝 Contributing

This was developed as a college project for Software Engineering. Feel free to fork and enhance the system with additional features.

## 📝 License

This project was created for educational purposes as part of a Software Engineering course.

## 📧 Contact

For questions or feedback about this project, please visit the live site at [fouadlibrary.gt.tc](http://fouadlibrary.gt.tc)

---

**Note**: This system was designed and implemented as a practical application of software engineering principles including requirements analysis, system design, database modeling, and multi-tier architecture.
