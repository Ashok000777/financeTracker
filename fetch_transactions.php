<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session to access session variables
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "financetracker";

// Database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['error' => "Connection failed: " . $conn->connect_error]));
}

// Ensure user ID is set in the session
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id']; // Get the user ID from the session

    // SQL query to fetch transactions for the logged-in user
    $sql = "SELECT date, amount, 
                   CASE 
                       WHEN transaction_type = 'credit' THEN 'Credited' 
                       WHEN transaction_type = 'debit' THEN 'Debited' 
                       ELSE 'Unknown' 
                   END AS transaction_type 
            FROM transactions 
            WHERE user_id = ? 
            ORDER BY date DESC"; // Filter by user_id

    // Prepare and bind
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id); // Bind user_id as an integer
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the query executed properly
    if (!$result) {
        die(json_encode(['error' => "Query failed: " . $conn->error]));
    }

    // Process result
    $data = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    } else {
        $data = ['error' => "No transactions found"];
    }

    // Close the prepared statement
    $stmt->close();
} else {
    // Return an error if the user is not logged in
    echo json_encode(['error' => "User not logged in."]);
    exit();
}

// Close the connection
$conn->close();

// Return data as JSON
echo json_encode($data, JSON_PRETTY_PRINT); // Pretty print for easy debugging
?>
