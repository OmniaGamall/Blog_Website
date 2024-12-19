<?php
session_start();
include 'database.php';
include 'header.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
$user_logged_in = isset($_SESSION['user_id']);

if ($user_logged_in) {
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}

// Fetch all blogs
$result = $conn->query("SELECT blog_id, title, content, user_id, created_at FROM blogs ORDER BY created_at DESC");
?>
    <link rel="stylesheet" href="css/index.css">

<div class="container">
    <h1>All Blogs</h1>

    <?php if ($user_logged_in): ?>
        <p id="mmm">Welcome, <?= htmlspecialchars($user['username']) ?>!</p>
    <?php else: ?>
        <p><a href="login.php">Login</a> to manage your blogs.</p>
    <?php endif; ?>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="blog">
            <h2><?= htmlspecialchars($row['title']) ?></h2>
            <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>
            <small>Posted on <?= $row['created_at'] ?></small>

            <?php if ($user_logged_in && $_SESSION['user_id'] == $row['user_id']): ?>
                <a href="delete_blog.php?blog_id=<?= htmlspecialchars($row['blog_id']) ?>" 
                   onclick="return confirm('Are you sure you want to delete this blog?')">Delete</a>
                <a href="edit_blog.php?blog_id=<?= htmlspecialchars($row['blog_id']) ?>">Edit</a>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</div>

<?php include 'footer.php'; ?>
