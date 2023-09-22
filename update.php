<?php
session_start();
require_once('vendor/autoload.php');

// Connect to the database (same code as in your original script)
$db_host = 'localhost';
$db_name = 'files';
$db_user = 'root';
$db_pass = '';
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
$conn->set_charset('utf8');

// Check for errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['id']) && isset($_POST['editedText'])) {
    $id = $_POST['id'];
    $editedText = $_POST['editedText'];

    // Update the 'englishText' column in the database
    $sql = "UPDATE newspapers SET englishText = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $editedText, $id);

    if ($stmt->execute()) {
        echo "Text updated successfully.";
    } else {
        echo "Error updating text: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>
