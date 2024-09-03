<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: userLogin.html");
    exit();
}

$successMessage = '';
$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title'], $_POST['description'], $_POST['category'])) {
    $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
    $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);

    $stmt = $conn->prepare("INSERT INTO notices (title, description, category, posted_by) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssi", $title, $description, $category, $_SESSION['user_id']);
        if ($stmt->execute()) {
            $successMessage = "Notice posted successfully!";
        } else {
            $errorMessage = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $errorMessage = "Failed to prepare the SQL statement.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Post Notice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Post Notice</h1>
        <form action="postNotice.php" method="post">
            <div class="mb-3">
                <label for="title" class="form-label">Notice Title</label>
                <input type="text" name="title" class="form-control" id="title" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Notice Content</label>
                <textarea name="description" class="form-control" id="description" rows="5" required></textarea>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select name="category" class="form-control" id="category" required>
                    <option value="Government">Government</option>
                    <option value="College">College</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Post Notice</button>
        </form>

        <!-- Display success or error messages -->
        <?php if ($successMessage): ?>
            <div class="alert alert-success mt-3" role="alert">
                <?php echo htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>
        <?php if ($errorMessage): ?>
            <div class="alert alert-danger mt-3" role="alert">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <a href="adminDashboards.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>
</body>
</html>
