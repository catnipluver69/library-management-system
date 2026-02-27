<?php
require_once '../../config/config.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';
require_once '../../classes/Book.php';

if (!User::isLoggedIn() || User::getRole() !== 'librarian') {
    header('Location: ../../login.php');
    exit;
}

$username = $_SESSION['username'];
$book = new Book();
$books = $book->getAllBooks();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - LMS</title>
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
            <h2>Book Inventory Management</h2>

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

            <button onclick="openAddModal()" class="btn btn-primary mb-20">Add New Book</button>

            <!-- Books Table -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>ISBN</th>
                            <th>Availability</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($books)): ?>
                            <tr>
                                <td colspan="6" class="text-center">No books in inventory.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($books as $b): ?>
                                <tr>
                                    <td><?php echo $b['book_id']; ?></td>
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
                                        <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($b)); ?>)" 
                                                class="btn btn-warning btn-sm">Edit</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Book Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddModal()">&times;</span>
            <h2>Add New Book</h2>
            <form action="../../process/add_book.php" method="POST">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="author">Author</label>
                    <input type="text" id="author" name="author" required>
                </div>
                <div class="form-group">
                    <label for="isbn">ISBN</label>
                    <input type="text" id="isbn" name="isbn" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Book</button>
            </form>
        </div>
    </div>

    <!-- Edit Book Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Book</h2>
            <form action="../../process/edit_book.php" method="POST">
                <input type="hidden" id="edit_book_id" name="book_id">
                <div class="form-group">
                    <label for="edit_title">Title</label>
                    <input type="text" id="edit_title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="edit_author">Author</label>
                    <input type="text" id="edit_author" name="author" required>
                </div>
                <div class="form-group">
                    <label for="edit_isbn">ISBN</label>
                    <input type="text" id="edit_isbn" name="isbn" required>
                </div>
                <button type="submit" class="btn btn-primary">UPDATE book</button>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('addModal').style.display = 'block';
        }

        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }

        function openEditModal(book) {
            document.getElementById('edit_book_id').value = book.book_id;
            document.getElementById('edit_title').value = book.title;
            document.getElementById('edit_author').value = book.author;
            document.getElementById('edit_isbn').value = book.ISBN;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const addModal = document.getElementById('addModal');
            const editModal = document.getElementById('editModal');
            if (event.target == addModal) {
                closeAddModal();
            }
            if (event.target == editModal) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>
