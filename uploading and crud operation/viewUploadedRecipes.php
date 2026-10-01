<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
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
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT r.*, c.category_name 
        FROM recipes r 
        LEFT JOIN categories c ON r.category_id = c.id
        WHERE r.user_id = $userId 
        ORDER BY r.id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kho món ngon của bạn - Cook Together</title>
  <link rel="stylesheet" href="viewuploadedrecipes.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>

  <!-- NAVBAR -->
  <nav class="my-recipes-nav">
    <a href="index3.php" class="brand-link">
      <i class="fa-solid fa-utensils"></i> Cook Together
    </a>
    <div style="display: flex; gap: 12px;">
      <a href="index3.php" class="btn-action btn-view" style="padding: 10px 18px;">
        <i class="fa-solid fa-house"></i> Trang chủ
      </a>
      <a href="index4.php" class="btn-primary">
        <i class="fa-solid fa-plus"></i> Viết món mới
      </a>
    </div>
  </nav>

  <div class="container">
    
    <div class="page-header-box">
      <div>
        <h1><i class="fa-solid fa-book-bookmark text-primary"></i> Kho món ngon của bạn</h1>
        <p style="color: #6b7280; margin-top: 4px; font-size: 14px;">Quản lý, chỉnh sửa hoặc bổ sung các công thức bạn đã đăng tải.</p>
      </div>
      <a href="index4.php" class="btn-primary">
        <i class="fa-solid fa-plus"></i> Đăng món mới
      </a>
    </div>

    <div class="recipes-list">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <?php 
            $photo = !empty($row['photo']) ? $row['photo'] : 'upload.jpeg'; 
          ?>
          <div class="recipe-row-card">
            <div class="recipe-row-left">
              <img src="<?php echo htmlspecialchars($photo); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="recipe-thumbnail" onerror="this.src='landing_page.jpg'">
              <div>
                <h3 class="recipe-title-text"><?php echo htmlspecialchars($row['title']); ?></h3>
                <div class="recipe-tags-row">
                  <span><i class="fa-solid fa-utensils"></i> <?php echo htmlspecialchars($row['type']); ?></span>
                  <span>•</span>
                  <span><i class="fa-regular fa-clock"></i> <?php echo htmlspecialchars($row['cooking_time']); ?> phút</span>
                  <span>•</span>
                  <span><?php echo date("d/m/Y", strtotime($row['uploaded_at'])); ?></span>
                </div>
              </div>
            </div>

            <div class="row-actions">
              <a href="viewRecipeDetails.php?recipe_id=<?php echo $row['id']; ?>" class="btn-action btn-view">
                <i class="fa-solid fa-eye"></i> Xem
              </a>
              <button type="button" onclick="editRecipe(<?php echo $row['id']; ?>)" class="btn-action btn-edit">
                <i class="fa-solid fa-pen-to-square"></i> Sửa
              </button>
              <button type="button" onclick="deleteRecipe(<?php echo $row['id']; ?>)" class="btn-action btn-delete">
                <i class="fa-solid fa-trash-can"></i> Xóa
              </button>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div style="background: white; border: 1px dashed #f0ebe1; padding: 60px 20px; text-align: center; border-radius: 20px;">
          <i class="fa-solid fa-utensils" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px;"></i>
          <h3 style="font-size: 18px; margin-bottom: 8px;">Bạn chưa đăng công thức nào!</h3>
          <p style="color: #6b7280; margin-bottom: 20px;">Hãy bắt đầu chia sẻ bí quyết món ngon đầu tiên của bạn cho cộng đồng nhé.</p>
          <a href="index4.php" class="btn-primary">
            <i class="fa-solid fa-plus"></i> Viết món đầu tiên
          </a>
        </div>
      <?php endif; ?>
    </div>

  </div>

  <script>
    function editRecipe(recipeId) {
      var option = prompt("Bạn muốn chỉnh sửa mục nào?\n(Nhập: title, cooking_time, recipe, category, hoặc ingredients)");
      if (option) {
        window.location.href = "edit.php?recipe_id=" + recipeId + "&edit_option=" + encodeURIComponent(option.trim().toLowerCase());
      }
    }

    function deleteRecipe(recipeId) {
      if (confirm("Bạn có chắc chắn muốn xóa công thức này không? Thao tác này không thể hoàn tác.")) {
        window.location.href = "delete.php?recipe_id=" + recipeId;
      }
    }
  </script>
</body>
</html>
<?php
$conn->close();
?>