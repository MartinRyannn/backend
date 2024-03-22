<?php
// Allow requests from any origin
header("Access-Control-Allow-Origin: *");

// Allow specific HTTP methods
header("Access-Control-Allow-Methods: GET, OPTIONS");

// Allow specific headers
header("Access-Control-Allow-Headers: Content-Type");

// Include the database connection
include 'db_conn.php';

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit;
}

// Get the player ID from the request parameters
$playerId = $_GET['playerId'];

// Get the sorting criteria from the request parameters
$sort = $_GET['sort'];

// Get the limit and offset for pagination
$limit = isset($_GET['limit']) ? $_GET['limit'] : 20;
$offset = isset($_GET['offset']) ? $_GET['offset'] : 0;

// Define the default sorting column and order
$sortColumn = 'date_played';
$sortOrder = 'DESC';

// Adjust sorting column and order based on the sorting criteria
switch ($sort) {
    case 'score':
        $sortColumn = 'score';
        break;
    case 'recent':
        $sortColumn = 'date_played';
        $sortOrder = 'DESC';
        break;
    case 'oldest':
        $sortColumn = 'date_played';
        $sortOrder = 'ASC';
        break;
    // Add more cases for additional sorting criteria
    default:
        $sortColumn = 'date_played';
}

// Prepare SQL statement to fetch game history for the player with sorting and pagination
$sql = "SELECT * FROM games WHERE player_id = ? GROUP BY date_played ORDER BY $sortColumn $sortOrder LIMIT ? OFFSET ?";

// Prepare SQL statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $playerId, $limit, $offset);

// Execute SQL statement
$stmt->execute();
$result = $stmt->get_result();

// Initialize an array to store game history data
$gameHistory = array();

// Check if any rows were returned
if ($result->num_rows > 0) {
    // Loop through each row and add it to the game history array
    while ($row = $result->fetch_assoc()) {
        $gameHistory[] = $row;
    }
    
    // Prepare response data
    $response = array(
        "gameHistory" => $gameHistory
    );
    
    // Send response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // No game history found for the player
    http_response_code(404);
    echo json_encode(array("message" => "No game history found for the player"));
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
