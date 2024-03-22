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

// Check if the required properties exist
if (isset($data->newUsername, $data->newPassword, $data->userId)) {
    $newUsername = $data->newUsername;
    $newPassword = $data->newPassword;
    $userId = $data->userId;

    // Update username and password in the database
    $sql = "UPDATE users SET username = ?, password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $newUsername, $newPassword, $userId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(array("message" => "Username and password updated successfully"));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Failed to update username and password"));
    }

    $stmt->close();
    $conn->close();
} else {
    // Data is invalid
    http_response_code(400);
    echo json_encode(array("message" => "Error: Invalid data"));
}
?>
