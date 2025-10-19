<?php
require_once 'connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "⚠️ Please login first.";
    exit;
}

$user_id = $_SESSION['user_id'];
$category = $_POST['category'];
$amount = $_POST['amount'];
$description = $_POST['description'];
$date = $_POST['date'];

if (empty($category) || empty($amount) || empty($date)) {
    echo "❌ Please fill all required fields.";
    exit;
}

$sql = "INSERT INTO expenses (user_id, category, amount, description, date, created_at)
        VALUES ('$user_id', '$category', '$amount', '$description', '$date', NOW())";

if ($conn->query($sql)) {
    echo "✅ Expense added successfully!";
} else {
    echo "❌ Error adding expense: " . $conn->error;
}
?>
