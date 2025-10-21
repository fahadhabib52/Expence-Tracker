<?php
require_once 'connection.php';
require_once 'includes/functions.php';
require_login();

$user_id = $_SESSION['user_id'];
$query = $conn->query("SELECT * FROM money_lent WHERE user_id=$user_id ORDER BY date_lent DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Loans - Expense Tracker</title>
<style>
body { font-family:'Poppins',sans-serif; background:#eef2f7; margin:0; }
header { background:linear-gradient(90deg,#007bff,#00b4d8); color:white; padding:15px 25px; display:flex; justify-content:space-between; align-items:center; }
header h1 { margin:0; font-size:22px; }
header nav a { color:white; text-decoration:none; margin-left:15px; }
.container {
  max-width:900px; margin:40px auto; background:white;
  padding:25px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1);
}
table { width:100%; border-collapse:collapse; margin-top:10px; }
th, td { border:1px solid #ddd; padding:10px; text-align:left; }
th { background:#007bff; color:white; }
tr:nth-child(even){ background:#f9f9f9; }
.reminder { background:#fff3cd; color:#856404; font-weight:500; padding:5px 10px; border-radius:6px; }
</style>
</head>
<body>

<header>
  <h1>💵 Loan Records</h1>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="add_loan.php">Add New Loan</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<div class="container">
  <h2>Your Loan / Udhar Records</h2>
  <?php if ($query->num_rows > 0): ?>
  <table>
    <tr>
      <th>Person</th>
      <th>Amount (Rs)</th>
      <th>Reason</th>
      <th>Date Lent</th>
      <th>Due Date</th>
      <th>Status</th>
      <th>Reminder</th>
    </tr>
    <?php while($row = $query->fetch_assoc()): ?>
      <?php
        $today = date('Y-m-d');
        $reminder = "";
        if (!empty($row['reminder_date']) && $today >= $row['reminder_date']) {
          $reminder = "<span class='reminder'>🔔 Reminder: Contact {$row['person_name']}</span>";
        }
      ?>
      <tr>
        <td><?= htmlspecialchars($row['person_name']) ?></td>
        <td><?= number_format($row['amount'],2) ?></td>
        <td><?= htmlspecialchars($row['reason']) ?></td>
        <td><?= htmlspecialchars($row['date_lent']) ?></td>
        <td><?= htmlspecialchars($row['due_date']) ?></td>
        <td><?= ucfirst($row['status']) ?></td>
        <td><?= $reminder ?></td>
      </tr>
    <?php endwhile; ?>
  </table>
  <?php else: ?>
    <p>No loans recorded yet.</p>
  <?php endif; ?>
</div>
</body>
</html>
