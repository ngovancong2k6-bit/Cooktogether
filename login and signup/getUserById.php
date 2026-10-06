<?php
session_start();


// Get the user ID from the request parameter
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    // Replace these with your actual database credentials
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "recipe_sharing_platform";
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname, 3306);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute the SQL query to fetch the user's name by user ID
    $sql = "SELECT name FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($user_name);
    $stmt->fetch();

    // Output the user's name as JSON
    echo json_encode(['name' => $user_name]);

    // Close connections
    $stmt->close();
    $conn->close();
} else {
    // Invalid request
    echo json_encode(['error' => 'Invalid request']);
}
?>
