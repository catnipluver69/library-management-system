<?php
require_once '../../config/config.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';
require_once '../../classes/Student.php';

if (!User::isLoggedIn() || User::getRole() !== 'manager') {
    header('Location: ../../login.php');
    exit;
}

$username = $_SESSION['username'];
$student = new Student();
$allStudents = $student->getAllStudents();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blacklist Management - LMS</title>
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
            <h2>Blacklist Management</h2>

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

            <div class="alert alert-warning">
                <strong>Note:</strong> Blacklisted students cannot borrow books. Use this feature for students with severe violations.
            </div>

            <h3>All Students</h3>
            <?php if (empty($allStudents)): ?>
                <p>No students registered.</p>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Status</th>
                                <th>Registered</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allStudents as $s): ?>
                                <tr>
                                    <td><?php echo $s['student_id']; ?></td>
                                    <td><?php echo htmlspecialchars($s['name']); ?></td>
                                    <td><?php echo htmlspecialchars($s['username']); ?></td>
                                    <td>
                                        <?php if ($s['blacklist_status']): ?>
                                            <span class="badge badge-danger">Blacklisted</span>
                                        <?php else: ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($s['created_at'])); ?></td>
                                    <td>
                                        <form action="../../process/toggle_blacklist.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="student_id" value="<?php echo $s['student_id']; ?>">
                                            <input type="hidden" name="current_status" value="<?php echo $s['blacklist_status']; ?>">
                                            <?php if ($s['blacklist_status']): ?>
                                                <button type="submit" class="btn btn-success btn-sm" 
                                                        onclick="return confirm('Remove from blacklist?')">
                                                    Remove Blacklist
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" class="btn btn-danger btn-sm" 
                                                        onclick="return confirm('Add to blacklist? Student will not be able to borrow books.')">
                                                    Blacklist Student
                                                </button>
                                            <?php endif; ?>
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
