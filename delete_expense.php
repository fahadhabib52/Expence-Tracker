<?php
require_once 'connection.php';
require_once 'includes/functions.php';
require_login();

if (isset($_GET['id'])) {
    $expense_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Sirf us user ka expense delete hoga jo login hai
    $sql = "DELETE FROM expenses WHERE expense_id = $expense_id AND user_id = $user_id";
    if ($conn->query($sql)) {
        header("Location: view_expenses.php?msg=deleted");
        exit();
    } else {
        echo "Error deleting expense: " . $conn->error;
    }
} else {
    header("Location: view_expenses.php");
    exit();
}
?>
