<?php
include 'database.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO blogs (title, content, user_id, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("ssi", $title, $content, $user_id);
    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        $error = "Failed to add blog.";
    }
}
?>

<form method="POST" action="">
    <h1>Add New Blog</h1>
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" required>
    <label for="content">Content:</label>
    <textarea id="content" name="content" required></textarea>
    <button type="submit">Add Blog</button>
    <?php if (!empty($error)) echo "<p>$error</p>"; ?>
</form>
