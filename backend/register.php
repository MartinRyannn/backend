<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// If it's an OPTIONS request, send back the appropriate headers and exit
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include database connection here
include_once 'db_conn.php';

$data = json_decode(file_get_contents("php://input"));

if(isset($data->username) && isset($data->password)) {
    $username = $data->username;
    $password = $data->password;

    $query = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
    
    // Execute query
    if(mysqli_query($conn, $query)) {
        http_response_code(200);
        echo json_encode(array("message" => "User registered successfully"));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Error registering user"));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete data"));
}
?>
