<?php
include 'connection.php';
$user_id = 1; // temporary test

$q = $conn->query("SELECT COALESCE(SUM(amount),0) AS total FROM expenses WHERE user_id=$user_id");

if (!$q) {
    echo "Error: " . $conn->error;
} else {
    $r = $q->fetch_assoc();
    echo "Total Expense = " . $r['total'];
}
?>
