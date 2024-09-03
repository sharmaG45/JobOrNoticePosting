<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: userLogin.html");
    exit();
}

$successMessage = '';
$errorMessage = '';
$notice = null;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM notices WHERE id = $id AND posted_by = {$_SESSION['user_id']}");
    if ($result) {
        $notice = $result->fetch_assoc();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'], $_POST['title'], $_POST['description'], $_POST['category'])) {
    $id = intval($_POST['id']);
    $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
    $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);

    $stmt = $conn->prepare("UPDATE notices SET title = ?, description = ?, category = ? WHERE id = ? AND posted_by = ?");
    if ($stmt) {
        $stmt->bind_param("sssii", $title, $description, $category, $id, $_SESSION['user_id']);
        if ($stmt->execute()) {
            $successMessage = "Notice updated successfully!";
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
    <title>Update Notice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Update Notice</h1>
        <?php if ($notice): ?>
            <form action="updateNotice.php?id=<?php echo htmlspecialchars($notice['id']); ?>" method="post">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($notice['id']); ?>">
                <div class="mb-3">
                    <label for="title" class="form-label">Notice Title</label>
                    <input type="text" name="title" class="form-control" id="title" value="<?php echo htmlspecialchars($notice['title']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Notice Content</label>
                    <textarea name="description" class="form-control" id="description" rows="5" required><?php echo htmlspecialchars($notice['description']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="category" class="form-label">Category</label>
                    <select name="category" class="form-control" id="category" required>
                        <option value="Government" <?php if ($notice['category'] == 'Government') echo 'selected'; ?>>Government</option>
                        <option value="College" <?php if ($notice['category'] == 'College') echo 'selected'; ?>>College</option>
                        <option value="Other" <?php if ($notice['category'] == 'Other') echo 'selected'; ?>>Other</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update Notice</button>
            </form>
        <?php else: ?>
            <div class="alert alert-danger" role="alert">
                Notice not found or you don't have permission to edit it.
            </div>
        <?php endif; ?>

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
