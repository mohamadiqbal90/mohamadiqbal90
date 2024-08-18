<?php
include 'config.php';

$subject = $_GET['subject'];

$query = "SELECT DISTINCT bab FROM materials WHERE subject = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 's', $subject);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$babs = [];
while ($row = mysqli_fetch_assoc($result)) {
    $babs[] = $row['bab'];
}

echo json_encode(['babs' => $babs]);
?>
