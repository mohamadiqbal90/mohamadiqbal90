<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'clear_errors') {
        $_SESSION['error_messages'] = [];
        // Respond with a success message
        echo json_encode(['status' => 'success']);
    }
}
?>
