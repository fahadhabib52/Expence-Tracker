<?php
require_once 'connection.php';
require_once 'includes/functions.php';
require_login();

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? "User";

// ✅ Fetch totals
$total_expense = $conn->query("SELECT COALESCE(SUM(amount),0) AS total FROM expenses WHERE user_id=$user_id")->fetch_assoc()['total'];
$total_income = $conn->query("SELECT COALESCE(SUM(amount),0) AS total FROM income WHERE user_id=$user_id")->fetch_assoc()['total'];
$balance = $total_income - $total_expense;

// ✅ Fetch current month budget (total + category-wise)
$current_month = date('Y-m');
$budget_sql = $conn->prepare("SELECT category, limit_amount FROM budget WHERE user_id=? AND month_year=?");
$budget_sql->bind_param("is", $user_id, $current_month);
$budget_sql->execute();
$budget_result = $budget_sql->get_result();

$budget_total = 0;
$budget_breakdown = [];
while ($row = $budget_result->fetch_assoc()) {
  $budget_total += $row['limit_amount'];
  $budget_breakdown[] = $row;
}

// ✅ Category-wise expense (for chart)
$category_data = $conn->query("SELECT category, SUM(amount) AS total FROM expenses WHERE user_id=$user_id GROUP BY category");

$categories = [];
$amounts = [];
while ($row = $category_data->fetch_assoc()) {
  $categories[] = $row['category'];
  $amounts[] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard - Expense Tracker</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  body {
    font-family: 'Poppins', sans-serif;
    background: #f6f7fb;
    margin: 0;
  }
  header {
    background: linear-gradient(90deg,#007bff,#00b4d8);
    color: white;
    padding: 15px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
  }
  header h1 { font-size: 22px; margin: 0; }
  header a {
    color: white; text-decoration: none; margin-left: 15px; font-weight: 500;
  }
  .container {
    max-width: 1150px;
    margin: 30px auto;
    padding: 20px;
  }
  .grid {
    display: grid;
    grid-template-columns: repeat(auto-fit,minmax(250px,1fr));
    gap: 20px;
  }
  .card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    text-align: center;
    transition: 0.3s;
  }
  .card:hover { transform: translateY(-5px); }
  .card h3 { color: #555; margin-bottom: 10px; }
  .big { font-size: 26px; font-weight: bold; color: #007bff; }
  .chart-box {
    background: white;
    padding: 25px;
    border-radius: 12px;
    margin-top: 30px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
  }
  .alert, .success {
    padding: 12px;
    border-radius: 8px;
    text-align: center;
    margin-top: 15px;
    font-weight: 500;
  }
  .alert { background: #ffe5e5; color: #d00000; }
  .success { background: #e6ffe6; color: #008000; }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
  }
  th, td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
  }
  th { background-color: #007bff; color: white; }
  tr:nth-child(even) { background-color: #f9f9f9; }
</style>
</head>
<body>
<header>
  <h1>Welcome, <?= htmlspecialchars($user_name) ?></h1>
  <nav>
    <a href="add_expense.php">Add Expense</a>
    <a href="view_expenses.php">View Expenses</a>
    <a href="set_budget.php">Set Budget</a>
    <a href="add_income.php">Add Income</a>
    <a href="view_income.php">View Income</a>
    <a href="summary.php">Total Summary</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<div class="container">
  <div class="grid">
    <div class="card">
      <h3>Total Income</h3>
      <p class="big">Rs <?= number_format($total_income,2) ?></p>
    </div>
    <div class="card">
      <h3>Total Expense</h3>
      <p class="big">Rs <?= number_format($total_expense,2) ?></p>
    </div>
    <div class="card">
      <h3>Balance</h3>
      <p class="big" style="color:<?= $balance>=0?'green':'red' ?>;">Rs <?= number_format($balance,2) ?></p>
    </div>
    <div class="card">
      <h3>Total Budget</h3>
      <p class="big">Rs <?= number_format($budget_total,2) ?></p>
    </div>
  </div>

  <?php
  if ($budget_total > 0) {
      $spent_percentage = ($total_expense / $budget_total) * 100;
      if ($spent_percentage >= 100) {
          echo '<div class="alert">⚠️ You have exceeded your monthly budget!</div>';
      } elseif ($spent_percentage >= 80) {
          echo '<div class="alert">⚠️ You have used '.round($spent_percentage).'% of your budget!</div>';
      } else {
          echo '<div class="success">✅ You are within your budget. Great job!</div>';
      }
  } else {
      echo '<div class="alert">⚠️ No budget set for this month.</div>';
  }
  ?>

  <!-- ✅ Category-wise Budget Breakdown -->
  <div class="chart-box">
    <h3 style="text-align:center;">Budget Overview (Category-wise)</h3>
    <?php if (count($budget_breakdown) > 0): ?>
      <table>
        <tr>
          <th>Category</th>
          <th>Limit (Rs)</th>
          <th>Month</th>
        </tr>
        <?php foreach ($budget_breakdown as $b): ?>
          <tr>
            <td><?= htmlspecialchars($b['category']) ?></td>
            <td><?= number_format($b['limit_amount'],2) ?></td>
            <td><?= htmlspecialchars($current_month) ?></td>
          </tr>
        <?php endforeach; ?>
        <tr style="font-weight:bold; background:#e3f2fd;">
          <td>Total</td>
          <td colspan="2">Rs <?= number_format($budget_total,2) ?></td>
        </tr>
      </table>
    <?php else: ?>
      <p style="text-align:center;">No budget records found for this month.</p>
    <?php endif; ?>
  </div>

  <div class="chart-box">
    <h3 style="text-align:center;">Spending by Category</h3>
    <canvas id="expenseChart"></canvas>
  </div>

  <div class="chart-box">
    <h3 style="text-align:center;">Income vs Expense</h3>
    <canvas id="incomeExpenseChart"></canvas>
  </div>
</div>

<script>
const categoryCtx = document.getElementById('expenseChart');
new Chart(categoryCtx, {
  type: 'doughnut',
  data: {
    labels: <?= json_encode($categories) ?>,
    datasets: [{
      data: <?= json_encode($amounts) ?>,
      backgroundColor: ['#007bff','#00b4d8','#90e0ef','#ffb703','#fb8500','#e63946','#8ecae6','#219ebc']
    }]
  },
  options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

const barCtx = document.getElementById('incomeExpenseChart');
new Chart(barCtx, {
  type: 'bar',
  data: {
    labels: ['Income', 'Expense', 'Budget'],
    datasets: [{
      label: 'Amount (Rs)',
      data: [<?= $total_income ?>, <?= $total_expense ?>, <?= $budget_total ?>],
      backgroundColor: ['#38b000','#e63946','#0077b6']
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true } }
  }
});
</script>
</body>
</html>
