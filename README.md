# Library Management System

> A full-stack web application for managing library operations with role-based access control and automated workflows.

**Live Demo:** [fouadlibrary.gt.tc](http://fouadlibrary.gt.tc)

---

<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/7bdbb05b-c7cf-4478-9b0e-3c3cbe969730" />

## About

This project was developed as a **Software Engineering college project** to demonstrate practical application of software development principles including system design, database modeling, and multi-tier architecture. The application is currently hosted on [InfinityFree](https://infinityfree.net/).

## Features

### For Students
- Search and browse available books
- Submit borrow requests with approval tracking
- View borrowed books and due dates
- Return books through the system
- Track fines and payment status

### For Librarians
- Manage book inventory (add, edit, remove)
- Approve/reject borrow requests
- Process book returns
- Calculate fines for overdue books
- View pending approvals dashboard

### For Managers
- Final approval authority for borrow requests
- Blacklist/unblacklist students
- Mark fines as paid
- System-wide analytics dashboard

## How It Works

The system implements a **dual-approval workflow** for book borrowing:

1. Student submits a borrow request
2. Librarian reviews and approves (first level)
3. Manager provides final approval (second level)
4. Book status updates to "Borrowed"

**Fine Management:** Overdue books automatically generate fines. Students must clear outstanding fines before borrowing additional books

## Tech Stack

- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP (OOP)
- **Database:** MySQL
- **Hosting:** InfinityFree

## Project Structure

```
├── index.php              # Entry point
├── login.php              # Authentication
├── classes/               # PHP classes (OOP)
├── config/                # Configuration files
├── database/              # SQL schema
├── process/               # Backend processing
├── views/                 # UI components
│   ├── student/
│   ├── librarian/
│   └── manager/
└── assets/                # CSS and static files
```

## Database Schema

**Main Tables:**
- `Manager` - Manager accounts
- `Librarian` - Librarian accounts  
- `Student` - Student information and blacklist status
- `Book` - Book inventory with ISBN, availability
- `Borrow_Record` - Borrowing transactions with dual approval tracking
- `Fine` - Overdue fines
- `Waiting_List` - Queue for unavailable books

## Installation

### Prerequisites
- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx

### Setup

1. Clone the repository
```bash
git clone <repository-url>
cd library-management-system
```

2. Create database and import schema
```bash
mysql -u username -p
CREATE DATABASE library_management_system;
USE library_management_system;
SOURCE database/schema.sql;
```

3. Configure database connection in `config/config.php`
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'library_management_system');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

4. Start your web server and navigate to the application

## Security

- Password hashing with `password_hash()`
- Prepared statements to prevent SQL injection
- Session-based authentication
- Role-based access control (RBAC)

## Contributing

This project was created as an educational exercise. Contributions, issues, and feature requests are welcome!


---

Made with ❤️ for Software Engineering coursework under the supervision of Prof. Ahmed Shehata



