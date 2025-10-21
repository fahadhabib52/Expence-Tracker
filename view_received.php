<?php
require_once 'connection.php';
require_once 'includes/functions.php';
require_login();

$user_id = $_SESSION['user_id'];
$query = $conn->query("SELECT * FROM money_received WHERE user_id=$user_id ORDER BY date_received DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Money Received - Expense Tracker</title>
<style>
body { font-family:'Poppins',sans-serif; background:#eef2f7; margin:0; }
header { background:linear-gradient(90deg,#00b4d8,#007bff); color:white; padding:15px 25px; display:flex; justify-content:space-between; align-items:center; }
header h1 { margin:0; font-size:22px; }
header nav a { color:white; text-decoration:none; margin-left:15px; }
.container {
  max-width:900px; margin:40px auto; background:white;
  padding:25px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1);
}
table { width:100%; border-collapse:collapse; margin-top:10px; }
th, td { border:1px solid #ddd; padding:10px; text-align:left; }
th { background:#00b4d8; color:white; }
tr:nth-child(even){ background:#f9f9f9; }
</style>
</head>
<body>

<header>
  <h1>💰 Money Received Records</h1>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="add_received.php">Add New</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<div class="container">
  <h2>All Received Money Records</h2>
  <?php if ($query->num_rows > 0): ?>
  <table>
    <tr>
      <th>Person</th>
      <th>Amount (Rs)</th>
      <th>Reason</th>
      <th>Date Received</th>
      <th>Note</th>
    </tr>
    <?php while($row = $query->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['person_name']) ?></td>
        <td><?= number_format($row['amount'],2) ?></td>
        <td><?= htmlspecialchars($row['reason']) ?></td>
        <td><?= htmlspecialchars($row['date_received']) ?></td>
        <td><?= htmlspecialchars($row['note']) ?></td>
      </tr>
    <?php endwhile; ?>
  </table>
  <?php else: ?>
    <p>No money received records yet.</p>
  <?php endif; ?>
</div>
</body>
</html>
