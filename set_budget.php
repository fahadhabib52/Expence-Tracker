<?php
session_start();
require_once 'connection.php';

// ✅ Agar user login nahi to redirect
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_msg = "";

// ✅ Form submit hone pe data insert
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = trim($_POST['category']);
    $month_year = trim($_POST['month_year']);
    $limit_amount = floatval($_POST['limit_amount']);

    if (!empty($category) && !empty($month_year) && $limit_amount > 0) {
        $stmt = $conn->prepare("INSERT INTO budget (user_id, category, month_year, limit_amount, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("issd", $user_id, $category, $month_year, $limit_amount);
        $stmt->execute();
        $success_msg = "✅ Budget set successfully for $category ($month_year)!";
    } else {
        $success_msg = "⚠️ Please fill all fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Set Monthly Budget</title>
<style>
body {
  font-family: 'Segoe UI', sans-serif;
  background: #f2f4f8;
  margin: 0;
  padding: 40px;
}
.container {
  background: white;
  max-width: 600px;
  margin: auto;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  padding: 30px;
}
h2 {
  text-align: center;
  color: #333;
  margin-bottom: 20px;
}
form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}
label {
  font-weight: bold;
}
input, select {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 15px;
}
button {
  background: #007bff;
  color: white;
  padding: 10px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 16px;
}
button:hover {
  background: #0056b3;
}
.success {
  text-align: center;
  background: #d4edda;
  color: #155724;
  padding: 10px;
  border-radius: 8px;
  margin-bottom: 15px;
}
.error {
  text-align: center;
  background: #f8d7da;
  color: #721c24;
  padding: 10px;
  border-radius: 8px;
  margin-bottom: 15px;
}
</style>
</head>
<body>

<div class="container">
  <h2>🎯 Set Monthly Budget</h2>

  <?php if ($success_msg): ?>
    <div class="<?= strpos($success_msg, '⚠️') !== false ? 'error' : 'success' ?>">
      <?= $success_msg ?>
    </div>
  <?php endif; ?>
  
<form method="POST">
  <label>Category</label>
  <select name="category" required>
    <option value="">-- Select Category --</option>
    <option value="Food">Food</option>
    <option value="Travel">Travel</option>
    <option value="Shopping">Shopping</option>
    <option value="Bills">Bills</option>
    <option value="Other">Other</option>
  </select>

  <label>Month (YYYY-MM)</label>
  <input type="month" name="month_year" required>

  <label>Budget Limit (Rs)</label>
  <input type="number" name="limit_amount" min="1" step="0.01" required>

  <div style="text-align:center; margin-top:15px;">
    <button type="submit" style="background:#007bff;color:white;border:none;padding:10px 20px;border-radius:5px;cursor:pointer;">
      💾 Save Budget
    </button>
    <a href="dashboard.php" style="background:#28a745;color:white;text-decoration:none;padding:10px 20px;border-radius:5px;margin-left:10px;">
      🏠 Go to Dashboard
    </a>
  </div>
</form>

</div>

</body>
</html>
