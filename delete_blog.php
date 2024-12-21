<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['blog_id']) && is_numeric($_GET['blog_id'])) {
    $blog_id = intval($_GET['blog_id']);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("DELETE FROM blogs WHERE blog_id = ? AND user_id = ?");
    if ($stmt) {
        $stmt->bind_param("ii", $blog_id, $user_id);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            header("Location: index.php");
            exit;
        } else {
            echo "No blog found or permission denied.";
        }
        $stmt->close();
    } else {
        echo "Error preparing query.";
    }
} else {
    echo "Invalid request or blog ID.";
}
?>
