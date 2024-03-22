<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once 'db_conn.php';

$username = $_GET['username'];

$stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
$stmt->bind_param("s", $username);

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(array("exists" => true));
} else {
    echo json_encode(array("exists" => false));
}

$stmt->close();
$conn->close();
?>
