<?php
// Start the session
session_start(); // Ensure session is started to access session variables

$servername = "localhost"; // Change as needed
$username = "root"; // Change as needed
$password = ""; // Change as needed
$dbname = "financetracker"; // Change as needed

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure user ID is set in the session
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id']; // Get the user ID from the session

    // Query to sum total credits for the logged-in user
    $sql_credits = "SELECT SUM(amount) AS total_credits FROM transactions WHERE transaction_type = 'credit' AND user_id = ?";
    $stmt_credits = $conn->prepare($sql_credits);
    $stmt_credits->bind_param("i", $user_id); // Bind user_id as an integer
    $stmt_credits->execute();
    $result_credits = $stmt_credits->get_result();
    $total_credits = $result_credits->fetch_assoc()['total_credits'];

    // Query to sum total debits for the logged-in user
    $sql_debits = "SELECT SUM(amount) AS total_debits FROM transactions WHERE transaction_type = 'debit' AND user_id = ?";
    $stmt_debits = $conn->prepare($sql_debits);
    $stmt_debits->bind_param("i", $user_id); // Bind user_id as an integer
    $stmt_debits->execute();
    $result_debits = $stmt_debits->get_result();
    $total_debits = $result_debits->fetch_assoc()['total_debits'];

    // Calculate balance
    $balance = ($total_credits ?? 0) - ($total_debits ?? 0);

    // Return balance as JSON
    echo json_encode(["balance" => $balance]);

    // Close statements
    $stmt_credits->close();
    $stmt_debits->close();
} else {
    // Return an error if the user is not logged in
    echo json_encode(["error" => "User not logged in."]);
}

// Close connection
$conn->close();
?>
