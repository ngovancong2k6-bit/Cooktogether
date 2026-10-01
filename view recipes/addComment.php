<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Vui lòng đăng nhập để bình luận!']);
    exit();
}

$userId = intval($_SESSION['user_id']);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "recipe_sharing_Platform";

$conn = new mysqli($servername, $username, $password, $dbname, 3306);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    http_response_code(500);
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $recipeId = isset($_POST["recipe_id"]) ? intval($_POST["recipe_id"]) : 0;
    $content = isset($_POST["content"]) ? trim($_POST["content"]) : (isset($_POST["comment"]) ? trim($_POST["comment"]) : '');

    if ($recipeId <= 0 || empty($content)) {
        http_response_code(400);
        echo json_encode(['error' => 'Dữ liệu bình luận không hợp lệ!']);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO comments (recipe_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $recipeId, $userId, $content);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Bình luận thành công']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Lỗi lưu bình luận: ' . $conn->error]);
    }
    $stmt->close();
}

$conn->close();
?>
