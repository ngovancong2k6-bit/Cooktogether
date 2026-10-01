<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "recipe_sharing_Platform";

$conn = new mysqli($servername, $username, $password, $dbname, 3306);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['recipe_id']) || empty($_GET['recipe_id'])) {
    header("Location: viewUploadedRecipes.php");
    exit();
}

$recipeId = intval($_GET['recipe_id']);
$userId = intval($_SESSION['user_id']);

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $cookingTime = intval($_POST['cooking_time']);
    $recipeText = $conn->real_escape_string($_POST['recipe']);
    $type = $conn->real_escape_string($_POST['type']);
    $description = isset($_POST['description']) ? $conn->real_escape_string($_POST['description']) : '';
    $videoLink = isset($_POST['video_link']) ? $conn->real_escape_string($_POST['video_link']) : '';

    // Get category id
    $catRes = $conn->query("SELECT id FROM categories WHERE category_name = '$type'");
    $catId = 1;
    if ($catRes && $catRes->num_rows > 0) {
        $catId = $catRes->fetch_assoc()['id'];
    }

    $updateSql = "UPDATE recipes SET 
                  title = '$title',
                  cooking_time = $cookingTime,
                  recipe = '$recipeText',
                  type = '$type',
                  category_id = $catId,
                  description = '$description',
                  video_link = '$videoLink'
                  WHERE id = $recipeId AND user_id = $userId";

    if ($conn->query($updateSql)) {
        // Update ingredients if provided
        if (isset($_POST['ingredients']) && is_array($_POST['ingredients'])) {
            $conn->query("DELETE FROM ingredients WHERE recipe_id = $recipeId");
            foreach ($_POST['ingredients'] as $key => $ingName) {
                if (empty($ingName)) continue;
                $safeName = $conn->real_escape_string($ingName);
                $qty = isset($_POST['quantities'][$key]) ? floatval($_POST['quantities'][$key]) : 1;
                $unit = isset($_POST['units'][$key]) ? $conn->real_escape_string($_POST['units'][$key]) : '';
                $conn->query("INSERT INTO ingredients (recipe_id, ingredient_name, quantity, unit) VALUES ($recipeId, '$safeName', $qty, '$unit')");
            }
        }

        echo "<script>alert('Cập nhật công thức thành công!'); window.location.href='viewUploadedRecipes.php';</script>";
        exit();
    } else {
        $error = "Lỗi cập nhật: " . $conn->error;
    }
}

// Fetch current recipe
$sql = "SELECT * FROM recipes WHERE id = $recipeId AND user_id = $userId";
$res = $conn->query($sql);
if (!$res || $res->num_rows === 0) {
    echo "<h1>Không tìm thấy hoặc bạn không có quyền chỉnh sửa công thức này!</h1><p><a href='viewUploadedRecipes.php'>Quay lại</a></p>";
    exit();
}
$recipe = $res->fetch_assoc();

