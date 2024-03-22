<?php
// update_profile_image.php

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

// Get the request body
$data = json_decode(file_get_contents('php://input'), true);
// Add console log to check received data
error_log("Received data: " . print_r($data, true));

// Extract username and profileImage from the request body
$username = $data['username'];
$profileImage = $data['profileImage'];
// Add console log to check extracted data
error_log("Username: $username, Profile Image: $profileImage");

// Prepare SQL statement
$stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE username = ?");
$stmt->bind_param("ss", $profileImage, $username);

// Execute SQL statement
if ($stmt->execute()) {
    // Profile image updated successfully
    echo json_encode(array("message" => "Profile image updated successfully"));
} else {
    // Failed to update profile image
    http_response_code(500); // Internal Server Error
    echo json_encode(array("message" => "Failed to update profile image"));
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
