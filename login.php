<?php
// Database connection
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $user_type = $_POST['user_type']; 

    
    $sql = "SELECT id, password, user_type FROM users WHERE email = ?";
    if ($stmt = $conn->prepare($sql)) {
      
        $stmt->bind_param("s", $email);
        
      
        $stmt->execute();
        
    
        $stmt->store_result();
        $stmt->bind_result($id, $hashed_password, $db_user_type);
        
        // Fetch result
        if ($stmt->fetch()) {
            // Verify password
            if (password_verify($password, $hashed_password)) {
                // Start session and set session variables
                session_start();
                $_SESSION['user_id'] = $id;
                $_SESSION['user_type'] = $db_user_type;

                // Redirect based on user type
                if ($db_user_type == 'Admin') {
                    header("Location: adminDashboards.php");
                } else {
                    header("Location: userDashboard.php");
                }
                exit();
            } else {
                echo "Invalid password.";
            }
        } else {
            echo "Invalid username.";
        }

 
        $stmt->close();
    } else {
        echo "Failed to prepare the SQL statement.";
    }
}


$conn->close();
?>
