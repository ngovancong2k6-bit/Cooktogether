<!-- viewUploadedRecipes.php -->
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

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "recipe_sharing_Platform";

$conn = new mysqli($servername, $username, $password, $dbname,3306);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Your Uploaded Recipes</title>";
echo "<link rel='stylesheet' href='user_upload_style.css'>"; // Link to your external CSS file
echo "</head>";
echo "<body>";

$sql = "SELECT * FROM recipes WHERE user_id = $userId";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>Your Uploaded Recipes</h2>";
    while ($row = $result->fetch_assoc()) {
        echo "<div class='recipe-container'>";
        echo "<h3>{$row['title']}</h3>";
        // echo "<p>Cooking Time: {$row['cooking_time']} minutes</p>";
        // echo "<p>Category: {$row['type']}</p>";
        echo "<button onclick='viewRecipe({$row['id']})'>View</button>";
        echo "<button onclick='editRecipe({$row['id']})'>Edit</button>";
        echo "<button onclick='deleteRecipe({$row['id']})'>Delete</button>";
        echo "</div>";
    }
} else {
    echo "<p>No recipes uploaded by you.</p>";
}
echo "<button onclick='goBack()'>Back</button>";
$conn->close();
// Close the HTML document
echo "</body>";
echo "</html>";
?>

<script>
    function viewRecipe(recipeId) {
        window.location.href = "view.php?recipe_id=" + recipeId;
    }

    function editRecipe(recipeId) {
        var editOption = prompt("What do you want to edit? (recipe, title, cooking_time, category, ingredients)");
        if (editOption) {
            window.location.href = "edit.php?recipe_id=" + recipeId + "&edit_option=" + editOption;
        }
    }

    function deleteRecipe(recipeId) {
        var confirmDelete = confirm("Are you sure you want to delete this recipe?");
        if (confirmDelete) {
            window.location.href = "delete.php?recipe_id=" + recipeId;
        }
    }
    function goBack() {
        window.history.back();
    }
</script>
</body>
</html>