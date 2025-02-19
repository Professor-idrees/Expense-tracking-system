<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="expenses.css">
<?php
include('includes/header.php');
include('db.php');
include('includes/functions.php');

// Fetch all categories
$categories = get_all_data_from_table('categories');

// Fetch current user expenses
$expenses = get_all_data_from_table_with_parameter('expenses', 'user_id', $_SESSION['user_id']);

if (isset($_POST['add-expense'])) {
    $q = $conn->prepare("INSERT INTO expenses(user_id,description,category_id,amount) VALUES(:user_id,:description,:category_id,:amount)");

    $q->execute([
        'user_id' => $_SESSION['user_id'],
        'description' => $_POST['designationInput'],
        'category_id' => $_POST['category_id'],
        'amount' => $_POST['amountInput']
    ]);

    $q->closeCursor();
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
                            Expenses
                        </div>
                        <div class="col-md-2">
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-expense">Add Expense</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead class="bg-secondary text-white">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Designation</th>
                                <th scope="col">Categories</th>
                                <th scope="col">Amount ($)</th>
                                <th scope="col">Expense Date</th>
                                <th scope="col">Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($expenses) != 0) : ?>
                                <?php foreach ($expenses as $index => $expense) : ?>
                                    <?php $category = fetch_defined_record_by_parameter('categories', 'id', $expense->category_id); ?>
                                    <tr>
                                        <th scope="row"><?= $index + 1 ?></th>
                                        <td><?= $expense->description ?></td>
                                        <td><?= $category->name ?></td>
                                        <td><?= $expense->amount ?></td>
                                        <td><?= $expense->date ?></td>
                                        <td><span data-expense_id="<?= $expense->id ?>" class="material-symbols-outlined delete-expense">
                                                delete
                                            </span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>Aucune depense pour le moment...</tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Add category modal -->
    <div class="modal" tabindex="-1" id="add-expense">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ADD EXPENSE</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="designationInput" class="form-label">Expense name</label>
                                <input type="text" class="form-control" name="designationInput" id="designationInput" placeholder="Designation" required>
                            </div>
                            <div class="col-md-6">
                                <label for="amountInput" class="form-label">Expense categories</label>
                                <select class="form-control" name="category_id" required>
                                    <option>Categories</option>
                                    <?php foreach ($categories as $category) : ?>
                                        <option value="<?= $category->id ?>"><?= $category->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="amountInput" class="form-label">Amount</label>
                                <input type="number" class="form-control" name="amountInput" id="amountInput" placeholder="Amount ($)" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add-expense" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add category modal -->

    <!-- Bootstrap script -->
    <script src="vendors/boostrap/js/bootstrap.min.js"></script>
    <script src="vendors/jquery-3.6.4.min.js"></script>
    <script>
        $(document).on('click', '.delete-expense', function() {
            var expense_id = $(this).data('expense_id');
            var url = 'ajax/delete-expense.php';
            if (confirm("Delete this expense?") == true) {
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        expense_id: expense_id
                    },
                    beforeSend: function() {
                        alert('Requesting');
                    },
                    success: function(data) {
                        alert('Success');
                    }
                });
            }
        });
    </script>
</body>

</html>