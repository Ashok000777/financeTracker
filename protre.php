<?php
// process_trends.php

header('Content-Type: application/json');
session_start();  // Ensure session is started to access user session variables

// Database connection
$conn = new mysqli("localhost", "root", "", "financetracker");

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

// Initialize variables
$user_id = $_SESSION['user_id'];  // Assuming user ID is stored in the session
$total_income = 0;
$total_expenses = 0;
$description_breakdown = [];
$highest_description = '';
$budget_recommendations = '';

// Fetch the selected date range from the request (default to 'month')
$date_range = isset($_POST['date-range']) ? $_POST['date-range'] : 'month';
$date_condition = "";

// Calculate the date range based on selection
switch ($date_range) {
    case 'week':
        $date_condition = "AND date >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
        break;
    case 'month':
        $date_condition = "AND date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        break;
    case 'quarter':
        $date_condition = "AND date >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
        break;
    case 'this_month':
        $date_condition = "AND MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())"; // New case for 'This Month'
        break;
}

// Query to fetch all transactions for the user within the selected date range
$query = "SELECT description, transaction_type, SUM(amount) AS total_amount
          FROM transactions
          WHERE user_id = ? $date_condition
          GROUP BY description, transaction_type";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Process the results
while ($row = $result->fetch_assoc()) {
    $description = $row['description'];
    $type = $row['transaction_type'];
    $amount = $row['total_amount'];

    // Calculate total income and expenses
    if ($type == 'credit') {
        $total_income += $amount;
    } elseif ($type == 'debit') {
        $total_expenses += $amount;
    }

    // Populate description breakdown
    if (!isset($description_breakdown[$description])) {
        $description_breakdown[$description] = ['income' => 0, 'expenses' => 0];
    }
    if ($type == 'credit') {
        $description_breakdown[$description]['income'] += $amount;
    } else {
        $description_breakdown[$description]['expenses'] += $amount;
    }
}

// Calculate additional details
$average_spend = round($total_expenses / 30, 2);  // Example for a monthly average

// Find the highest spending category based on description
$max_expense = 0;
foreach ($description_breakdown as $description => $data) {
    $total_description_expense = $data['expenses'];
    if ($total_description_expense > $max_expense) {
        $max_expense = $total_description_expense;
        $highest_description = $description;
    }
}

// Generate budget recommendations based on spending trends
$budget_recommendations = "Your highest spending description is $highest_description. Consider reducing expenses in this area.";

// Return the response as JSON
$response = [
    'total_income' => $total_income,
    'total_expenses' => $total_expenses,
    'average_spend' => $average_spend,
    'highest_description' => $highest_description,
    'description_breakdown' => $description_breakdown,
    'budget_recommendations' => $budget_recommendations
];

echo json_encode($response);

// Close the database connection
$conn->close();
?>
