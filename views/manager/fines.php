<?php
require_once '../../config/config.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';
require_once '../../classes/Fine.php';

if (!User::isLoggedIn() || User::getRole() !== 'manager') {
    header('Location: ../../login.php');
    exit;
}

$username = $_SESSION['username'];
$fine = new Fine();
$unpaidFines = $fine->getAllUnpaidFines();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fine Management - LMS</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="dashboard.php" class="navbar-brand">Library Management System</a>
            <ul class="navbar-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="fines.php">Fines</a></li>
                <li><a href="approvals.php">Approvals</a></li>
                <li><a href="blacklist.php">Blacklist</a></li>
                <li class="navbar-user">Welcome, <?php echo htmlspecialchars($username); ?></li>
                <li><a href="../../process/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="dashboard">
            <h2>Fine Management</h2>

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

            <h3>Outstanding Fines</h3>
            <?php if (empty($unpaidFines)): ?>
                <div class="alert alert-success">
                    No outstanding fines at this time!
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Fine ID</th>
                                <th>Student Name</th>
                                <th>Username</th>
                                <th>Amount</th>
                                <th>Date Issued</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $totalUnpaid = 0;
                            foreach ($unpaidFines as $f): 
                                $totalUnpaid += $f['amount'];
                            ?>
                                <tr>
                                    <td><?php echo $f['fine_id']; ?></td>
                                    <td><?php echo htmlspecialchars($f['name']); ?></td>
                                    <td><?php echo htmlspecialchars($f['username']); ?></td>
                                    <td>$<?php echo number_format($f['amount'], 2); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($f['created_at'])); ?></td>
                                    <td><span class="badge badge-danger">Unpaid</span></td>
                                    <td>
                                        <form action="../../process/mark_fine_paid.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="fine_id" value="<?php echo $f['fine_id']; ?>">
                                            <button type="submit" class="btn btn-success btn-sm" 
                                                    onclick="return confirm('Mark this fine as paid?')">
                                                Mark as Paid
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr style="background-color: #f7fafc; font-weight: bold;">
                                <td colspan="3" class="text-right">Total Outstanding:</td>
                                <td colspan="4">$<?php echo number_format($totalUnpaid, 2); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
