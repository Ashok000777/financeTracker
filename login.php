<?php
// Start the session
session_start();

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "financetracker";

// Create a connection to the MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data is posted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($password)) {
        // Prepare and bind to retrieve user ID and password
        $stmt = $conn->prepare("SELECT id, password FROM signinfo WHERE email = ?"); // Change here to select id
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // Check if the user exists
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $hashed_password); // Fetch user ID along with hashed password
            $stmt->fetch();

            // Verify the password
            if (password_verify($password, $hashed_password)) {
                // Set session variables
                $_SESSION['email'] = $email;
                $_SESSION['user_id'] = $user_id; // Store user ID in session

                // Redirect to the tester page
                header("Location: testerdemo.html");
                exit();
            } else {
                echo "Invalid email or password.";
            }
        } else {
            echo "Invalid email or password.";
        }

        $stmt->close();
    } else {
        echo "Please provide a valid email and password.";
    }
} else {
    echo "Invalid request method.";
}

// Close the database connection
$conn->close();
?>
