<?php
header("Access-Control-Allow-Origin: *");

header("Access-Control-Allow-Methods: POST, OPTIONS");

header("Access-Control-Allow-Headers: Content-Type");

include 'db_conn.php';

$userId = $_GET['userId'];

$sql = "SELECT l.*, IFNULL(g.id, 0) AS completed
        FROM levels l
        LEFT JOIN games g ON l.id = g.level_id AND g.player_id = ?
        ORDER BY l.levelNR";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$levels = [];
while ($row = $result->fetch_assoc()) {
    $levels[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode(['levels' => $levels]);
?>
