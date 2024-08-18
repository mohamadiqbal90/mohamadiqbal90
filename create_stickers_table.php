<?php
// Include your database configuration
include 'config.php';

// SQL query to create the stickers table
$sql = "CREATE TABLE IF NOT EXISTS stickers (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT(11) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    position_x INT(11) NOT NULL,
    position_y INT(11) NOT NULL,
    size INT(11) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'stickers' created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

// Close the connection
$conn->close();
?>
