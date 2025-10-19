<?php
require_once 'connection.php';
require_once 'includes/functions.php';
require_login();

$user_id = $_SESSION['user_id'];

// ✅ Delete expense if requested
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM expenses WHERE expense_id=$delete_id AND user_id=$user_id");
    header("Location: view_expenses.php?msg=deleted");
    exit();
}

// ✅ Fetch all expenses for current user
$sql = "SELECT * FROM expenses WHERE user_id=$user_id ORDER BY date DESC";
$result = $conn->query($sql);

// ✅ Handle message alerts
$msg = "";
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == "deleted") $msg = "Expense deleted successfully ✅";
    if ($_GET['msg'] == "updated") $msg = "Expense updated successfully ✏️";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Expenses</title>
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: #f4f7fb;
  margin: 0;
  padding: 0;
}
header {
  background: linear-gradient(90deg, #007bff, #00b4d8);
  color: white;
  padding: 15px 25px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
header h1 { margin: 0; font-size: 22px; }
header nav a {
  color: white;
  text-decoration: none;
  margin-left: 15px;
  font-weight: 500;
  transition: 0.3s;
}
header nav a:hover { text-decoration: underline; }

.container {
  max-width: 1000px;
  margin: 40px auto;
  background: white;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

h2 {
  text-align: center;
  color: #333;
  margin-bottom: 20px;
}

.message {
  text-align: center;
  background: #d4edda;
  color: #155724;
  padding: 10px;
  border-radius: 6px;
  margin-bottom: 15px;
  font-weight: 500;
  animation: fadeIn 0.8s ease-in;
}

@keyframes fadeIn {
  from {opacity: 0;}
  to {opacity: 1;}
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
}

th, td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

th {
  background: #007bff;
  color: white;
}

tr:hover {
  background: #f1f7ff;
}

.action-btns {
  display: flex;
  gap: 8px;
}

.edit-btn, .delete-btn {
  padding: 6px 10px;
  border-radius: 6px;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  transition: 0.3s;
}

.edit-btn {
  background: #28a745;
  color: white;
}
.edit-btn:hover {
  background: #218838;
}

.delete-btn {
  background: #dc3545;
  color: white;
}
.delete-btn:hover {
  background: #c82333;
}

.back-btn {
  display: inline-block;
  margin-top: 20px;
  text-decoration: none;
  background: #007bff;
  color: white;
  padding: 10px 18px;
  border-radius: 8px;
  transition: 0.3s;
}
.back-btn:hover {
  background: #0056b3;
}

.no-records {
  text-align: center;
  color: #777;
  font-style: italic;
  margin-top: 20px;
}
</style>
</head>
<body>

<header>
  <h1>Expense Tracker</h1>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="add_expense.php">Add Expense</a>
    <a href="set_budget.php">Set Budget</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<div class="container">
  <h2>🧾 Your Expenses</h2>

  <?php if ($msg): ?>
    <div class="message"><?= $msg ?></div>
  <?php endif; ?>

  <?php if ($result->num_rows > 0): ?>
  <table>
    <tr>
      <th>#</th>
      <th>Category</th>
      <th>Amount (Rs)</th>
      <th>Description</th>
      <th>Date</th>
      <th>Actions</th>
    </tr>
    <?php $i=1; while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $i++ ?></td>
        <td><?= htmlspecialchars($row['category']) ?></td>
        <td><?= number_format($row['amount'], 2) ?></td>
        <td><?= htmlspecialchars($row['description']) ?></td>
        <td><?= htmlspecialchars($row['date']) ?></td>
        <td class="action-btns">
          <a href="edit_expense.php?id=<?= $row['expense_id'] ?>" class="edit-btn">Edit</a>
          <a href="view_expenses.php?delete_id=<?= $row['expense_id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this expense?')">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
  <?php else: ?>
    <p class="no-records">No expenses found yet.</p>
  <?php endif; ?>

  <a href="dashboard.php" class="back-btn">⬅ Back to Dashboard</a>
</div>

</body>
</html>
