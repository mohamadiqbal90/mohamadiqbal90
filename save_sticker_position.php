<?php
session_start();
include 'config.php';

// Ensure the request is coming via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

// Retrieve and decode the JSON data
$data = json_decode(file_get_contents('php://input'), true);

// Check if the data is valid
if (!isset($data) || !is_array($data)) {
    echo json_encode(['error' => 'Invalid data']);
    exit;
}

// Iterate over each sticker and update its data in the database
foreach ($data as $sticker) {
    $id = intval($sticker['id']);
    $pos_x = intval($sticker['x']);
    $pos_y = intval($sticker['y']);
    $width = intval($sticker['width']);
    $height = intval($sticker['height']);
    $z_index = intval($sticker['z_index']);

    $sql = "UPDATE profile_stickers SET pos_x = ?, pos_y = ?, width = ?, height = ?, z_index = ? WHERE id = ? AND student_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iiiiiii", $pos_x, $pos_y, $width, $height, $z_index, $id, $_SESSION['student_id']);
        $stmt->execute();
        $stmt->close();
    }
}

echo json_encode(['success' => 'Stickers updated successfully']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_SESSION['student_id'];
    $sticker_id = $_POST['id'];
    $pos_x = $_POST['pos_x'];
    $pos_y = $_POST['pos_y'];

    // Debug output
    error_log("Student ID: " . $student_id);
    error_log("Sticker ID: " . $sticker_id);
    error_log("Position X: " . $pos_x);
    error_log("Position Y: " . $pos_y);


    $sql = "UPDATE profile_stickers SET pos_x = ?, pos_y = ? WHERE id = ? AND student_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iiii", $pos_x, $pos_y, $sticker_id, $student_id);
        if ($stmt->execute()) {
            echo "Position updated successfully.";
        } else {
            echo "Error updating position.";
        }
        $stmt->close();
    } else {
        echo "Error preparing statement.";
    }
    

    $conn->close();
}
?>
