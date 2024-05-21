<!DOCTYPE html>
<html lang="en">

<?php
include('db.php');
include('includes/header.php');

// Fetch all expenses
$q = $conn->prepare("SELECT * FROM expenses");
$q->execute();
$expenses = $q->fetchAll(PDO::FETCH_OBJ);
$q->closeCursor();
?>

<head>
    <style>
        /* Reset default styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body styles */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f9;
            color: #333;
        }

        /* Navigation Bar */
        .navigation-bar {
            background-color: #007bff;
            color: white;
            padding: 15px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navigation-bar nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navigation-bar ul {
            list-style-type: none;
            display: flex;
            margin: 0;
            padding: 0;
        }

        .navigation-bar ul li {
            margin-left: 20px;
        }

        .navigation-bar ul li a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }

        .navigation-bar ul li a:hover {
            color: #dcdcdc;
        }

        /* Container and Side Bar */
        .container {
            display: flex;
            min-height: calc(100vh - 50px);
        }

        .side-bar {
            width: 250px;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
            transition: width 0.3s;
        }

        .side-bar ul {
            list-style-type: none;
            padding: 0;
        }

        .side-bar ul li {
            padding: 15px 20px;
            transition: background-color 0.3s;
        }

        .side-bar ul li a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .side-bar ul li:hover {
            background-color: #495057;
        }

        .side-bar .material-symbols-outlined {
            margin-right: 10px;
            font-size: 20px;
        }

        /* Dashboard and Cards */
        .dashboard {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            padding: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
        }

        .card .icon {
            font-size: 24px;
            color: #007bff;
            margin-bottom: 10px;
        }

        .card .title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        .card .amount {
            font-size: 18px;
            margin: 10px 0;
            color: #28a745;
        }

        .card .description,
        .card .category {
            font-size: 14px;
            color: #6c757d;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .side-bar {
                width: 60px;
            }

            .side-bar ul li {
                text-align: center;
            }

            .side-bar ul li a {
                justify-content: center;
            }

            .side-bar .material-symbols-outlined {
                margin-right: 0;
            }

            .dashboard {
                padding: 10px;
            }

            .card {
                padding: 10px;
            }

            .card .title,
            .card .amount,
            .card .description,
            .card .category {
                font-size: 14px;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

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
            <div class="chart-container">
                <canvas id="expenseChart"></canvas>
            </div>
            <?php foreach ($expenses as $expense) : ?>
                <div class="card" id="card-<?= htmlspecialchars($expense->id) ?>">
                    <i class="icon fa fa-briefcase"></i>
                    <div class="title"><?= htmlspecialchars($expense->description) ?></div>
                    <div class="amount">$<?= htmlspecialchars(number_format($expense->amount, 2)) ?></div>
                    <div class="description">Spent on: <?= htmlspecialchars($expense->date) ?></div>
                    <div class="category">Category ID: <?= htmlspecialchars($expense->category_id) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('expenseChart').getContext('2d');
            const data = {
                labels: [
                    <?php foreach ($expenses as $expense) {
                        echo '"' . htmlspecialchars($expense->description) . '",';
                    } ?>
                ],
                datasets: [{
                    label: 'Expenses',
                    data: [
                        <?php foreach ($expenses as $expense) {
                            echo htmlspecialchars($expense->amount) . ',';
                        } ?>
                    ],
                    backgroundColor: 'rgba(0, 123, 255, 0.2)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }]
            };
            const config = {
                type: 'bar',
                data: data,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            };
            const expenseChart = new Chart(ctx, config);
        });
    </script>
</body>

</html>
