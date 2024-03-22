<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'db_conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit;
}

$data = json_decode(file_get_contents("php://input"));

$username = $data->username;

if (strlen($username) < 6) {
  http_response_code(400);
  echo json_encode(array("message" => "Username must be at least 6 characters long."));
  exit();
}

$stmt = $conn->prepare("SELECT username, password, wins, (SELECT COALESCE(SUM(score), 0) FROM games WHERE player_id = users.id) as score, background_image, profile_image, credits FROM users WHERE username = ?");
$stmt->bind_param("s", $username);

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $response = array(
    "username" => $row['username'],
    "password" => $row['password'],
    "wins" => $row['wins'],
    "credits" => $row['credits'],
    "score" => $row['score'],
    "background_image" => $row['background_image'],
    "profile_image" => $row['profile_image']
  );
  http_response_code(200);
  echo json_encode($response);
} else {
  http_response_code(404);
  echo json_encode(array("message" => "User not found"));
}

$stmt->close();
$conn->close();
?>
