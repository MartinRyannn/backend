<?php

header("Access-Control-Allow-Origin: *");

header("Access-Control-Allow-Methods: GET, OPTIONS");

header("Access-Control-Allow-Headers: Content-Type");

include 'db_conn.php';

$query = "SELECT * FROM levels";
$result = mysqli_query($conn, $query);

if (!$result) {
    $response = array(
        'error' => 'Failed to fetch levels'
    );
    echo json_encode($response);
    exit();
}


$levels = array();
while ($row = mysqli_fetch_assoc($result)) {
    $levels[] = $row;
}

mysqli_close($conn);

$response = array(
    'levels' => $levels
);
echo json_encode($response);
?>
