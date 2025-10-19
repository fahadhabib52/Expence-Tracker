<?php
require_once 'connection.php';
require_once 'includes/functions.php';
require_login();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: view_expenses.php");
    exit();
}

$expense_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// ✅ Fetch current expense
$sql = "SELECT * FROM expenses WHERE expense_id = $expense_id AND user_id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: view_expenses.php");
    exit();
}

$expense = $result->fetch_assoc();

// ✅ Update expense
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = $conn->real_escape_string($_POST['category']);
    $amount = $conn->real_escape_string($_POST['amount']);
    $description = $conn->real_escape_string($_POST['description']);
    $date = $conn->real_escape_string($_POST['date']);

    $update_sql = "UPDATE expenses 
                   SET category='$category', amount='$amount', description='$description', date='$date' 
                   WHERE expense_id=$expense_id AND user_id=$user_id";

    if ($conn->query($update_sql)) {
        header("Location: view_expenses.php?msg=updated");
        exit();
    } else {
        $error = "Kuch galat ho gaya. Try again!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Expense</title>
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            background: #fff;
            margin: 60px auto;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            margin-top: 20px;
        }
        label {
            font-weight: 600;
            display: block;
            margin-bottom: 6px;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 15px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #3498db;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background: #2980b9;
        }
        .back {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #555;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>✏ Edit Expense</h2>
    <?php if (isset($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Category</label>
        <select name="category" required>
            <option value="Food" <?= $expense['category'] == 'Food' ? 'selected' : '' ?>>Food</option>
            <option value="Transport" <?= $expense['category'] == 'Transport' ? 'selected' : '' ?>>Transport</option>
            <option value="Shopping" <?= $expense['category'] == 'Shopping' ? 'selected' : '' ?>>Shopping</option>
            <option value="Bills" <?= $expense['category'] == 'Bills' ? 'selected' : '' ?>>Bills</option>
            <option value="Entertainment" <?= $expense['category'] == 'Entertainment' ? 'selected' : '' ?>>Entertainment</option>
            <option value="Other" <?= $expense['category'] == 'Other' ? 'selected' : '' ?>>Other</option>
        </select>

        <label>Amount (Rs)</label>
        <input type="number" name="amount" value="<?= $expense['amount'] ?>" required step="0.01">

        <label>Description</label>
        <textarea name="description" rows="3"><?= htmlspecialchars($expense['description']) ?></textarea>

        <label>Date</label>
        <input type="date" name="date" value="<?= $expense['date'] ?>" required>

        <button type="submit">💾 Update Expense</button>
    </form>

    <a href="view_expenses.php" class="back">⬅ Back to Expenses</a>
</div>

</body>
</html>
