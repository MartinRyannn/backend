<?php
header("Access-Control-Allow-Origin: *");

header("Access-Control-Allow-Methods: GET, OPTIONS");

header("Access-Control-Allow-Headers: Content-Type");

include 'db_conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit;
}

$stmt = $conn->prepare("SELECT background_image FROM users WHERE username = ?");
$stmt->bind_param("s", $_GET['username']);

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "User not found"));
}


$stmt->close();
$conn->close();
?>
