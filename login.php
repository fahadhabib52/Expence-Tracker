<?php
require_once 'connection.php';
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($email === '' || $password === '') {
        $errors[] = "Please fill all fields.";
    } else {
        $stmt = $conn->prepare("SELECT user_id, name, password FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['user_name'] = $row['name'];
                header("Location: dashboard.php");
                exit();
            } else {
                $errors[] = "Invalid email or password.";
            }
        } else {
            $errors[] = "Invalid email or password.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - Expense Tracker</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body {
  font-family: "Poppins", sans-serif;
  background: linear-gradient(135deg, #ff512f, #dd2476);
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  margin: 0;
}
.card {
  background: #ffffff;
  padding: 35px 45px;
  border-radius: 20px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.2);
  width: 360px;
}
h2 {
  text-align: center;
  color: #333;
  margin-bottom: 25px;
  font-weight: 600;
}
input {
  width: 100%;
  padding: 12px;
  margin: 10px 0;
  border: 1px solid #ccc;
  border-radius: 10px;
  font-size: 15px;
}
button {
  width: 100%;
  background: #dd2476;
  color: white;
  border: none;
  padding: 12px;
  border-radius: 10px;
  cursor: pointer;
  font-size: 16px;
  transition: background 0.3s ease;
}
button:hover { background: #c01f67; }
p {
  text-align: center;
  margin-top: 15px;
  color: #555;
}
a { color: #dd2476; text-decoration: none; }
a:hover { text-decoration: underline; }
.error {
  color: red;
  background: #ffe6e6;
  padding: 8px;
  border-radius: 8px;
  text-align: center;
}
.success {
  color: green;
  background: #e7ffe7;
  padding: 8px;
  border-radius: 8px;
  text-align: center;
}
</style>
</head>
<body>
<div class="card">
  <h2>Welcome Back 👋</h2>
  
  <?php if (isset($_GET['registered'])): ?>
    <p class="success">Registration successful! Please login.</p>
  <?php endif; ?>

  <?php if ($errors): ?>
    <?php foreach($errors as $e) echo "<p class='error'>$e</p>"; ?>
  <?php endif; ?>

  <form method="post">
    <input type="email" name="email" placeholder="Email Address" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
  </form>
  
  <p>Don't have an account? <a href="register.php">Register</a></p>
</div>
</body>
</html>
