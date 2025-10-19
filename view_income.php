<?php
require_once 'connection.php';
require_once 'includes/functions.php';
require_login();

$user_id = $_SESSION['user_id'];
$message = "";

// ✅ Delete income record
if (isset($_GET['delete'])) {
    $income_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM income WHERE income_id=? AND user_id=?");
    $stmt->bind_param("ii", $income_id, $user_id);
    if ($stmt->execute()) {
        $message = "✅ Income deleted successfully!";
    } else {
        $message = "❌ Failed to delete income!";
    }
}

// ✅ Fetch all incomes
$result = $conn->query("SELECT * FROM income WHERE user_id=$user_id ORDER BY date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Income</title>
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: #eef2f7;
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
  color: white; text-decoration: none; margin-left: 15px; font-weight: 500;
}
.container {
  max-width: 900px;
  margin: 40px auto;
  background: white;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
h2 {
  text-align: center;
  color: #333;
  margin-bottom: 20px;
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
tr:hover { background: #f9f9f9; }
.btn {
  padding: 6px 12px;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 500;
  font-size: 14px;
}
.edit-btn {
  background: #28a745;
  color: white;
}
.delete-btn {
  background: #dc3545;
  color: white;
}
.edit-btn:hover { background: #218838; }
.delete-btn:hover { background: #c82333; }
.message {
  text-align: center;
  margin-bottom: 15px;
  padding: 10px;
  border-radius: 6px;
  background: #d1ecf1;
  color: #0c5460;
  font-weight: 500;
}
.no-data {
  text-align: center;
  padding: 20px;
  color: #666;
}
.add-btn {
  display: inline-block;
  background: #007bff;
  color: white;
  padding: 10px 15px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 500;
  margin-bottom: 15px;
}
.add-btn:hover { background: #0056b3; }
</style>
</head>
<body>
<header>
  <h1>View Income</h1>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="add_income.php">Add Income</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<div class="container">
  <h2>💰 Your Income Records</h2>

  <?php if($message): ?>
    <div class="message"><?= $message ?></div>
  <?php endif; ?>

  <a href="add_income.php" class="add-btn">➕ Add New Income</a>

  <?php if ($result->num_rows > 0): ?>
  <table>
    <tr>
      <th>Source</th>
      <th>Amount (Rs)</th>
      <th>Description</th>
      <th>Date</th>
      <th>Actions</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($row['source']) ?></td>
      <td><?= number_format($row['amount'], 2) ?></td>
      <td><?= htmlspecialchars($row['description']) ?></td>
      <td><?= htmlspecialchars($row['date']) ?></td>
      <td>
        <a href="edit_income.php?id=<?= $row['income_id'] ?>" class="btn edit-btn">Edit</a>
        <a href="?delete=<?= $row['income_id'] ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this income?');">Delete</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
  <?php else: ?>
    <p class="no-data">No income records found yet.</p>
  <?php endif; ?>
</div>

</body>
</html>
