<?php
// get_leaderboard.php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'db_conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit;
}

// Get the sort criteria from the request query parameters (e.g., ?sort=score or ?sort=wins)
$sortCriteria = isset($_GET['sort']) ? $_GET['sort'] : 'score'; // Default to sorting by score if not provided

// Prepare the SQL query based on the selected sort criteria
$sqlQuery = "SELECT u.username, COALESCE((SELECT SUM(g.score) FROM games g WHERE g.player_id = u.id), 0) as total_score, ";
$sqlQuery .= "(SELECT COUNT(*) FROM games WHERE player_id = u.id AND score > 700) as wins ";
$sqlQuery .= "FROM users u ";

// Adjust the SQL query based on the selected sort criteria
if ($sortCriteria === 'wins') {
    $sqlQuery .= "ORDER BY wins DESC, total_score DESC LIMIT 10";
} else {
    $sqlQuery .= "ORDER BY total_score DESC, wins DESC LIMIT 10";
}

$stmt = $conn->prepare($sqlQuery);
$stmt->execute();
$result = $stmt->get_result();

$leaderboardData = array();

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $leaderboardData[] = array(
      "username" => $row['username'],
      "score" => $row['total_score'], // Use total_score as the key
      "wins" => $row['wins']
    );
  }
  
  http_response_code(200);
  echo json_encode($leaderboardData);
} else {
  http_response_code(404);
  echo json_encode(array("message" => "No players found"));
}

$stmt->close();
$conn->close();
?>
