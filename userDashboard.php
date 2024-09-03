<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'User') {
    header("Location: userLogin.html");
    exit();
}

include "config.php";

// Get the user's name
$user_id = $_SESSION['user_id'];
$user_query = $conn->prepare("SELECT name FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user = $user_result->fetch_assoc();
$user_name = $user['name'];


$category = isset($_POST['category']) ? $_POST['category'] : '';


$category_sql = "SELECT DISTINCT category FROM notices";
$category_result = $conn->query($category_sql);


$notices_sql = "SELECT * FROM notices";
if ($category) {
    $notices_sql .= " WHERE category = ?";
}

$stmt = $conn->prepare($notices_sql);

if ($category) {
    $stmt->bind_param("s", $category);
}

$stmt->execute();
$notices_result = $stmt->get_result();

// Check for errors
if (!$notices_result) {
    die("Error executing query: " . $conn->error);
}

if ($notices_result->num_rows === 0) {
    $message = "No notices found for the selected category.";
} else {
    $message = "";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <!-- Navbar with profile and logout -->
    <nav class="navbar navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand">
                <img src="profile.png" alt="Profile Icon" width="30" height="30" class="d-inline-block align-top">
                <?php echo htmlspecialchars($user_name); ?>
            </a>
            <form class="d-flex" action="logout.php" method="post">
                <button class="btn btn-outline-danger" type="submit">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>User Dashboard</h1>

        <!-- Category selection -->
        <form action="userDashboard.php" method="post" class="mt-3">
            <div class="mb-3">
                <label for="category" class="form-label">Select Category:</label>
                <select name="category" id="category" class="form-select">
                    <option value="">All Categories</option>
                    <?php while ($row = $category_result->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($row['category']); ?>" <?php echo $category === $row['category'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['category']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <!-- Notices -->
        <h3 class="mt-5">Notices</h3>
        <?php if ($message): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php else: ?>
            <ul class="list-group">
                <?php while ($notice = $notices_result->fetch_assoc()): ?>
                    <li class="list-group-item">
                        <h5><?php echo htmlspecialchars($notice['title']); ?></h5>
                        <p><?php echo htmlspecialchars($notice['description']); ?></p>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>
</html>

