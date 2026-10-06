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
$recipeId = isset($_GET['recipe_id']) ? intval($_GET['recipe_id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);
$userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : (isset($_GET['author_id']) ? intval($_GET['author_id']) : 0);
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'rating'; // Mặc định hoặc theo tham số
$minRating = isset($_GET['min_rating']) ? floatval($_GET['min_rating']) : 0;
$maxTime = isset($_GET['max_time']) ? intval($_GET['max_time']) : 0;

$whereClauses = [];

if ($recipeId > 0) {
    $whereClauses[] = "recipes.id = $recipeId";
}

if ($userId > 0) {
    $whereClauses[] = "recipes.user_id = $userId";
}

if (!empty($category) && strtolower($category) !== 'all') {
    $safeCategory = $conn->real_escape_string($category);
    $whereClauses[] = "recipes.type = '$safeCategory'";
}

if (!empty($query)) {
    $safeQuery = $conn->real_escape_string($query);
    $whereClauses[] = "(recipes.title LIKE '%$safeQuery%' OR recipes.recipe LIKE '%$safeQuery%' OR recipes.description LIKE '%$safeQuery%')";
}

if ($maxTime > 0) {
    $whereClauses[] = "recipes.cooking_time <= $maxTime";
}

$whereSql = "";
if (count($whereClauses) > 0) {
    $whereSql = "WHERE " . implode(" AND ", $whereClauses);
}

// HAVING clause cho lọc đánh giá tối thiểu
$havingClauses = [];
if ($minRating > 0) {
    $havingClauses[] = "avg_rating >= $minRating";
}

$havingSql = "";
if (count($havingClauses) > 0) {
    $havingSql = "HAVING " . implode(" AND ", $havingClauses);
}

// Sắp xếp
$orderBySql = "COALESCE(AVG(ratings.rating), 0) DESC, rating_count DESC, recipes.id DESC"; // Mặc định ưu tiên đánh giá cao

switch ($sort) {
    case 'rating':
    case 'top_rated':
    case 'rating_desc':
        $orderBySql = "COALESCE(AVG(ratings.rating), 0) DESC, rating_count DESC, recipes.id DESC";
        break;
    case 'newest':
        $orderBySql = "recipes.id DESC";
        break;
    case 'time_asc':
        $orderBySql = "recipes.cooking_time ASC, recipes.id DESC";
        break;
    case 'popular':
        $orderBySql = "(COUNT(DISTINCT comments.id) + COUNT(ratings.id)) DESC, COALESCE(AVG(ratings.rating), 0) DESC, recipes.id DESC";
        break;
}

$sql = "SELECT recipes.id, recipes.title, recipes.cooking_time, recipes.photo, recipes.type, 
               recipes.description, recipes.user_id, u.name AS uploader_name,
               AVG(ratings.rating) AS avg_rating,
               COUNT(ratings.id) AS rating_count,
               COUNT(DISTINCT comments.id) AS comment_count
        FROM recipes
        LEFT JOIN users u ON recipes.user_id = u.id
        LEFT JOIN ratings ON recipes.id = ratings.recipe_id
        LEFT JOIN comments ON recipes.id = comments.recipe_id
        $whereSql
        GROUP BY recipes.id
        $havingSql
        ORDER BY $orderBySql";

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