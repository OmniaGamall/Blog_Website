<?php
session_start();
include('database.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$postId = $_GET['blog_id'] ?? null;

if ($postId) {
    $stmt = $conn->prepare("SELECT * FROM blogs WHERE blog_id = ?");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $post = $stmt->get_result()->fetch_assoc();

    if ($post && $post['user_id'] == $_SESSION['user_id']) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $stmt = $conn->prepare("UPDATE blogs SET title = ?, content = ? WHERE blog_id = ?");
            $stmt->bind_param("ssi", $_POST['title'], $_POST['content'], $postId);
            $stmt->execute();
            header("Location: index.php");
            exit();
        }
    } else {
        exit("Unauthorized to update this post.");
    }
} else {
    exit("No blog ID provided!");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Blog</title>
    <link rel="stylesheet" href="css/edit_blog.css">
</head>
<body>
    <h1>Update Blog</h1>
    <form action="edit_blog.php?blog_id=<?= htmlspecialchars($post['blog_id']) ?>" method="POST">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" value="<?= htmlspecialchars($post['title']) ?>" required><br>

        <label for="content">Content:</label>
        <textarea name="content" id="content" required><?= htmlspecialchars($post['content']) ?></textarea><br>

        <button type="submit">Update</button>
    </form>
</body>
</html>
