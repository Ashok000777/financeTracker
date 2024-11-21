<?php
session_start();
$response = ['success' => false];

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_ID = $_SESSION['user_id'];

if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
    $upload_dir = 'uploads/';
    $file_name = basename($_FILES['profile_pic']['name']);
    $target_file = $upload_dir . $file_name;

    // Move uploaded file
    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file)) {
        // Update the profile image in the database
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "your_database_name";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "UPDATE signinfo SET profile_image = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $file_name, $user_id);
        $stmt->execute();

        $response['success'] = true;
        $response['imagePath'] = $target_file;

        $stmt->close();
        $conn->close();
    }
}

echo json_encode($response);
?>
