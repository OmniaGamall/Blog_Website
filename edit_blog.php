<?php
session_start();

include('database.php');


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the post ID from the URL
$postId = $_GET['blog_id'] ?? null;

if ($postId) {

    $stmt = $conn->prepare("SELECT * FROM blogs WHERE blog_id = ?");
    $stmt->bind_param("i", $postId);  // Bind the postId parameter
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();

    if ($post && $post['user_id'] == $_SESSION['user_id']) {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $title = $_POST['title'];
            $content = $_POST['content'];

            $stmt = $conn->prepare("UPDATE blogs SET title = ?, content = ? WHERE blog_id = ?");
            $stmt->bind_param("ssi", $title, $content, $postId);  
            $stmt->execute();


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
