<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

$servername = "localhost"; // Your database server
$username = "root";        // Your database username
$password = "";            // Your database password
$dbname = "financetracker";     // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in user's user_id from session
$user_id = $_SESSION['user_id'];

// Query the database to get the user's full name and email from the signinfo table
$query = "SELECT fullname, email FROM signinfo WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the data
$userData = $result->fetch_assoc();

// If user data is not found, redirect to login page
if (!$userData) {
    header("Location: login.php");
    exit();
}

// Send JSON response with user data, including user_id
echo json_encode([
    'user_id' => htmlspecialchars($user_id), // Include user_id in response
    'fullname' => htmlspecialchars($userData['fullname']),
    'email' => htmlspecialchars($userData['email']),
]);

// Close the database connection
$conn->close();
?>
