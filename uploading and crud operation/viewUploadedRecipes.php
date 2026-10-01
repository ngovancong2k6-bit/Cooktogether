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

// Lấy danh sách món ăn của user kèm số nguyên liệu và rating trung bình
$sql = "SELECT r.*, c.category_name,
               COUNT(DISTINCT ing.id) AS total_ingredients,
               AVG(rt.rating) AS avg_rating
        FROM recipes r 
        LEFT JOIN categories c ON r.category_id = c.id
        LEFT JOIN ingredients ing ON r.id = ing.recipe_id
        LEFT JOIN ratings rt ON r.id = rt.recipe_id
        WHERE r.user_id = $userId 
        GROUP BY r.id
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
  <style>
    .recipe-meta-badges {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 6px;
    }
    .meta-badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 4px 10px;
      background: #f3f4f6;
      border-radius: 9999px;
      font-size: 12px;
      font-weight: 600;
      color: #4b5563;
    }
    .meta-badge.rating {
      background: #fef3c7;
      color: #d97706;
    }
    .meta-badge.category {
      background: #fff7ed;
      color: #e27227;
    }
  </style>
</head>
<body>

  <!-- NAVBAR -->
  <nav class="my-recipes-nav">
    <a href="index3.php" class="brand-link">
      <i class="fa-solid fa-utensils"></i> Cook Together
    </a>
    <div style="display: flex; gap: 12px;">
      <a href="index3.php" class="btn-action btn-view" style="padding: 10px 18px; border-radius: 9999px;">
        <i class="fa-solid fa-house"></i> Trang chủ Feed
      </a>
      <a href="index4.php" class="btn-primary" style="padding: 10px 20px; border-radius: 9999px;">
        <i class="fa-solid fa-plus"></i> Đăng món mới
      </a>
    </div>
  </nav>

  <div class="container">
    
    <div class="page-header-box">
      <div>
        <h1><i class="fa-solid fa-book-bookmark text-primary"></i> Kho món ngon của bạn</h1>
        <p style="color: #6b7280; margin-top: 4px; font-size: 14px;">Quản lý toàn bộ danh sách công thức do bạn sáng tạo và đăng tải.</p>
      </div>
      <a href="index4.php" class="btn-primary">
        <i class="fa-solid fa-plus"></i> Viết công thức mới
      </a>
    </div>

    <div class="recipes-list">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <?php 
            $photo = !empty($row['photo']) ? $row['photo'] : 'upload.jpeg'; 
            $ratingVal = $row['avg_rating'] ? number_format((float)$row['avg_rating'], 1) : "5.0";
          ?>
          <div class="recipe-row-card">
            <div class="recipe-row-left">
              <img src="<?php echo htmlspecialchars($photo); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="recipe-thumbnail" onerror="this.src='landing_page.jpg'">
              <div>
                <h3 class="recipe-title-text"><?php echo htmlspecialchars($row['title']); ?></h3>
                
                <div class="recipe-meta-badges">
                  <span class="meta-badge category">
                    <i class="fa-solid fa-utensils"></i> <?php echo htmlspecialchars($row['type']); ?>
                  </span>
                  <span class="meta-badge">
                    <i class="fa-regular fa-clock"></i> <?php echo htmlspecialchars($row['cooking_time']); ?> phút
                  </span>
                  <span class="meta-badge">
                    <i class="fa-solid fa-carrot"></i> <?php echo htmlspecialchars($row['total_ingredients']); ?> nguyên liệu
                  </span>
                  <span class="meta-badge rating">
                    <i class="fa-solid fa-star"></i> <?php echo $ratingVal; ?>
                  </span>
                </div>

                <?php if (!empty($row['description'])): ?>
                  <p style="font-size: 13px; color: #6b7280; margin-top: 8px; max-width: 600px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    <?php echo htmlspecialchars($row['description']); ?>
                  </p>
                <?php endif; ?>
              </div>
            </div>

            <div class="recipe-row-actions">
              <a href="viewRecipeDetails.php?recipe_id=<?php echo $row['id']; ?>" class="btn-action btn-view" title="Xem chi tiết món">
                <i class="fa-regular fa-eye"></i> Xem
              </a>
              <a href="edit.php?recipe_id=<?php echo $row['id']; ?>" class="btn-action btn-edit" title="Chỉnh sửa công thức">
                <i class="fa-regular fa-pen-to-square"></i> Sửa
              </a>
              <button onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo addslashes($row['title']); ?>')" class="btn-action btn-delete" title="Xóa món ăn">
                <i class="fa-regular fa-trash-can"></i> Xóa
              </button>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="empty-state-box">
          <i class="fa-solid fa-utensils"></i>
          <h3>Bạn chưa đăng tải công thức nào!</h3>
          <p>Hãy chia sẻ món ngon đầu tiên của bạn cho cộng đồng đầu bếp Cook Together ngay hôm nay.</p>
          <a href="index4.php" class="btn-primary" style="margin-top: 16px; display: inline-flex;">
            <i class="fa-solid fa-plus"></i> Đăng món đầu tiên
          </a>
        </div>
      <?php endif; ?>
    </div>

  </div>

  <script>
    function confirmDelete(recipeId, recipeTitle) {
      if (confirm('Bạn có chắc chắn muốn xóa công thức "' + recipeTitle + '" không?\nHành động này không thể khôi phục!')) {
        window.location.href = 'delete.php?recipe_id=' + recipeId;
      }
    }
  </script>

</body>
</html>