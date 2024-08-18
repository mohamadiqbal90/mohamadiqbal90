<?php
include 'config.php';

session_start();
$created_by = $_SESSION['name'] ?? '';

if (!isset($_POST['post_id'], $_POST['content'])) {
    echo "Invalid input.";
    exit();
}

$post_id = $_POST['post_id'];
$content = $_POST['content'];

// Insert comment
$query = "INSERT INTO forum_comments (post_id, content, created_by) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("iss", $post_id, $content, $created_by);

if ($stmt->execute()) {
    header("Location: forum.php");
} else {
    echo "Error: " . $stmt->error;
}
?>
