<?php
// Allow requests from any origin
header("Access-Control-Allow-Origin: *");

// Allow specific HTTP methods
header("Access-Control-Allow-Methods: POST, OPTIONS");

// Allow specific headers
header("Access-Control-Allow-Headers: Content-Type");

// Include the database connection
include 'db_conn.php';

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit;
}

// Get the POST data
$data = json_decode(file_get_contents("php://input"));

// Validate received data
if (!isset($data->username) || !isset($data->backgroundImage)) {
    http_response_code(400);
    echo json_encode(array("message" => "Invalid request data."));
    exit();
}

$username = $data->username;
$backgroundImage = $data->backgroundImage;

// Validate background image
if (!is_numeric($backgroundImage)) {
    http_response_code(400);
    echo json_encode(array("message" => "Invalid background image number."));
    exit();
}

// Prepare SQL statement to update background image
$stmt = $conn->prepare("UPDATE users SET background_image = ? WHERE username = ?");
$stmt->bind_param("is", $backgroundImage, $username);

// Execute SQL statement
if ($stmt->execute()) {
    // Background image updated successfully
    http_response_code(200);
    echo json_encode(array("message" => "Background image updated successfully"));
} else {
    // Failed to update background image
    http_response_code(500);
    echo json_encode(array("message" => "Failed to update background image"));
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
