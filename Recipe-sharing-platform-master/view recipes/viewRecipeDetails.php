<?php
// viewRecipeDetails.php

// Connect to the database (replace with your database credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "recipe_sharing_Platform";

$conn = new mysqli($servername, $username, $password, $dbname,3306);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get recipe ID from the query parameter
$recipeId = $_GET['recipe_id'];

// Fetch recipe details based on the selected recipe ID
$sql = "SELECT r.*, u.name AS uploader_name, c.category_name
        FROM recipes r
        JOIN users u ON r.user_id = u.id
        JOIN categories c ON r.category_id = c.id
        WHERE r.id = $recipeId";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $recipe = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $recipe['title']; ?></title>
    <link rel="stylesheet" href="index52.css"> <!-- Add your CSS file -->
</head>
<body>
    <div class="container">
        <h1><?php echo $recipe['title']; ?></h1>
        <p><strong>Cooking Time:</strong> <?php echo $recipe['cooking_time']; ?> minutes</p>
        <p><strong>Category:</strong> <?php echo $recipe['category_name']; ?></p>
        <p><strong>Uploader:</strong> <?php echo $recipe['uploader_name']; ?></p>
        <h2>Ingredients</h2>
        <ul>
            <?php
            // Fetch ingredients for the recipe
            $ingredientSql = "SELECT * FROM ingredients WHERE recipe_id = $recipeId";
            $ingredientResult = $conn->query($ingredientSql);

            if ($ingredientResult->num_rows > 0) {
                while ($ingredient = $ingredientResult->fetch_assoc()) {
                    echo "<li>{$ingredient['quantity']} {$ingredient['unit']} - {$ingredient['ingredient_name']}</li>";
                }
            }
            ?>
        </ul>
        <h2>Recipe</h2>
        <p><?php echo $recipe['recipe']; ?></p>
        <?php
            // Display photo and video links if available
            if (!empty($recipe['photo'])) {
                echo "<p><strong>Photo:</strong> <img src='{$recipe['photo']}' alt='Recipe Photo'></p>";
            }

            if (!empty($recipe['video_link'])) {
                echo "<p><strong>Video Link:</strong> <a href='{$recipe['video_link']}' target='_blank'>Watch Video</a></p>";
            }
        ?>

        <h2>Comments</h2>
        <div id="comments-list">
            <?php
                // Fetch comments for the recipe
                $commentsSql = "SELECT c.*, u.name AS commenter_name
                                FROM comments c
                                JOIN users u ON c.user_id = u.id
                                WHERE c.recipe_id = $recipeId
                                ORDER BY c.created_at DESC";
                $commentsResult = $conn->query($commentsSql);

                if ($commentsResult->num_rows > 0) {
                    while ($comment = $commentsResult->fetch_assoc()) {
                        echo "<div class='comment'>";
                        echo "<p><strong>{$comment['commenter_name']}:</strong> {$comment['content']}</p>";
                        echo "<p class='comment-time'>{$comment['created_at']}</p>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No comments yet. Be the first to comment!</p>";
                }
            ?>
        </div>

        <h2>Add a Comment</h2>
        <form id="comment-form">
            <textarea id="comment-content" placeholder="Write your comment"></textarea>
            <button type="button" onclick="addComment()">Add Comment</button>
        </form>
        <h2>Rate this Recipe</h2>
        <form id="rating-form">
            <select id="rating" name="rating">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
            <button type="button" onclick="addRating()">Rate</button>
        </form>
    </div>

    <script>
        function addComment() {
            var content = document.getElementById("comment-content").value;

            if (content.trim() === "") {
                alert("Please enter a comment.");
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Reload the page to display the new comment
                    location.reload();
                }
            };
            xhr.open("POST", "addComment.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send("recipe_id=<?php echo $recipeId; ?>&content=" + encodeURIComponent(content));
        }
        function addRating() {
            var rating = document.getElementById("rating").value;

            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Reload the page to reflect the new rating
                    location.reload();
                }
            };
            xhr.open("POST", "addRating.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send("recipe_id=<?php echo $recipeId; ?>&rating=" + rating);
        }
    </script>
</body>
</html>

<?php
} else {
    echo "Recipe not found.";
}

$conn->close();
?>
