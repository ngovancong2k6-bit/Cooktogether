<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$userId = intval($_SESSION['user_id']);

// Kết nối cơ sở dữ liệu
$dsn = 'mysql:host=localhost;port=3306;dbname=recipe_sharing_platform;charset=utf8mb4';
try {
    $pdo = new PDO($dsn, 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage());
}

// Kiểm tra method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index4.php");
    exit();
}

// Validate dữ liệu bắt buộc
$title = trim($_POST['title'] ?? '');
$cookingTime = intval($_POST['cooking_time'] ?? 30);
$type = trim($_POST['type'] ?? 'Vietnamese');
$description = trim($_POST['description'] ?? '');
$instructions = trim($_POST['instructions'] ?? '');
$videoLink = trim($_POST['video_link'] ?? '');
$photoUrl = trim($_POST['photo_url'] ?? '');

if (empty($title) || empty($type)) {
    echo "<script>alert('Vui lòng điền đầy đủ tên món ăn và danh mục!'); window.history.back();</script>";
    exit();
}

// Xử lý các bước nấu (steps[])
$steps = $_POST['steps'] ?? [];
$recipeText = '';
if (!empty($steps) && is_array($steps)) {
    $formattedSteps = [];
    $i = 1;
    foreach ($steps as $step) {
        $stepTrim = trim($step);
        if (!empty($stepTrim)) {
            $formattedSteps[] = "Bước " . $i . ": " . $stepTrim;
            $i++;
        }
    }
    $recipeText = implode("\n", $formattedSteps);
} else {
    $recipeText = trim($_POST['recipe'] ?? '');
}

if (empty($recipeText)) {
    $recipeText = "1. Chuẩn bị và sơ chế nguyên liệu sạch sẽ.\n2. Chế biến theo khẩu vị gia đình.\n3. Bày ra đĩa và thưởng thức khi còn nóng.";
}

// Lấy category_id tương ứng
$catStmt = $pdo->prepare('SELECT id FROM categories WHERE category_name = :category_name LIMIT 1');
$catStmt->execute([':category_name' => $type]);
$categoryId = $catStmt->fetchColumn();
if (!$categoryId) {
    $categoryId = 6; // Mặc định Vietnamese nếu không tìm thấy
}

// Xử lý hình ảnh
$finalPhoto = '';
if (isset($_FILES['image']) && $_FILES['image']['size'] > 0 && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (in_array($ext, $allowed)) {
        $newFileName = 'recipe_' . time() . '_' . rand(100, 999) . '.' . $ext;
        $targetPath = __DIR__ . '/' . $newFileName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $finalPhoto = $newFileName;
            $syncDirs = [
                __DIR__ . '/..',
                __DIR__ . '/../view recipes',
                __DIR__ . '/../main page',
                __DIR__ . '/../uploading and crud operation',
                __DIR__ . '/../images',
                'C:/xampp/htdocs/recipe-sharing-platform',
                'C:/xampp/htdocs/recipe-sharing-platform/view recipes',
                'C:/xampp/htdocs/recipe-sharing-platform/main page',
                'C:/xampp/htdocs/recipe-sharing-platform/uploading and crud operation',
                'C:/xampp/htdocs/recipe-sharing-platform/images'
            ];
            foreach ($syncDirs as $sDir) {
                if (is_dir($sDir)) {
                    @copy($targetPath, $sDir . '/' . $newFileName);
                }
            }
        }
    }
}

// Nếu không upload file mà có nhập URL
if (empty($finalPhoto) && !empty($photoUrl)) {
    $finalPhoto = $photoUrl;
}

// Nếu vẫn chưa có ảnh, dùng ảnh mặc định
if (empty($finalPhoto)) {
    $finalPhoto = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800';
}

// Chuẩn hóa link video YouTube
if (!empty($videoLink)) {
    if (!filter_var($videoLink, FILTER_VALIDATE_URL)) {
        $videoLink = '';
    }
}

// 1. Chèn vào bảng recipes
$sql = "INSERT INTO recipes (title, cooking_time, recipe, type, category_id, photo, video_link, description, instructions, user_id, uploaded_at)
        VALUES (:title, :cooking_time, :recipe, :type, :category_id, :photo, :video_link, :description, :instructions, :user_id, NOW())";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':title' => $title,
    ':cooking_time' => $cookingTime,
    ':recipe' => $recipeText,
    ':type' => $type,
    ':category_id' => $categoryId,
    ':photo' => $finalPhoto,
    ':video_link' => $videoLink,
    ':description' => $description,
    ':instructions' => $instructions,
    ':user_id' => $userId
]);

$recipeId = $pdo->lastInsertId();

// 2. Chèn danh sách nguyên liệu vào bảng ingredients
if (!empty($_POST['ingredients']) && is_array($_POST['ingredients'])) {
    $ingStmt = $pdo->prepare('INSERT INTO ingredients (recipe_id, ingredient_name, quantity, unit) VALUES (:recipe_id, :ingredient_name, :quantity, :unit)');
    
    foreach ($_POST['ingredients'] as $key => $ingName) {
        $ingName = trim($ingName);
        if (empty($ingName)) continue;
        
        $qty = isset($_POST['quantities'][$key]) ? floatval($_POST['quantities'][$key]) : 1;
        $unit = isset($_POST['units'][$key]) ? trim($_POST['units'][$key]) : 'phần';
        
        $ingStmt->execute([
            ':recipe_id' => $recipeId,
            ':ingredient_name' => $ingName,
            ':quantity' => $qty,
            ':unit' => $unit
        ]);
    }
}

// Chuyển hướng đến chi tiết món vừa tạo
header("Location: viewRecipeDetails.php?recipe_id=" . $recipeId);
exit();
