<?php
$host = "localhost";
$user = "root"; // default in XAMPP
$pass = "";
$dbname = "expence_tracker"; // ✅ new DB name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
