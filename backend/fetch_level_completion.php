<?php

header("Access-Control-Allow-Origin: *");

header("Access-Control-Allow-Methods: GET, OPTIONS");

header("Access-Control-Allow-Headers: Content-Type");

include 'db_conn.php';

$userId = $_GET['userId'];

$query = "SELECT level_id, MAX(score) AS highest_score FROM games WHERE player_id = $userId GROUP BY level_id";
$result = mysqli_query($conn, $query);

if (!$result) {
    $response = array(
        'error' => 'Failed to fetch level completion status'
    );
    echo json_encode($response);
    exit();
}

$levelCompletion = array();
while ($row = mysqli_fetch_assoc($result)) {
    $levelCompletion[$row['level_id']] = $row['highest_score'];
}

mysqli_close($conn);

echo json_encode($levelCompletion);
?>
