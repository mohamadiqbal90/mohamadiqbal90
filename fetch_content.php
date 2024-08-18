<?php
include 'config.php';

$subject = $_GET['subject'];
$bab = $_GET['bab'];

$query = "SELECT content, video_path FROM materials WHERE subject = ? AND bab = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'ss', $subject, $bab);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$row = mysqli_fetch_assoc($result);

echo json_encode([
    'content' => $row['content'],
    'video_path' => $row['video_path']
]);
?>
