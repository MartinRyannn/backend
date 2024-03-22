<?php
// get_password.php

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

// Validate username
if (strlen($username) < 6) {
  http_response_code(400);
  echo json_encode(array("message" => "Username must be at least 6 characters long."));
  exit();
}

// Prepare SQL statement (use prepared statements to prevent SQL injection)
$stmt = $conn->prepare("SELECT password, wins, games, score FROM users WHERE username = ?");
$stmt->bind_param("s", $username);

// Execute SQL statement
$stmt->execute();
$result = $stmt->get_result();

// Check if any rows were returned
if ($result->num_rows > 0) {
  // User found
  $row = $result->fetch_assoc();
  $password = $row['password'];
  // Prepare response data
  $response = array(
    "password" => $password,
    "wins" => $row['wins'],
    "games" => $row['games'],
    "score" => $row['score']
  );
  http_response_code(200);
  echo json_encode($response);
} else {
  // User not found
  http_response_code(404);
  echo json_encode(array("message" => "User not found"));
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
