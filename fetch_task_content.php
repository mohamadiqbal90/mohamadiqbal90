<?php
include 'config.php';

header('Content-Type: application/json');

if (!isset($_GET['task_id'])) {
    echo json_encode(['error' => 'Task ID is missing']);
    exit();
}

$task_id = $_GET['task_id'];

$query = "SELECT title, description, due_date, link FROM task WHERE task_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $task_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Task not found']);
} else {
    $task = $result->fetch_assoc();
    echo json_encode($task);
}
?>
