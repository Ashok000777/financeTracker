<?php
// Start the session to access session variables
session_start();

// Check if the user is logged in (assuming user_id is set in the session)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized access. Please log in.']);
    exit;
}

// Database connection settings
$servername = "localhost";
$username = "root"; // Default Laragon MySQL username
$password = "";     // Default Laragon MySQL password is empty
$dbname = "financetracker";

// Create a connection to the MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection and handle any connection errors
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Get the search parameters from the URL
$date = isset($_GET['date']) ? trim($_GET['date']) : '';
$transactionType = isset($_GET['transaction_type']) ? trim($_GET['transaction_type']) : '';

// Validate the input
if (empty($date) && empty($transactionType)) {
    echo json_encode(['error' => 'At least one search criterion is required']);
    exit;
}

// Sanitize the inputs
$searchDate = $conn->real_escape_string($date);
$searchTransactionType = $conn->real_escape_string($transactionType);

// Prepare the SQL query
$sql = "SELECT transaction_type, amount, DATE_FORMAT(date, '%Y-%m-%d') AS date 
        FROM transactions 
        WHERE user_id = {$_SESSION['user_id']}"; // Ensure only the logged-in user's transactions are fetched

if (!empty($searchDate)) {
    $sql .= " AND date = '$searchDate'";
}

if (!empty($searchTransactionType)) {
    $sql .= " AND transaction_type LIKE '%$searchTransactionType%'";
}

$sql .= " ORDER BY date DESC";

// Execute the query
$result = $conn->query($sql);

// Check for errors in the query
if (!$result) {
    echo json_encode(['error' => 'Query failed: ' . htmlspecialchars($conn->error)]);
    exit;
}

// Fetch the results and store them in an array
$transactions = [];
while ($row = $result->fetch_assoc()) {
    $transactions[] = [
        'transaction_type' => htmlspecialchars($row['transaction_type']),
        'amount' => htmlspecialchars($row['amount']),
        'date' => htmlspecialchars($row['date'])
    ];
}

// Close the connection
$conn->close();

// Return the results as a JSON response
echo json_encode($transactions);
?>
