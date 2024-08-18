<?php
session_start();
include 'config.php';

// Ensure the request is coming via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stickerData = json_decode(file_get_contents('php://input'), true);
    
    // Log received data for debugging
    error_log(print_r($stickerData, true));
    
    // Check if the data is valid
    if (!isset($stickerData) || !is_array($stickerData)) {
        echo json_encode(['error' => 'Invalid data']);
        exit;
    }
    
    // Iterate over each sticker and update its data in the database
    foreach ($stickerData as $sticker) {
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
        } else {
            // If there's a database error, return it
            echo json_encode(['error' => 'Database error: ' . $conn->error]);
            exit;
        }
    }

    // If everything went well, return a success message
    echo json_encode(['success' => 'Stickers updated successfully']);
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
