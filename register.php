<?php
include 'config.php';

// Get form data
$name = $_POST['name'];
$email = $_POST['email'];
$phone_no = $_POST['phone_no'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
$user_type = $_POST['user_type'];


$stmt = $conn->prepare("INSERT INTO users (name, email, phone_no, password, user_type) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $email, $phone_no, $password, $user_type);

// Execute the query
if ($stmt->execute()) {
    echo "Registration successful!";
} else {
    echo "Error: " . $stmt->error;
}

// Close connection
$stmt->close();
$conn->close();
?>
