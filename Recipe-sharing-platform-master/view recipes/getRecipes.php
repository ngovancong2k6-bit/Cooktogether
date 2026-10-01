<?php
// getRecipes.php

// Connect to the database (replace with your database credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "recipe_sharing_Platform";

$conn = new mysqli($servername, $username, $password, $dbname, 3306);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get category from the query parameter
$category = $_GET['category'];

// Fetch recipes and their average ratings
$sql = "SELECT recipes.id, recipes.title, AVG(ratings.rating) AS avg_rating
        FROM recipes
        LEFT JOIN ratings ON recipes.id = ratings.recipe_id
        WHERE recipes.type = '$category'
        GROUP BY recipes.id, recipes.title";
$result = $conn->query($sql);

$recipes = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $recipes[] = $row;
    }

    // Send the recipes as JSON
    header('Content-Type: application/json');
    echo json_encode($recipes);
} else {
    // Provide error information if the query fails
    echo '{"error": "' . $conn->error . '"}';
}

$conn->close();
?>