<?php
// getRecipes.php
header('Content-Type: application/json; charset=utf-8');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "recipe_sharing_Platform";

$conn = new mysqli($servername, $username, $password, $dbname, 3306);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

$whereClauses = [];

if (!empty($category) && strtolower($category) !== 'all') {
    $safeCategory = $conn->real_escape_string($category);
    $whereClauses[] = "recipes.type = '$safeCategory'";
}

if (!empty($query)) {
    $safeQuery = $conn->real_escape_string($query);
    $whereClauses[] = "(recipes.title LIKE '%$safeQuery%' OR recipes.recipe LIKE '%$safeQuery%' OR recipes.description LIKE '%$safeQuery%')";
}

$whereSql = "";
if (count($whereClauses) > 0) {
    $whereSql = "WHERE " . implode(" AND ", $whereClauses);
}

$sql = "SELECT recipes.id, recipes.title, recipes.cooking_time, recipes.photo, recipes.type, 
               recipes.description, u.name AS uploader_name,
               AVG(ratings.rating) AS avg_rating,
               COUNT(DISTINCT comments.id) AS comment_count
        FROM recipes
        LEFT JOIN users u ON recipes.user_id = u.id
        LEFT JOIN ratings ON recipes.id = ratings.recipe_id
        LEFT JOIN comments ON recipes.id = comments.recipe_id
        $whereSql
        GROUP BY recipes.id
        ORDER BY recipes.id DESC";

$result = $conn->query($sql);

$recipes = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recipes[] = $row;
    }
    echo json_encode($recipes);
} else {
    echo json_encode(["error" => $conn->error]);
}

$conn->close();
?>