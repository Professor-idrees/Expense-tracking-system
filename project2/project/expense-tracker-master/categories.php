<!DOCTYPE html>
<html lang="en">

<?php
include('db.php');
include('includes/header.php');
include('includes/functions.php');

// Fetch all categories
$categories = get_all_data_from_table('categories');

// Initialize error message variable
$errorMessage = '';

if (isset($_POST['add-category'])) {
    $categoryName = $_POST['designationInput'];

    // Check if category name already exists
    $q = $conn->prepare("SELECT COUNT(*) FROM categories WHERE name = :name");
    $q->execute(['name' => $categoryName]);
    $count = $q->fetchColumn();
    $q->closeCursor();

    if ($count > 0) {
        // Category name already exists, set error message
        $errorMessage = 'Category name already exists. Please choose a different name.';
    } else {
        // Insert new category
        $q = $conn->prepare("INSERT INTO categories(name) VALUES(:name)");
        $q->execute(['name' => $categoryName]);
        $q->closeCursor();

        // Refresh the page to show the new category
        header("Location: categories.php");
        exit();
    }
}

if (isset($_POST['delete-category'])) {
    $categoryId = $_POST['category_id'];

    // Delete category
    $q = $conn->prepare("DELETE FROM categories WHERE id = :id");
    $q->execute(['id' => $categoryId]);
    $q->closeCursor();

    // Refresh the page to show the updated category list
    header("Location: categories.php");
    exit();
}

if (isset($_POST['update-category'])) {
    $categoryId = $_POST['category_id'];
    $categoryName = $_POST['updateDesignationInput'];

    // Update category
    $q = $conn->prepare("UPDATE categories SET name = :name WHERE id = :id");
    $q->execute(['name' => $categoryName, 'id' => $categoryId]);
    $q->closeCursor();

    // Refresh the page to show the updated category list
    header("Location: categories.php");
    exit();
}
?>

<body>
    <header class="navigation-bar">
        <nav>
            <h1>Expense tracker</h1>
            <ul>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <div class="container">
        <div class="side-bar">
            <ul>
                <li><span class="material-symbols-outlined">
                        widgets
                    </span><a href="dashboard.php">Dashboard</a></li>
                <li><span class="material-symbols-outlined">
                        category
                    </span> <a href="categories.php">Categories</a></li>
                <li><span class="material-symbols-outlined">
                        edit_calendar
                    </span> <a href="expenses.php">Expenses</a></li>
                <li><span class="material-symbols-outlined">
                        settings
                    </span> <a href="settings.php">Settings</a></li>
            </ul>
        </div>
        <div class="dashboard">
            <div class="card w-100">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-8">
                            Categories
                        </div>
                        <div class="col-md-2">
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-category">Add Category</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($errorMessage): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($errorMessage) ?>
                        </div>
                    <?php endif; ?>
                    <table class="table">
                        <thead class="bg-secondary text-white">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Label</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $index => $category) : ?>
                                <tr>
                                    <th scope="row"><?= $index + 1 ?></th>
                                    <td><?= htmlspecialchars($category->name) ?></td>
                                    <td>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="category_id" value="<?= $category->id ?>">
                                            <button type="submit" name="delete-category" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#update-category-<?= $category->id ?>">Update</button>
                                    </td>
                                </tr>

                                <!-- Update category modal -->
                                <div class="modal" tabindex="-1" id="update-category-<?= $category->id ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Update Category</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="category_id" value="<?= $category->id ?>">
                                                    <div class="mb-3">
                                                        <label for="updateDesignationInput" class="form-label">Designation</label>
                                                        <input type="text" class="form-control" name="updateDesignationInput" id="updateDesignationInput" value="<?= htmlspecialchars($category->name) ?>" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" name="update-category" class="btn btn-primary">Save changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Update category modal -->
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Add category modal -->
    <div class="modal" tabindex="-1" id="add-category">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="designationInput" class="form-label">Designation</label>
                            <input type="text" class="form-control" name="designationInput" id="designationInput" placeholder="Category name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add-category" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add category modal -->

    <!-- Bootstrap script -->
    <script src="vendors/boostrap/js/bootstrap.min.js"></script>
</body>

</html>
