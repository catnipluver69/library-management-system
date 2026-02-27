<?php
require_once '../../config/config.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';
require_once '../../classes/Book.php';
require_once '../../classes/BorrowRecord.php';
require_once '../../classes/Fine.php';
require_once '../../classes/Student.php';

if (!User::isLoggedIn() || User::getRole() !== 'student') {
    header('Location: ../../login.php');
    exit;
}

$studentId = $_SESSION['user_id'];
$studentName = $_SESSION['name'];

$borrowRecord = new BorrowRecord();
$fine = new Fine();
$student = new Student();

// Get student data
$borrowedBooks = $borrowRecord->getBorrowedBooksByStudent($studentId);
$totalFines = $fine->getTotalUnpaidFines($studentId);
$waitingList = $student->getWaitingList($studentId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - LMS</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="dashboard.php" class="navbar-brand">Library Management System</a>
            <ul class="navbar-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="search_books.php">Search Books</a></li>
                <li><a href="my_books.php">My Books</a></li>
                <li class="navbar-user">Welcome, <?php echo htmlspecialchars($studentName); ?></li>
                <li><a href="../../process/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="dashboard">
            <h2>Student Dashboard</h2>

            <?php if ($_SESSION['blacklist_status']): ?>
                <div class="alert alert-error">
                    <strong>Warning:</strong> Your account is blacklisted. You cannot borrow books.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Books Borrowed</h3>
                    <div class="stat-value"><?php echo count($borrowedBooks); ?></div>
                </div>
                <div class="stat-card <?php echo $totalFines > 0 ? 'red' : 'green'; ?>">
                    <h3>Outstanding Fines</h3>
                    <div class="stat-value">$<?php echo number_format($totalFines, 2); ?></div>
                </div>
                <div class="stat-card orange">
                    <h3>Waiting List</h3>
                    <div class="stat-value"><?php echo count($waitingList); ?></div>
                </div>
            </div>

            <!-- Currently Borrowed Books -->
            <h3>Currently Borrowed Books & Pending Requests</h3>
            <?php if (empty($borrowedBooks)): ?>
                <p>You have no borrowed books or pending requests.</p>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>ISBN</th>
                                <th>Borrow Date</th>
                                <th>Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($borrowedBooks as $book): ?>
                                <?php
                                $today = date('Y-m-d');
                                $isOverdue = $book['status'] === 'Approved' && $today > $book['due_date'];
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                                    <td><?php echo htmlspecialchars($book['ISBN']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($book['borrow_date'])); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($book['due_date'])); ?></td>
                                    <td>
                                        <?php if ($book['status'] === 'Pending'): ?>
                                            <span class="badge badge-warning">Pending Approval</span>
                                        <?php elseif ($isOverdue): ?>
                                            <span class="badge badge-danger">Overdue</span>
                                        <?php else: ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- Waiting List -->
            <?php if (!empty($waitingList)): ?>
                <h3 class="mt-20">Your Waiting List</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>ISBN</th>
                                <th>Request Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($waitingList as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                                    <td><?php echo htmlspecialchars($item['author']); ?></td>
                                    <td><?php echo htmlspecialchars($item['ISBN']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($item['request_date'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- Quick Actions -->
            <div class="mt-20">
                <a href="search_books.php" class="btn btn-primary">Search & Borrow Books</a>
            </div>
        </div>
    </div>
</body>
</html>
