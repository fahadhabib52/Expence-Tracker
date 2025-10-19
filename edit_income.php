<?php
require_once 'connection.php';
require_once 'includes/functions.php';
require_login();

$user_id = $_SESSION['user_id'];
$message = "";

// ✅ Income ID from URL
if (!isset($_GET['id'])) {
    header("Location: view_income.php");
    exit();
}

$income_id = intval($_GET['id']);

// ✅ Fetch existing income data
$stmt = $conn->prepare("SELECT * FROM income WHERE income_id=? AND user_id=?");
$stmt->bind_param("ii", $income_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<h3 style='text-align:center;color:red;'>Income not found!</h3>";
    exit();
}

$income = $result->fetch_assoc();

// ✅ Update record
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $source = $_POST['source'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $date = $_POST['date'];

    $update = $conn->prepare("UPDATE income SET source=?, amount=?, description=?, date=? WHERE income_id=? AND user_id=?");
    $update->bind_param("sdssii", $source, $amount, $description, $date, $income_id, $user_id);

    if ($update->execute()) {
        $message = "✅ Income updated successfully!";
        // Update local variable for instant display refresh
        $income['source'] = $source;
        $income['amount'] = $amount;
        $income['description'] = $description;
        $income['date'] = $date;
    } else {
        $message = "❌ Failed to update income!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Income</title>
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
  max-width: 600px;
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
form {
  display: flex;
  flex-direction: column;
}
label {
  margin-top: 10px;
  font-weight: 500;
  color: #333;
}
input, textarea {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
  margin-top: 5px;
}
button {
  margin-top: 20px;
  background: #007bff;
  color: white;
  border: none;
  padding: 12px;
  border-radius: 8px;
  font-size: 16px;
  cursor: pointer;
  font-weight: 500;
}
button:hover { background: #0056b3; }
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
  <h1>Edit Income</h1>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="view_income.php">View Income</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<div class="container">
  <h2>📝 Edit Income Record</h2>

  <?php if($message): ?>
    <div class="message"><?= $message ?></div>
  <?php endif; ?>

  <form method="POST">
    <label for="source">Source</label>
    <input type="text" id="source" name="source" value="<?= htmlspecialchars($income['source']) ?>" required>

    <label for="amount">Amount (Rs)</label>
    <input type="number" step="0.01" id="amount" name="amount" value="<?= htmlspecialchars($income['amount']) ?>" required>

    <label for="description">Description</label>
    <textarea id="description" name="description" rows="3"><?= htmlspecialchars($income['description']) ?></textarea>

    <label for="date">Date</label>
    <input type="date" id="date" name="date" value="<?= htmlspecialchars($income['date']) ?>" required>

    <button type="submit">Update Income</button>
  </form>
</div>
</body>
</html>
