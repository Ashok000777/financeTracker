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
$password = ""; // Default is empty
$dbname = "financetracker"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['error' => "Connection failed: " . $conn->connect_error]));
}

// Ensure user ID is set in the session
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id']; // Get the user ID from the session

    // Query to fetch data, grouping by date, summing the amounts for credited and debited transactions
    $sql = "
        SELECT 
            date, 
            SUM(CASE WHEN transaction_type = 'credit' THEN amount ELSE 0 END) AS credited_amount, 
            SUM(CASE WHEN transaction_type = 'debit' THEN amount ELSE 0 END) AS debited_amount 
        FROM 
            transactions 
        WHERE 
            user_id = ?  -- Filter by user_id
        GROUP BY 
            date 
        ORDER BY 
            date ASC
    ";

    // Prepare and bind
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id); // Bind user_id as an integer
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    if ($result->num_rows > 0) {
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    } else {
        $data = ['error' => "0 results"];
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
