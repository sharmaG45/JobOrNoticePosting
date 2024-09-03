<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: userLogin.html");
    exit();
}

$notices = [];

$result = $conn->query("SELECT id, title, description, category FROM notices WHERE posted_by = {$_SESSION['user_id']}");
if ($result) {
    $notices = $result->fetch_all(MYSQLI_ASSOC);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Admin Dashboard</h1>
        
        <div class="d-flex justify-content-between mb-3">
           
            <a href="postNotice.php" class="btn btn-primary">Post New Notice</a>

          
            <form action="logOut.php" method="post">
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </div>
        
        <h2 class="mt-5">Manage Notices</h2>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Title</th>
                    <th scope="col">Description</th>
                    <th scope="col">Category</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notices as $notice): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($notice['id']); ?></td>
                        <td><?php echo htmlspecialchars($notice['title']); ?></td>
                        <td><?php echo htmlspecialchars($notice['description']); ?></td>
                        <td><?php echo htmlspecialchars($notice['category']); ?></td>
                        <td>
                            
                            <a href="updateNotice.php?id=<?php echo htmlspecialchars($notice['id']); ?>" class="btn btn-warning btn-sm">Update</a>
                            <a href="deleteNotice.php?id=<?php echo htmlspecialchars($notice['id']); ?>" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
