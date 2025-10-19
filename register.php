<?php
require_once 'connection.php';
session_start();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm']);

      header("Location: dashboard.php");

    if ($name === '' || $email === '' || $password === '') {
        $errors[] = "All fields are required.";
    } elseif ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hash);
        if ($stmt->execute()) {
            $success = true;
        } else {
            $errors[] = "Email already exists or registration failed.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register - Expense Tracker</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body {
  font-family: "Poppins", sans-serif;
  background: linear-gradient(135deg, #6a11cb, #2575fc);
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
  background: #2575fc;
  color: white;
  border: none;
  padding: 12px;
  border-radius: 10px;
  cursor: pointer;
  font-size: 16px;
  transition: background 0.3s ease;
}
button:hover { background: #1a5edc; }
p {
  text-align: center;
  margin-top: 15px;
  color: #555;
}
a { color: #2575fc; text-decoration: none; }
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
  <h2>Create Account</h2>
  
  <?php if ($success): ?>
    <p class="success">Registration successful! <a href="login.php">Login now</a></p>
  <?php elseif ($errors): ?>
    <?php foreach($errors as $e) echo "<p class='error'>$e</p>"; ?>
  <?php endif; ?>

  <form method="post">
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email Address" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="password" name="confirm" placeholder="Confirm Password" required>
    <button type="submit">Register</button>
  </form>
  
  <p>Already have an account? <a href="login.php">Login</a></p>
</div>
</body>
</html>
