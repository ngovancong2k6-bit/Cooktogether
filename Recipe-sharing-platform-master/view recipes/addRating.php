<?php
// addRating.php
session_start();

if (isset($_SESSION["user_id"])) {
    $user_Id = $_SESSION["user_id"];
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "recipe_sharing_Platform";

    $conn = new mysqli($servername, $username, $password, $dbname, 3306);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get recipe ID and rating from the POST data
    $recipeId = $_POST['recipe_id'];
    $rating = $_POST['rating'];

    // Check if the user has already rated the recipe
    $existingRatingSql = "SELECT * FROM ratings WHERE user_id = $user_Id AND recipe_id = $recipeId";
    $existingRatingResult = $conn->query($existingRatingSql);

    if ($existingRatingResult->num_rows > 0) {
        // User has already rated this recipe, display an alert
        echo "You have already rated this recipe.";
    } else {
        // Insert the rating into the database
        $insertRatingSql = "INSERT INTO ratings (user_id, recipe_id, rating) VALUES ($user_Id, $recipeId, $rating)";
        $result = $conn->query($insertRatingSql);

        // Check if the insertion was successful
        // if ($result) {
        //     // Rating added successfully, display an alert
            
        // } else {
        //     // Error adding rating, display an alert with the error message
        //     echo "Error adding rating: " . $conn->error;
        // }
    }

    $conn->close();
}
?>
