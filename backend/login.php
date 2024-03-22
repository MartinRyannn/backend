<?php
// login.php

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

$username = $data->username;
$password = $data->password;

// Validate username and password
if (strlen($username) < 6 || strlen($password) < 6) {
  http_response_code(400);
  echo json_encode(array("message" => "Username and password must be at least 6 characters long."));
  exit();
}

// Prepare SQL statement (use prepared statements to prevent SQL injection)
$stmt = $conn->prepare("SELECT id, username FROM users WHERE username = ? AND password = ?");
$stmt->bind_param("ss", $username, $password);

// Execute SQL statement
$stmt->execute();
$result = $stmt->get_result();

// Check if any rows were returned
if ($result->num_rows > 0) {
  // User authenticated successfully
  $row = $result->fetch_assoc();
  $userId = $row['id'];
  $username = $row['username']; // Get the username from the database
  http_response_code(200);
  echo json_encode(array("message" => "Login successful", "userId" => $userId, "username" => $username));

  // Set username in session storage
  session_start();
  $_SESSION['username'] = $username;
} else {
  // Authentication failed
  http_response_code(401);
  echo json_encode(array("message" => "Invalid username or password"));
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
