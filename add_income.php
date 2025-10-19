<?php
require_once 'connection.php';
require_once 'includes/functions.php';
require_login();

$user_id = $_SESSION['user_id'];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $source = trim($_POST['source']);
    $amount = floatval($_POST['amount']);
    $description = trim($_POST['description']);
    $date = $_POST['date'];

    if (!empty($source) && $amount > 0 && !empty($date)) {
        $sql = "INSERT INTO income (user_id, source, amount, description, date, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isdss", $user_id, $source, $amount, $description, $date);
        if ($stmt->execute()) {
            $message = "✅ Income added successfully!";
        } else {
            $message = "❌ Failed to add income!";
        }
    } else {
        $message = "⚠️ Please fill all required fields!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Income</title>
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
}
header h1 { margin: 0; font-size: 22px; }
header nav a {
  color: white; text-decoration: none; margin-left: 15px; font-weight: 500;
}
.container {
  max-width: 600px;
  margin: 40px auto;
  background: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
h2 {
  text-align: center;
  color: #333;
}
form label {
  display: block;
  margin-top: 15px;
  font-weight: 500;
  color: #333;
}
form input, textarea {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
  margin-top: 5px;
}
button {
  background: #007bff;
  color: white;
  border: none;
  padding: 10px 18px;
  margin-top: 20px;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: 0.3s;
}
button:hover {
  background: #0056b3;
}
.message {
  text-align: center;
  margin-bottom: 15px;
  padding: 10px;
  border-radius: 6px;
  background: #d1ecf1;
  color: #0c5460;
  font-weight: 500;
}
</style>
</head>
<body>
<header>
  <h1>Add Income</h1>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="view_income.php">View Income</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<div class="container">
  <h2>💰 Add New Income</h2>

  <?php if($message): ?>
    <div class="message"><?= $message ?></div>
  <?php endif; ?>
<form method="POST">
  <label>Income Source</label>
  <input type="text" name="source" placeholder="e.g. Salary, Freelancing" required>

  <label>Amount (Rs)</label>
  <input type="number" name="amount" step="0.01" required>

  <label>Description</label>
  <textarea name="description" rows="3" placeholder="Optional details"></textarea>

  <label>Date</label>
  <input type="date" name="date" required>

  <div style="text-align:center; margin-top:15px;">
    <button type="submit" style="background:#007bff;color:white;border:none;padding:10px 20px;border-radius:5px;cursor:pointer;">
      💰 Add Income
    </button>
    <a href="dashboard.php" style="background:#28a745;color:white;text-decoration:none;padding:10px 20px;border-radius:5px;margin-left:10px;">
      🏠 Go to Dashboard
    </a>
  </div>
</form>

</div>

</body>
</html>
