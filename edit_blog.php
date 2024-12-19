<?php
session_start();

// Include database connection
include('database.php');

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the post ID from the URL
$postId = $_GET['blog_id'] ?? null;

if ($postId) {
    // Use mysqli to fetch the post details
    $stmt = $conn->prepare("SELECT * FROM blogs WHERE blog_id = ?");
    $stmt->bind_param("i", $postId);  // Bind the postId parameter
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();

    // Check if the post exists and belongs to the logged-in user
    if ($post && $post['user_id'] == $_SESSION['user_id']) {
        // If the form is submitted to update the post
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $title = $_POST['title'];
            $content = $_POST['content'];

            // Update the post in the database
            $stmt = $conn->prepare("UPDATE blogs SET title = ?, content = ? WHERE blog_id = ?");
            $stmt->bind_param("ssi", $title, $content, $postId);  // Bind the parameters
            $stmt->execute();

            // Redirect after updating
            header("Location: index.php");
            exit();
        }
    } else {
        echo "You are not authorized to update this post.";
        exit();
    }
} else {
    echo "No blog ID provided!";
    exit();
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

    <!-- Display the form to update the blog post -->
    <form action="edit_blog.php?blog_id=<?php echo $post['blog_id']; ?>" method="POST">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($post['title']); ?>" required><br>

        <label for="content">Content:</label>
        <textarea name="content" id="content" required><?php echo htmlspecialchars($post['content']); ?></textarea><br>

        <button type="submit">Update</button>
    </form>

</body>
</html>
