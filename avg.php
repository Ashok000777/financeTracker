<?php
// Start the session
session_start(); // Ensure session is started to access session variables

// Database connection
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "financetracker"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure user ID is set in the session
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id']; // Get the user ID from the session

    // SQL query to get the average daily debits, credits, and difference for the logged-in user
    $sql = "SELECT 
        AVG(CASE WHEN transaction_type = 'credit' THEN amount END) AS avg_credit, 
        AVG(CASE WHEN transaction_type = 'debit' THEN amount END) AS avg_debit, 
        (AVG(CASE WHEN transaction_type = 'credit' THEN amount END) - AVG(CASE WHEN transaction_type = 'debit' THEN amount END)) AS avg_difference
    FROM transactions
    WHERE date >= DATE_FORMAT(CURDATE(), '%Y-%m-01') 
  AND date < DATE_FORMAT(CURDATE() + INTERVAL 1 MONTH, '%Y-%m-01')
  AND user_id = ?;
"; // Include user_id in WHERE clause

    // Prepare and bind
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("i", $user_id); // Bind user_id as an integer
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the result as an associative array
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row); // Return data as JSON
    } else {
        echo json_encode([
            'avg_debit' => 0,
            'avg_credit' => 0,
            'avg_difference' => 0
        ]);
    }

    $stmt->close(); // Close the statement
} else {
    echo json_encode([
        'error' => 'User not logged in.'
    ]);
}

$conn->close(); // Close the database connection
?>

