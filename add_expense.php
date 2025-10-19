<?php
session_start();
include 'connection.php';

// Agar user login nahi hai to login page pe bhej do
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Multiple Expense Insert Logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['category'])) {
    $categories = $_POST['category'];
    $amounts = $_POST['amount'];
    $descriptions = $_POST['description'];
    $dates = $_POST['date'];

    $stmt = $conn->prepare("INSERT INTO expenses (user_id, category, amount, description, date, created_at) VALUES (?, ?, ?, ?, ?, NOW())");

    for ($i = 0; $i < count($categories); $i++) {
        if (!empty($categories[$i]) && !empty($amounts[$i])) {
            $stmt->bind_param("isdss", $user_id, $categories[$i], $amounts[$i], $descriptions[$i], $dates[$i]);
            $stmt->execute();
        }
    }

    echo "<script>alert('All expenses added successfully!'); window.location='view_expenses.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Expenses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f9;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            max-width: 900px;
            margin-top: 60px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .btn-add {
            background-color: #28a745;
            color: white;
        }
        .btn-remove {
            background-color: #dc3545;
            color: white;
        }
        .btn-add:hover, .btn-remove:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card p-4">
        <h3 class="text-center mb-4">➕ Add Multiple Expenses</h3>

        <form method="POST">
            <table class="table table-bordered" id="expenseTable">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Category</th>
                        <th>Amount (Rs)</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" name="category[]" class="form-control" required></td>
                        <td><input type="number" name="amount[]" step="0.01" class="form-control" required></td>
                        <td><input type="text" name="description[]" class="form-control"></td>
                        <td><input type="date" name="date[]" class="form-control" required></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-add btn-sm" onclick="addRow()">+</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="text-center mt-3">
               <button type="submit" class="btn btn-primary px-4">💾 Save All Expenses</button>
               <a href="dashboard.php" class="btn btn-success px-4 ms-2">🏠 Go to Dashboard</a>
           </div>

        </form>
    </div>
</div>

<script>
function addRow() {
    let table = document.getElementById("expenseTable").getElementsByTagName('tbody')[0];
    let newRow = table.rows[0].cloneNode(true);

    // Clear input values in the new row
    newRow.querySelectorAll('input').forEach(input => input.value = '');

    // Change "+" to "−" button for removal
    let btn = newRow.querySelector('.btn-add');
    btn.classList.remove('btn-add');
    btn.classList.add('btn-remove');
    btn.textContent = '−';
    btn.setAttribute('onclick', 'removeRow(this)');

    table.appendChild(newRow);
}

function removeRow(btn) {
    let row = btn.closest('tr');
    row.remove();
}
</script>

</body>
</html>
