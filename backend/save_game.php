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

$player_id = $data->player_id;
$level_id = $data->level_id;
$score = $data->score;
$time = $data->time;
$date_played = $data->date_played;
$credits = $data->credits;

if ($player_id && $level_id && $score !== null && $time !== null && $date_played && $credits !== null) {
    // Insert the game data
    $stmt = $conn->prepare("INSERT INTO games (player_id, level_id, score, time, date_played) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $player_id, $level_id, $score, $time, $date_played);
    $stmt->execute();

    // Check for successful insertion
    if ($stmt->affected_rows > 0) {
        // Update user credits with the provided value
        $sqlUpdateCredits = "UPDATE users SET credits = ? WHERE id = ?";
        $stmtUpdateCredits = $conn->prepare($sqlUpdateCredits);
        $stmtUpdateCredits->bind_param("ii", $credits, $player_id);
        $stmtUpdateCredits->execute();
        $stmtUpdateCredits->close();

        echo json_encode(array("message" => "Game data saved successfully"));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Failed to save game data"));
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Error: Invalid game data"));
}
?>
