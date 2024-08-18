<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $task_id = $_POST['task_id'] ?? '';
    $link = $_POST['link'] ?? '';

    if (empty($task_id) || empty($link)) {
        echo 'Invalid task ID or link';
        exit();
    }

    // Update the database with the link
    $query = "UPDATE task SET link = ? WHERE task_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $link, $task_id);

    if ($stmt->execute()) {
        echo 'Link submitted successfully';
    } else {
        echo 'Error: ' . $conn->error;
    }
}
?>
