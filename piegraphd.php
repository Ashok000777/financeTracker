<?php
// Start the session to access session variables
session_start();

// Database connection credentials
$servername = "localhost";
$username = "root"; // Replace with your DB username
$password = ""; // Replace with your DB password
$dbname = "financetracker"; // Replace with your DB name

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure user ID is set in the session
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id']; // Get the user ID from the session

    // SQL query to sum debit amounts based on common descriptions
    $sql = "SELECT description, SUM(amount) AS total_amount
            FROM transactions
            WHERE transaction_type = 'debit' AND user_id = ?  -- Fetching only debit transactions for the logged-in user
            GROUP BY description
            ORDER BY total_amount DESC";  // Ordering by total amount in descending order

    // Prepare and bind the SQL statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id); // Bind user_id as an integer
    $stmt->execute();
    $result = $stmt->get_result();

    // Prepare an array to store the results
    $data = [];
    if ($result->num_rows > 0) {
        // Fetch data from the result set
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                'description' => $row['description'],
                'total_amount' => $row['total_amount']
            ];
        }
    }

    // Return the data as JSON format for frontend usage
    header('Content-Type: application/json');
    echo json_encode($data);

    // Close the prepared statement
    $stmt->close();
} else {
    // Return an error if the user is not logged in
    header('Content-Type: application/json');
    echo json_encode(['error' => "User not logged in."]);
}

// Close the database connection
$conn->close();
?>
