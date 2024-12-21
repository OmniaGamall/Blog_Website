<?php
session_start();
include 'database.php';
include 'header.php';

$user_logged_in = isset($_SESSION['user_id']);

if ($user_logged_in) {
    $stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
}

$result = $conn->query("
    SELECT blogs.blog_id, blogs.title, blogs.content, blogs.user_id, blogs.created_at, users.username 
    FROM blogs 
    JOIN users ON blogs.user_id = users.user_id 
    ORDER BY blogs.created_at DESC
");
?>
<link rel="stylesheet" href="css/index.css">

<div class="container">
    <h1>All Blogs</h1>

    <?php if ($user_logged_in): ?>
        <p>Welcome, <?= htmlspecialchars($user['username']) ?>!</p>
    <?php else: ?>
        <p><a href="login.php">Login</a> to manage your blogs.</p>
    <?php endif; ?>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="blog">
            <h2><?= htmlspecialchars($row['title']) ?></h2>
            <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>
            <small>
                Posted by <strong><?= htmlspecialchars($row['username']) ?></strong> 
                on <?= $row['created_at'] ?>
            </small>

            <?php if ($user_logged_in && $_SESSION['user_id'] == $row['user_id']): ?>
                <a href="delete_blog.php?blog_id=<?= htmlspecialchars($row['blog_id']) ?>" 
                   onclick="return confirm('Are you sure you want to delete this blog?')">Delete</a>
                <a href="edit_blog.php?blog_id=<?= htmlspecialchars($row['blog_id']) ?>">Edit</a>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</div>

<?php include 'footer.php'; ?>
