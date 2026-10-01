
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Uploaded Recipes</title>
    <link rel="stylesheet" href="viewuploadedrecipes.css"> <!-- Add the correct path to your styles.css file -->
</head>
<body>


<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "recipe_sharing_Platform";

$conn = new mysqli($servername, $username, $password, $dbname,3306);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['recipe_id'])) {
    $recipeId = $_GET['recipe_id'];

    // Delete recipe and corresponding ingredients
    $deleteRecipeSql = "DELETE FROM recipes WHERE id = $recipeId";
    $deleteIngredientsSql = "DELETE FROM ingredients WHERE recipe_id = $recipeId";

    // Perform the deletion
    if ($conn->query($deleteRecipeSql) === TRUE && $conn->query($deleteIngredientsSql) === TRUE) {
        echo "Deleted recipe ID {$recipeId} and its corresponding ingredients.";
        header("Location: viewUploadedRecipes.php");
    } else {
        echo "Error deleting recipe: " . $conn->error;
    }
} else {
    echo "Recipe ID not provided.";
}

$conn->close();
?>
</body>
</html>
