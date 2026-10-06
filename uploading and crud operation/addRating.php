<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["error" => "Vui lòng đăng nhập để đánh giá món ăn!"]);
    exit();
}

$userId = intval($_SESSION["user_id"]);
$recipeId = isset($_POST['recipe_id']) ? intval($_POST['recipe_id']) : 0;
$rating = isset($_POST['rating']) ? floatval($_POST['rating']) : 0;

if ($recipeId <= 0 || $rating < 1 || $rating > 5) {
    http_response_code(400);
    echo json_encode(["error" => "Số sao đánh giá không hợp lệ!"]);
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "recipe_sharing_Platform";

$conn = new mysqli($servername, $username, $password, $dbname, 3306);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["error" => "Lỗi kết nối CSDL: " . $conn->connect_error]));
}

// Kiểm tra xem đã từng đánh giá chưa
$checkStmt = $conn->prepare("SELECT id FROM ratings WHERE user_id = ? AND recipe_id = ?");
$checkStmt->bind_param("ii", $userId, $recipeId);
$checkStmt->execute();
$checkRes = $checkStmt->get_result();

if ($checkRes->num_rows > 0) {
    // Cập nhật lại số sao mới
    $updateStmt = $conn->prepare("UPDATE ratings SET rating = ? WHERE user_id = ? AND recipe_id = ?");
    $updateStmt->bind_param("dii", $rating, $userId, $recipeId);
    $updateStmt->execute();
    $updateStmt->close();
    echo json_encode(["success" => true, "message" => "Đã cập nhật đánh giá thành công!"]);
} else {
    // Thêm đánh giá mới
    $insertStmt = $conn->prepare("INSERT INTO ratings (user_id, recipe_id, rating) VALUES (?, ?, ?)");
    $insertStmt->bind_param("iid", $userId, $recipeId, $rating);
    $insertStmt->execute();
    $insertStmt->close();
    echo json_encode(["success" => true, "message" => "Đánh giá món ăn thành công!"]);
}

$checkStmt->close();
$conn->close();
?>
