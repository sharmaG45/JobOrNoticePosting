<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: userLogin.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
   
    $id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

   
    $stmt = $conn->prepare("DELETE FROM notices WHERE id = ? AND posted_by = ?");
    if ($stmt) {
        $stmt->bind_param("ii", $id, $user_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Notice deleted successfully!";
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = "Failed to prepare the SQL statement.";
    }
} else {
    $_SESSION['message'] = "Invalid request.";
}

$conn->close();
header("Location: adminDashboards.php");
exit();
?>
