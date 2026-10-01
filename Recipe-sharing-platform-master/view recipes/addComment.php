<?php
// addComment.php
session_start();
// Connect to the database (replace with your database credentials)
if (isset($_SESSION['user_id'])) {
$userId = $_SESSION['user_id'];
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "recipe_sharing_Platform";

$conn = new mysqli($servername, $username, $password, $dbname,3306);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recipeId = $_POST["recipe_id"];
    $content = $_POST["content"];
    $userId = $_SESSION['user_id'];; // Replace with the authenticated user's ID

    // Insert the comment into the database
    $sql = "INSERT INTO comments (recipe_id, user_id, content, created_at)
            VALUES ($recipeId, $userId, '$content', NOW())";

    if ($conn->query($sql) === TRUE) {
        echo "Comment added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
