<!-- viewRecipeDetails.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Uploaded Recipes</title>
    <link rel="stylesheet" href="user_upload_style.css"> <!-- Add the correct path to your styles.css file -->
</head>
<body>
<!-- viewRecipeDetails.php -->

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


    //join operation is used here to join users, categories and recipes
    $sql = "SELECT r.*, u.name AS uploader_name, c.category_name
            FROM recipes r
            JOIN users u ON r.user_id = u.id
            JOIN categories c ON r.category_id = c.id
            WHERE r.id = $recipeId";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $recipe = $result->fetch_assoc();
        echo "<div class= 'body_content'>";

        echo "<div class='recipe-card'>";
        echo "<h2>{$recipe['title']}</h2>";
        echo "<p>Cooking Time: {$recipe['cooking_time']} minutes</p>";
        // echo "<p>Type: {$recipe['type']}</p>";
        echo "<p>Category: {$recipe['category_name']}</p>";
        echo "<p>Uploader: {$recipe['uploader_name']}</p>";
        echo "<h3 id='h3-style'>Ingredients</h3>";
        
        $ingredientsSql = "SELECT * FROM ingredients WHERE recipe_id = $recipeId";
        $ingredientsResult = $conn->query($ingredientsSql);

        echo "<ul>";
        while ($ingredient = $ingredientsResult->fetch_assoc()) {
            echo "<li>{$ingredient['quantity']} {$ingredient['unit']} {$ingredient['ingredient_name']}</li>";
        }
        echo "</ul>";

        // Displaying the recipe content
        echo "<h3 id='h3-style'>Recipe Content</h3>";
        echo "<p>{$recipe['recipe']}</p>";

        if (!empty($recipe['photo'])) {
            echo "<p><img src='{$recipe['photo']}' alt='Recipe Photo' class='recipe-image'></p>";
        }

        if (!empty($recipe['video_link'])) {
            echo "<p><strong>Video Link:</strong> <a href='{$recipe['video_link']}' target='_blank'>Watch Video</a></p>";
        }
        echo "<hr>";
        echo "<h3 id='h3-style'>Comments</h3>";

        $commentsSql = "SELECT c.*, u.name AS commenter_name
                        FROM comments c
                        JOIN users u ON c.user_id = u.id
                        WHERE c.recipe_id = $recipeId
                        ORDER BY c.created_at DESC";
        $commentsResult = $conn->query($commentsSql);

        if ($commentsResult->num_rows > 0) {
            echo "<ul>";
            while ($comment = $commentsResult->fetch_assoc()) {
                echo "<li>{$comment['commenter_name']}: {$comment['content']}</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No comments yet.</p>";
        }

        echo "</div>";
        
    } else {
        echo "Recipe not found.";
    }
} else {
    echo "Recipe ID not provided.";
}

$conn->close();
echo "</div>";
echo "</body>";
echo "</html>";
?>

</body>
</html>