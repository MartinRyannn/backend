<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'db_conn.php';

$data = json_decode(file_get_contents("php://input"));

$username = $data->username;

if ($username) {
    // Query to count wins based on games with a score over 700
    $sql = "SELECT COUNT(*) as win_count FROM games WHERE player_id = (SELECT id FROM users WHERE username = ?) AND score > 700";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $win_count = $row['win_count'];
        echo json_encode(array("wins" => $win_count));
    } else {
        echo json_encode(array("wins" => 0)); // No wins found for the user
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Error: Invalid username"));
}
?>
