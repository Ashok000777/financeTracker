<?php
// Start the session to access session variables
session_start();

// Check if the user is logged in by verifying the existence of the 'user_id' in the session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Please log in.']);
    exit(); // Stop further execution
}

// Database connection
$servername = "localhost"; // Update with your server name
$username = "root"; // Update with your database username
$password = ""; // Update with your database password
$dbname = "financetracker"; // Update with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Return JSON response for connection error
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit(); // Stop script execution
}

// Prepare and bind the SQL statement
$stmt = $conn->prepare("INSERT INTO transactions (user_id, transaction_type, amount, date, description) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $user_id, $transaction_type, $amount, $date, $description);

// Get user_id from the session
$user_id = $_SESSION['user_id'];

// Get the other parameters from the POST request
$transaction_type = $_POST['transaction_type']; // Should match the name in your form
$amount = $_POST['amount'];
$date = $_POST['date'];
$description = $_POST['description'];

// Execute the SQL statement
if ($stmt->execute()) {
    // Return JSON response for successful transaction addition
    echo json_encode(['success' => true, 'message' => 'New transaction added successfully!']);
} else {
    // Return JSON response for error
    echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
}

// Close connections
$stmt->close();
$conn->close();
?>
