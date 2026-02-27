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
$pendingApprovals = $borrowRecord->getPendingLibrarianApprovals();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Requests - LMS</title>
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
            <h2>Pending Borrow Requests</h2>

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

            <?php if (empty($pendingApprovals)): ?>
                <div class="alert alert-info">
                    No pending requests at this time.
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Student Name</th>
                                <th>Username</th>
                                <th>Book Title</th>
                                <th>Author</th>
                                <th>Borrow Date</th>
                                <th>Due Date</th>
                                <th>Request Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingApprovals as $approval): ?>
                                <tr>
                                    <td><?php echo $approval['record_id']; ?></td>
                                    <td><?php echo htmlspecialchars($approval['name']); ?></td>
                                    <td><?php echo htmlspecialchars($approval['username']); ?></td>
                                    <td><?php echo htmlspecialchars($approval['title']); ?></td>
                                    <td><?php echo htmlspecialchars($approval['author']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($approval['borrow_date'])); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($approval['due_date'])); ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($approval['created_at'])); ?></td>
                                    <td>
                                        <form action="../../process/librarian_approval.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="record_id" value="<?php echo $approval['record_id']; ?>">
                                            <button type="submit" class="btn btn-success btn-sm">Approve & Issue Book</button>
                                        </form>
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
