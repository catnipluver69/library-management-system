<?php
require_once '../../config/config.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';
require_once '../../classes/Book.php';

if (!User::isLoggedIn() || User::getRole() !== 'student') {
    header('Location: ../../login.php');
    exit;
}

$studentName = $_SESSION['name'];
$book = new Book();

// Handle search
$searchTerm = $_GET['search'] ?? '';
$books = !empty($searchTerm) ? $book->searchBooks($searchTerm) : $book->getAllBooks();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Books - LMS</title>
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
            <h2>Search Books</h2>

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

            <!-- Search Box -->
            <div class="search-box">
                <form action="" method="GET">
                    <input type="text" name="search" placeholder="Search by title, author, or ISBN..." 
                           value="<?php echo htmlspecialchars($searchTerm); ?>" autofocus>
                    <button type="submit" class="btn btn-primary mt-20">Search</button>
                </form>
            </div>

            <!-- Books Table -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>ISBN</th>
                            <th>Availability</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($books)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No books found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($books as $b): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($b['title']); ?></td>
                                    <td><?php echo htmlspecialchars($b['author']); ?></td>
                                    <td><?php echo htmlspecialchars($b['ISBN']); ?></td>
                                    <td>
                                        <?php if ($b['availability'] === 'Available'): ?>
                                            <span class="badge badge-success">Available</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Borrowed</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form action="../../process/borrow_process.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="book_id" value="<?php echo $b['book_id']; ?>">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                Borrow
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
