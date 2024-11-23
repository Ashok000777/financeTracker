<?php
header('Content-Type: application/json');

// Start the session
session_start(); // Ensure the session is started to access session variables

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

    // Query to fetch data, grouping by date, summing the amounts for the logged-in user
    $sql = "SELECT DATE(date) AS date, SUM(amount) AS total_amount 
            FROM transactions 
            WHERE transaction_type = 'credit' AND user_id = ? 
            GROUP BY DATE(date) 
            ORDER BY DATE(date) ASC;";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id); // Bind user_id as an integer
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    if ($result->num_rows > 0) {
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    } else {
        echo json_encode(['error' => "0 results"]);
    }

    // Close the prepared statement
    $stmt->close();
} else {
    // Return an error if the user is not logged in
    echo json_encode(['error' => "User not logged in."]);
}

// Close connection
$conn->close();

// Return data as JSON
echo json_encode($data);
?>
