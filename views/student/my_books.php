<?php
require_once '../../config/config.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';
require_once '../../classes/BorrowRecord.php';

if (!User::isLoggedIn() || User::getRole() !== 'student') {
    header('Location: ../../login.php');
    exit;
}

$studentId = $_SESSION['user_id'];
$studentName = $_SESSION['name'];

$borrowRecord = new BorrowRecord();
$history = $borrowRecord->getBorrowHistory($studentId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Books - LMS</title>
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
            <h2>My Borrowing History</h2>

            <?php if (empty($history)): ?>
                <p>You have no borrowing history.</p>
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
                                <th>Return Date</th>
                                <th>Fine</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($history as $record): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($record['title']); ?></td>
                                    <td><?php echo htmlspecialchars($record['author']); ?></td>
                                    <td><?php echo htmlspecialchars($record['ISBN']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($record['borrow_date'])); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($record['due_date'])); ?></td>
                                    <td>
                                        <?php 
                                        echo $record['return_date'] 
                                            ? date('M d, Y', strtotime($record['return_date'])) 
                                            : '-'; 
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        echo $record['fine_amount'] > 0 
                                            ? '$' . number_format($record['fine_amount'], 2) 
                                            : '-'; 
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        switch ($record['status']) {
                                            case 'Approved':
                                                echo '<span class="badge badge-success">Active</span>';
                                                break;
                                            case 'Returned':
                                                echo '<span class="badge badge-info">Returned</span>';
                                                break;
                                            case 'Pending':
                                                echo '<span class="badge badge-warning">Pending</span>';
                                                break;
                                            case 'Rejected':
                                                echo '<span class="badge badge-danger">Rejected</span>';
                                                break;
                                        }
                                        ?>
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
