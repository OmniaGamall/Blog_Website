<?php
include 'database.php';
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Debugging output
    echo "<pre>";
    print_r($_GET);
    echo "</pre>";

    if (isset($_GET['blog_id']) && is_numeric($_GET['blog_id'])) {
        $blog_id = intval($_GET['blog_id']);
        $user_id = $_SESSION['user_id'];

        echo "Blog ID: $blog_id, User ID: $user_id<br>";

        $stmt = $conn->prepare("DELETE FROM blogs WHERE blog_id = ? AND user_id = ?");
        if ($stmt) {
            $stmt->bind_param("ii", $blog_id, $user_id);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo "Blog deleted successfully!";
                    header("Location: index.php");
                    exit;
                } else {
                    echo "No blog found or permission denied.";
                }
            } else {
                echo "Query execution error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing query: " . $conn->error;
        }
    } else {
        echo "Invalid blog ID.";
    }
} else {
    echo "Invalid request method.";
}
?>
