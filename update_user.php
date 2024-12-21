<?php
include 'database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($current_password_hash);
    $stmt->fetch();
    $stmt->close();

    if (!empty($_POST['new_password'])) {
        if (!password_verify($_POST['old_password'], $current_password_hash)) {
            echo "Old password is incorrect.";
            exit;
        }
        $hashed_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE user_id = ?");
        $stmt->bind_param("sssi", $_POST['username'], $_POST['email'], $hashed_password, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE user_id = ?");
        $stmt->bind_param("ssi", $_POST['username'], $_POST['email'], $user_id);
    }

    echo $stmt->execute() ? "User data updated successfully." : "Failed to update user data.";
    $stmt->close();
}

$stmt = $conn->prepare("SELECT username, email FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>
<link rel="stylesheet" href="css/update_user.css">

<form method="POST">
    <h1>Update User Data</h1>
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

    <label for="old_password">Old Password:</label>
    <input type="password" id="old_password" name="old_password" placeholder="Enter your old password">

    <label for="new_password">New Password:</label>
    <input type="password" id="new_password" name="new_password" placeholder="Leave blank to keep current password">

    <button type="submit">Update</button>
</form>
