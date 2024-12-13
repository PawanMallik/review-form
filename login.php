<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database connection
    $host = 'localhost';
    $username = 'root'; // Use your database username
    $password = ''; // Use your database password
    $database = 'CustomerReviews';
    
    $conn = new mysqli($host, $username, $password, $database);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Get form inputs
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $query = $conn->real_escape_string($_POST['query']);
    $review = $conn->real_escape_string($_POST['review']);
    
    // Handle file upload
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    
    $fileName = basename($_FILES['file']['name']);
    $targetFilePath = $uploadDir . $fileName;
    
    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFilePath)) {
        // Save data to the database
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
    
    $conn->close();
}
?>
