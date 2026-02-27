<?php
require_once '../../config/config.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';
require_once '../../classes/BorrowRecord.php';

if (!User::isLoggedIn() || User::getRole() !== 'librarian') {
    header('Location: ../../login.php');
    exit;
}

$username = $_SESSION['username'];
$borrowRecord = new BorrowRecord();
$activeBorrows = $borrowRecord->getAllActiveBorrows();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Returns - LMS</title>
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
            <h2>Process Book Returns</h2>

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

            <?php if (empty($activeBorrows)): ?>
                <div class="alert alert-info">
                    No active borrows at this time.
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Record ID</th>
                                <th>Student Name</th>
                                <th>Username</th>
                                <th>Book Title</th>
                                <th>Author</th>
                                <th>ISBN</th>
                                <th>Borrow Date</th>
                                <th>Due Date</th>
                                <th>Days Remaining</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activeBorrows as $borrow): ?>
                                <?php
                                $today = date('Y-m-d');
                                $daysRemaining = (strtotime($borrow['due_date']) - strtotime($today)) / (60 * 60 * 24);
                                $isOverdue = $daysRemaining < 0;
                                ?>
                                <tr>
                                    <td><?php echo $borrow['record_id']; ?></td>
                                    <td><?php echo htmlspecialchars($borrow['name']); ?></td>
                                    <td><?php echo htmlspecialchars($borrow['username']); ?></td>
                                    <td><?php echo htmlspecialchars($borrow['title']); ?></td>
                                    <td><?php echo htmlspecialchars($borrow['author']); ?></td>
                                    <td><?php echo htmlspecialchars($borrow['ISBN']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($borrow['borrow_date'])); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($borrow['due_date'])); ?></td>
                                    <td>
                                        <?php if ($isOverdue): ?>
                                            <span class="badge badge-danger">
                                                <?php echo abs(ceil($daysRemaining)) . ' days overdue'; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-success">
                                                <?php echo ceil($daysRemaining) . ' days'; ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form action="../../process/return_process.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="record_id" value="<?php echo $borrow['record_id']; ?>">
                                            <button type="submit" class="btn btn-primary btn-sm" 
                                                    onclick="return confirm('Confirm book return?')">
                                                Process Return
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div class="alert alert-info mt-20">
                <strong>Note:</strong> Returns after the due date will automatically generate fines at $<?php echo FINE_PER_DAY; ?> per day.
            </div>
        </div>
    </div>
</body>
</html>
