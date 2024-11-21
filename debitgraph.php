<?php
header('Content-Type: application/json');

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

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Prepare SQL statement to fetch data, grouping by date, summing the amounts, and ordering by date
    $sql = "SELECT date, SUM(amount) AS total_amount 
            FROM transactions  
            WHERE transaction_type = 'debit' AND user_id = ? 
            GROUP BY date  
            ORDER BY date ASC";
            
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        echo json_encode(['error' => "SQL prepare failed: " . $conn->error]);
        exit();
    }
    
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
        $data = ['error' => "0 results"];
    }

    $stmt->close();
} else {
    // Return an error if the user is not logged in
    $data = ['error' => "User not logged in."];
}

$conn->close();

// Output the result as JSON
echo json_encode($data);
?>
