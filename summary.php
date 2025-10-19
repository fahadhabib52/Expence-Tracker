<?php
require_once 'connection.php';
require_once 'includes/functions.php';
require_login();

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? "User";

// ✅ Income Details
$income_query = $conn->query("SELECT source, amount, description, date FROM income WHERE user_id=$user_id ORDER BY date DESC");
$total_income = 0;
if ($income_query) {
  while ($row = $income_query->fetch_assoc()) {
    $income_data[] = $row;
    $total_income += $row['amount'];
  }
}

// ✅ Expense Details
$expense_query = $conn->query("SELECT category, amount, description, date FROM expenses WHERE user_id=$user_id ORDER BY date DESC");
$total_expense = 0;
if ($expense_query) {
  while ($row = $expense_query->fetch_assoc()) {
    $expense_data[] = $row;
    $total_expense += $row['amount'];
  }
}

// ✅ Budget
$budget_query = $conn->query("SELECT category, limit_amount, month_year FROM budget WHERE user_id=$user_id ORDER BY created_at DESC");
$total_budget = 0;
if ($budget_query) {
  while ($row = $budget_query->fetch_assoc()) {
    $budget_data[] = $row;
    $total_budget += $row['limit_amount'];
  }
}

$balance = $total_income - $total_expense;
$remaining_budget = $total_budget - $total_expense;
$percent_used = ($total_budget > 0) ? ($total_expense / $total_budget) * 100 : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Detailed Summary - Expense Tracker</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: #eef2f7;
  margin: 0;
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
  transition: 0.3s;
}
header nav a:hover { text-decoration: underline; }

.container {
  max-width: 900px;
  background: white;
  margin: 40px auto;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
h2 {
  text-align: center;
  color: #007bff;
  margin-bottom: 25px;
}
section {
  margin-bottom: 30px;
}
h3 {
  color: #007bff;
  border-bottom: 2px solid #007bff;
  padding-bottom: 5px;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}
th, td {
  border: 1px solid #ddd;
  padding: 10px;
  text-align: left;
}
th {
  background: #007bff;
  color: white;
}
tr:nth-child(even) { background: #f9f9f9; }

.summary-box {
  background: #f8f9fa;
  padding: 15px;
  border-radius: 8px;
  line-height: 1.8;
}
.download-btn {
  display: block;
  margin: 30px auto 0;
  background: #007bff;
  color: white;
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 16px;
  transition: 0.3s;
}
.download-btn:hover {
  background: #0056b3;
  transform: scale(1.05);
}
.alert {
  background: #ffe6e6;
  color: #d00000;
  padding: 12px;
  border-radius: 8px;
  text-align: center;
  font-weight: 500;
  margin-top: 15px;
}
</style>
</head>
<body>
<header>
  <h1>Welcome, <?= htmlspecialchars($user_name) ?></h1>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="view_expenses.php">Expenses</a>
    <a href="view_income.php">Income</a>
    <a href="set_budget.php">Budget</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<div class="container" id="summaryArea">
  <h2>📄 Detailed Financial Summary</h2>

  <section>
    <h3>💰 Income Summary</h3>
    <?php if (!empty($income_data)): ?>
      <table>
        <tr><th>Source</th><th>Amount (Rs)</th><th>Description</th><th>Date</th></tr>
        <?php foreach ($income_data as $i): ?>
          <tr>
            <td><?= htmlspecialchars($i['source']) ?></td>
            <td><?= number_format($i['amount'],2) ?></td>
            <td><?= htmlspecialchars($i['description']) ?></td>
            <td><?= htmlspecialchars($i['date']) ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php else: ?>
      <p>No income records found.</p>
    <?php endif; ?>
  </section>

  <section>
    <h3>💸 Expense Summary</h3>
    <?php if (!empty($expense_data)): ?>
      <table>
        <tr><th>Category</th><th>Amount (Rs)</th><th>Description</th><th>Date</th></tr>
        <?php foreach ($expense_data as $e): ?>
          <tr>
            <td><?= htmlspecialchars($e['category']) ?></td>
            <td><?= number_format($e['amount'],2) ?></td>
            <td><?= htmlspecialchars($e['description']) ?></td>
            <td><?= htmlspecialchars($e['date']) ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php else: ?>
      <p>No expense records found.</p>
    <?php endif; ?>
  </section>

  <section>
    <h3>🎯 Budget Overview</h3>
    <?php if (!empty($budget_data)): ?>
      <table>
        <tr><th>Category</th><th>Limit (Rs)</th><th>Month</th></tr>
        <?php foreach ($budget_data as $b): ?>
          <tr>
            <td><?= htmlspecialchars($b['category']) ?></td>
            <td><?= number_format($b['limit_amount'],2) ?></td>
            <td><?= htmlspecialchars($b['month_year']) ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php else: ?>
      <p>No budget records found.</p>
    <?php endif; ?>
  </section>

  <section>
    <h3>📊 Summary Stats</h3>
    <div class="summary-box">
      <p><strong>Total Income:</strong> Rs <?= number_format($total_income,2) ?></p>
      <p><strong>Total Expenses:</strong> Rs <?= number_format($total_expense,2) ?></p>
      <p><strong>Total Budget:</strong> Rs <?= number_format($total_budget,2) ?></p>
      <p><strong>Remaining Budget:</strong> Rs <?= number_format($remaining_budget,2) ?></p>
      <p><strong>Overall Balance:</strong> Rs <?= number_format($balance,2) ?></p>
      <p><strong>Budget Used:</strong> <?= round($percent_used) ?>%</p>
    </div>
  </section>

  <?php if ($percent_used > 80): ?>
    <div class="alert">⚠️ You’ve used <?= round($percent_used) ?>% of your budget! Consider cutting down some expenses.</div>
  <?php endif; ?>

  <button id="downloadBtn" class="download-btn">⬇ Download Full Summary (PDF)</button>
</div>

<script>
document.getElementById('downloadBtn').addEventListener('click', () => {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF({ orientation: 'portrait', unit: 'pt', format: 'a4' });

  doc.setFontSize(18);
  doc.text("Detailed Expense Summary", 200, 40);

  const summary = document.getElementById('summaryArea').innerText;
  doc.setFontSize(12);
  doc.text(summary, 40, 70, { maxWidth: 520 });

  doc.save("Detailed_Expense_Summary.pdf");
});
</script>
</body>
</html>