// Fetch ingredients
$ingRes = $conn->query("SELECT * FROM ingredients WHERE recipe_id = $recipeId");
$ingredients = [];
if ($ingRes && $ingRes->num_rows > 0) {
    while ($ing = $ingRes->fetch_assoc()) {
        $ingredients[] = $ing;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chỉnh sửa công thức - Cook Together</title>
  <link rel="stylesheet" href="index4.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>

  <!-- NAVBAR -->
  <nav class="editor-navbar">
    <a href="index3.php" class="brand-link">
      <i class="fa-solid fa-utensils"></i> Cook Together
    </a>
    <div class="nav-actions">
      <a href="viewUploadedRecipes.php" class="btn-secondary">
        <i class="fa-solid fa-xmark"></i> Hủy bỏ
      </a>
      <button type="button" class="btn-primary" onclick="document.getElementById('editForm').submit()">
        <i class="fa-solid fa-check"></i> Lưu thay đổi
      </button>
    </div>
  </nav>

  <div class="editor-container">
    <form id="editForm" method="post">
      
      <div class="editor-card">
        <h2 class="section-title">
          <i class="fa-solid fa-pen-to-square text-primary"></i> Chỉnh sửa thông tin công thức
        </h2>

        <div class="form-group">
          <label class="form-label">Tên món ăn <span style="color: #ef4444;">*</span></label>
          <input type="text" name="title" class="input-title" value="<?php echo htmlspecialchars($recipe['title']); ?>" required>
        </div>

        <div class="form-group">
          <label class="form-label">Mô tả ngắn</label>
          <input type="text" name="description" class="form-control" value="<?php echo htmlspecialchars($recipe['description'] ?? ''); ?>">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Danh mục món <span style="color: #ef4444;">*</span></label>
            <select name="type" class="form-control" required>
              <option value="Vietnamese" <?php if ($recipe['type'] === 'Vietnamese') echo 'selected'; ?>>🇻🇳 Món Việt</option>
              <option value="NorthIndian" <?php if ($recipe['type'] === 'NorthIndian') echo 'selected'; ?>>🥘 Món Bắc Ấn</option>
              <option value="SouthIndian" <?php if ($recipe['type'] === 'SouthIndian') echo 'selected'; ?>>🍛 Món Nam Ấn</option>
              <option value="Chinese" <?php if ($recipe['type'] === 'Chinese') echo 'selected'; ?>>🥢 Món Hoa</option>
              <option value="Dessert" <?php if ($recipe['type'] === 'Dessert') echo 'selected'; ?>>🍰 Món tráng miệng</option>
              <option value="Drinks" <?php if ($recipe['type'] === 'Drinks') echo 'selected'; ?>>🍹 Thức uống</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Thời gian nấu (phút) <span style="color: #ef4444;">*</span></label>
            <input type="number" name="cooking_time" class="form-control" value="<?php echo htmlspecialchars($recipe['cooking_time']); ?>" min="1" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Video hướng dẫn (Tùy chọn)</label>
          <input type="url" name="video_link" class="form-control" value="<?php echo htmlspecialchars($recipe['video_link'] ?? ''); ?>">
        </div>
      </div>

      <!-- INGREDIENTS -->
      <div class="editor-card">
        <h2 class="section-title">
          <i class="fa-solid fa-carrot text-primary"></i> Nguyên liệu
        </h2>

        <div id="ingredientsList">
          <?php if (count($ingredients) > 0): ?>
            <?php foreach ($ingredients as $ing): ?>
              <div class="ingredient-row">
                <input type="text" name="ingredients[]" class="form-control" value="<?php echo htmlspecialchars($ing['ingredient_name']); ?>" required>
                <input type="number" step="any" name="quantities[]" class="form-control" value="<?php echo (float)$ing['quantity']; ?>" required>
                <input type="text" name="units[]" class="form-control" value="<?php echo htmlspecialchars($ing['unit']); ?>" required>
                <button type="button" class="btn-delete-row" onclick="removeIngredientRow(this)" title="Xóa dòng">
                  <i class="fa-solid fa-trash-can"></i>
                </button>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="ingredient-row">
              <input type="text" name="ingredients[]" class="form-control" placeholder="Tên nguyên liệu" required>
              <input type="number" step="any" name="quantities[]" class="form-control" placeholder="Số lượng" required>
              <input type="text" name="units[]" class="form-control" placeholder="Đơn vị" required>
              <button type="button" class="btn-delete-row" onclick="removeIngredientRow(this)" title="Xóa dòng">
                <i class="fa-solid fa-trash-can"></i>
              </button>
            </div>
          <?php endif; ?>
        </div>

        <button type="button" class="btn-add-ingredient" onclick="addIngredientRow()">
          <i class="fa-solid fa-plus"></i> Thêm nguyên liệu
        </button>
      </div>

      <!-- INSTRUCTIONS -->
      <div class="editor-card">
        <h2 class="section-title">
          <i class="fa-solid fa-fire-burner text-primary"></i> Hướng dẫn các bước thực hiện
        </h2>

        <div class="form-group">
          <textarea name="recipe" class="form-control" style="min-height: 200px;" required><?php echo htmlspecialchars($recipe['recipe']); ?></textarea>
        </div>
      </div>

      <div class="bottom-submit-bar">
        <a href="viewUploadedRecipes.php" class="btn-secondary">Hủy bỏ</a>
        <button type="submit" class="btn-primary">
          <i class="fa-solid fa-check"></i> Lưu thay đổi
        </button>
      </div>

    </form>
  </div>

  <script>
    function addIngredientRow() {
      var list = document.getElementById("ingredientsList");
      var row = document.createElement("div");
      row.className = "ingredient-row";
      row.innerHTML = `
        <input type="text" name="ingredients[]" class="form-control" placeholder="Tên nguyên liệu" required>
        <input type="number" step="any" name="quantities[]" class="form-control" placeholder="Số lượng" required>
        <input type="text" name="units[]" class="form-control" placeholder="Đơn vị" required>
        <button type="button" class="btn-delete-row" onclick="removeIngredientRow(this)" title="Xóa dòng">
          <i class="fa-solid fa-trash-can"></i>
        </button>
      `;
      list.appendChild(row);
    }

    function removeIngredientRow(button) {
      var list = document.getElementById("ingredientsList");
      if (list.getElementsByClassName("ingredient-row").length > 1) {
        button.closest(".ingredient-row").remove();
      } else {
        alert("Công thức cần ít nhất 1 dòng nguyên liệu!");
      }
    }
  </script>
</body>
</html>
<?php
$conn->close();
?>
