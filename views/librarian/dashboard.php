<?php
require_once '../../config/config.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';
require_once '../../classes/Book.php';
require_once '../../classes/BorrowRecord.php';
require_once '../../classes/Student.php';

if (!User::isLoggedIn() || User::getRole() !== 'librarian') {
    header('Location: ../../login.php');
    exit;
}

$username = $_SESSION['username'];
$book = new Book();
$borrowRecord = new BorrowRecord();
$student = new Student();

// Get statistics
$bookStats = $book->getStatistics();
$studentStats = $student->getStatistics();
$pendingApprovals = $borrowRecord->getPendingLibrarianApprovals();
$activeBorrows = $borrowRecord->getAllActiveBorrows();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Dashboard - LMS</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="dashboard.php" class="navbar-brand">Library Management System</a>
            <ul class="navbar-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="inventory.php">Inventory</a></li>
                <li><a href="approvals.php">Approvals</a></li>
                <li><a href="returns.php">Returns</a></li>
                <li class="navbar-user">Welcome, <?php echo htmlspecialchars($username); ?></li>
                <li><a href="../../process/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="dashboard">
            <h2>Librarian Dashboard</h2>

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
                    <h3>Total Books</h3>
                    <div class="stat-value"><?php echo $bookStats['total_books']; ?></div>
                </div>
                <div class="stat-card green">
                    <h3>Available Books</h3>
                    <div class="stat-value"><?php echo $bookStats['available_books']; ?></div>
                </div>
                <div class="stat-card orange">
                    <h3>Borrowed Books</h3>
                    <div class="stat-value"><?php echo $bookStats['borrowed_books']; ?></div>
                </div>
                <div class="stat-card red">
                    <h3>Pending Approvals</h3>
                    <div class="stat-value"><?php echo count($pendingApprovals); ?></div>
                </div>
            </div>

            <!-- Pending Approvals -->
            <h3>Pending Approvals</h3>
            <?php if (empty($pendingApprovals)): ?>
                <p>No pending approvals.</p>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Book Title</th>
                                <th>Author</th>
                                <th>Request Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingApprovals as $approval): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($approval['name']); ?></td>
                                    <td><?php echo htmlspecialchars($approval['title']); ?></td>
                                    <td><?php echo htmlspecialchars($approval['author']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($approval['created_at'])); ?></td>
                                    <td>
                                        <form action="../../process/librarian_approval.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="record_id" value="<?php echo $approval['record_id']; ?>">
                                            <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- Active Borrows -->
            <h3 class="mt-20">Active Borrows</h3>
            <?php if (empty($activeBorrows)): ?>
                <p>No active borrows.</p>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Book</th>
                                <th>Borrow Date</th>
                                <th>Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activeBorrows as $borrow): ?>
                                <?php
                                $today = date('Y-m-d');
                                $isOverdue = $today > $borrow['due_date'];
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($borrow['name']); ?></td>
                                    <td><?php echo htmlspecialchars($borrow['title']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($borrow['borrow_date'])); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($borrow['due_date'])); ?></td>
                                    <td>
                                        <?php if ($isOverdue): ?>
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
        </div>
    </div>
</body>
</html>
