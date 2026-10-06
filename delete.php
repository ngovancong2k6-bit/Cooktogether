<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$userId = intval($_SESSION['user_id']);

if (!isset($_GET['recipe_id']) || empty($_GET['recipe_id'])) {
    header("Location: viewUploadedRecipes.php");
    exit();
}

$recipeId = intval($_GET['recipe_id']);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "recipe_sharing_Platform";

$conn = new mysqli($servername, $username, $password, $dbname, 3306);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// 1. Kiểm tra xem món ăn này có tồn tại và thuộc quyền sở hữu của user không
$checkSql = "SELECT id, title, photo FROM recipes WHERE id = $recipeId AND user_id = $userId";
$checkRes = $conn->query($checkSql);

if (!$checkRes || $checkRes->num_rows === 0) {
    echo "<script>
        alert('Bạn không có quyền xóa hoặc công thức này không tồn tại!');
        window.location.href = 'viewUploadedRecipes.php';
    </script>";
    exit();
}

$recipeData = $checkRes->fetch_assoc();
$recipeTitle = $recipeData['title'];
$photoFile = $recipeData['photo'];

// 2. Xóa dữ liệu liên kết ở tất cả các bảng liên quan (Cascade Clean)
$conn->query("DELETE FROM ratings WHERE recipe_id = $recipeId");
$conn->query("DELETE FROM comments WHERE recipe_id = $recipeId");
$conn->query("DELETE FROM ingredients WHERE recipe_id = $recipeId");

// 3. Xóa món ăn khỏi bảng recipes
$deleteRecipeSql = "DELETE FROM recipes WHERE id = $recipeId AND user_id = $userId";

if ($conn->query($deleteRecipeSql) === TRUE) {
    // Nếu có file ảnh local được upload, xóa file để giải phóng dung lượng
    if (!empty($photoFile) && strpos($photoFile, 'http') === false) {
        $filePath = __DIR__ . '/' . $photoFile;
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    echo "<script>
        alert('✓ Đã xóa công thức \"".addslashes($recipeTitle)."\" thành công!');
        window.location.href = 'viewUploadedRecipes.php';
    </script>";
    exit();
} else {
    echo "<script>
        alert('Lỗi khi xóa công thức: ".addslashes($conn->error)."');
        window.location.href = 'viewUploadedRecipes.php';
    </script>";
    exit();
}

$conn->close();
?>
