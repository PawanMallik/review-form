<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$host = 'localhost';
$username = 'root'; // Replace with your DB username
$password = ''; // Replace with your DB password
$database = 'CustomerReviews';

// Connect to the database
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $query = $conn->real_escape_string($_POST['query']);
    $review = $conn->real_escape_string($_POST['review']);

    // Handle file upload
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
    }

    $fileName = basename($_FILES['file']['name']);
    $targetFilePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFilePath)) {
        // Insert data into the database
        $stmt = $conn->prepare("INSERT INTO reviews (name, email, query, review, file_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $query, $review, $targetFilePath);

        if ($stmt->execute()) {
            echo "Thank you! Your review has been submitted.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "File upload failed. Please try again.";
    }
}

$conn->close();
?>
