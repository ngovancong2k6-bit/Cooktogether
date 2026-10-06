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

// Xử lý khi Submit Form cập nhật (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string(trim($_POST['title']));
    $cookingTime = intval($_POST['cooking_time']);
    $type = $conn->real_escape_string(trim($_POST['type']));
    $description = isset($_POST['description']) ? $conn->real_escape_string(trim($_POST['description'])) : '';
    $instructions = isset($_POST['instructions']) ? $conn->real_escape_string(trim($_POST['instructions'])) : '';
    $videoLink = isset($_POST['video_link']) ? $conn->real_escape_string(trim($_POST['video_link'])) : '';
    $photoUrl = isset($_POST['photo_url']) ? trim($_POST['photo_url']) : '';
    $currentPhoto = isset($_POST['current_photo']) ? $_POST['current_photo'] : '';

    // Xử lý các bước nấu (steps[])
    $steps = $_POST['steps'] ?? [];
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
        $recipeText = $conn->real_escape_string(implode("\n", $formattedSteps));
    } else {
        $recipeText = $conn->real_escape_string(trim($_POST['recipe'] ?? ''));
    }

    // Lấy category_id
    $catRes = $conn->query("SELECT id FROM categories WHERE category_name = '$type'");
    $catId = 6;
    if ($catRes && $catRes->num_rows > 0) {
        $catId = $catRes->fetch_assoc()['id'];
    }

    // Xử lý ảnh cập nhật
    $finalPhoto = $currentPhoto;
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
    } elseif (!empty($photoUrl)) {
        $finalPhoto = $photoUrl;
    }
    $finalPhoto = $conn->real_escape_string($finalPhoto);

    // Cập nhật bảng recipes
    $updateSql = "UPDATE recipes SET 
                  title = '$title',
                  cooking_time = $cookingTime,
                  recipe = '$recipeText',
                  type = '$type',
                  category_id = $catId,
                  description = '$description',
                  instructions = '$instructions',
                  photo = '$finalPhoto',
                  video_link = '$videoLink'
                  WHERE id = $recipeId AND user_id = $userId";

    if ($conn->query($updateSql)) {
        // Cập nhật lại toàn bộ danh sách nguyên liệu
        if (isset($_POST['ingredients']) && is_array($_POST['ingredients'])) {
            $conn->query("DELETE FROM ingredients WHERE recipe_id = $recipeId");
            foreach ($_POST['ingredients'] as $key => $ingName) {
                $ingName = trim($ingName);
                if (empty($ingName)) continue;
                $safeName = $conn->real_escape_string($ingName);
                $qty = isset($_POST['quantities'][$key]) ? floatval($_POST['quantities'][$key]) : 1;
                $unit = isset($_POST['units'][$key]) ? $conn->real_escape_string(trim($_POST['units'][$key])) : 'phần';
                $conn->query("INSERT INTO ingredients (recipe_id, ingredient_name, quantity, unit) VALUES ($recipeId, '$safeName', $qty, '$unit')");
            }
        }

        echo "<script>alert('✓ Cập nhật công thức thành công!'); window.location.href='viewRecipeDetails.php?recipe_id=$recipeId';</script>";
        exit();
    } else {
        $error = "Lỗi cập nhật: " . $conn->error;
    }
}

// Lấy thông tin công thức hiện tại
$sql = "SELECT * FROM recipes WHERE id = $recipeId AND user_id = $userId";
$res = $conn->query($sql);
if (!$res || $res->num_rows === 0) {
    echo "<div style='font-family: sans-serif; text-align: center; padding: 50px;'><h2>Không tìm thấy công thức hoặc bạn không có quyền chỉnh sửa!</h2><p><a href='viewUploadedRecipes.php'>Quay lại danh sách</a></p></div>";
    exit();
}
$recipe = $res->fetch_assoc();

// Lấy danh sách nguyên liệu
$ingRes = $conn->query("SELECT * FROM ingredients WHERE recipe_id = $recipeId");
$ingredients = [];
if ($ingRes && $ingRes->num_rows > 0) {
    while ($ing = $ingRes->fetch_assoc()) {
        $ingredients[] = $ing;
    }
}

// Tách các bước nấu thành mảng
$recipeText = $recipe['recipe'] ?? '';
$stepLines = explode("\n", str_replace(["\r\n", "\r"], "\n", $recipeText));
$existingSteps = [];
foreach ($stepLines as $line) {
    $lineTrim = trim($line);
    if (!empty($lineTrim)) {
        // Bỏ tiền tố "Bước X: " hoặc "1. " nếu có
        $cleanStep = preg_replace('/^(Bước\s*\d+[:.]?|\d+[.:])\s*/iu', '', $lineTrim);
        $existingSteps[] = $cleanStep;
    }
}
if (empty($existingSteps)) {
    $existingSteps = [$recipeText];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chỉnh sửa công thức - <?php echo htmlspecialchars($recipe['title']); ?></title>
  <link rel="stylesheet" href="index4.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    .image-type-toggle {
      display: flex;
      gap: 12px;
      margin-bottom: 16px;
    }
    .image-type-btn {
      flex: 1;
      padding: 10px;
      background: #f3f4f6;
      border: 2px solid transparent;
      border-radius: 10px;
      font-weight: 600;
      font-size: 14px;
      color: #4b5563;
      cursor: pointer;
      text-align: center;
      transition: all 0.2s;
    }
    .image-type-btn.active {
      background: #fff7ed;
      border-color: #e27227;
      color: #e27227;
    }
    .step-row {
      display: flex;
      gap: 12px;
      align-items: flex-start;
      margin-bottom: 14px;
      background: #fafaf9;
      padding: 14px;
      border-radius: 12px;
      border: 1px solid #f0ebe1;
    }
    .step-number-badge {
      width: 32px;
      height: 32px;
      background: #e27227;
      color: #fff;
      font-weight: 700;
      font-size: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      flex-shrink: 0;
      margin-top: 4px;
    }
  </style>
</head>
<body>

  <!-- NAVBAR -->
  <nav class="editor-navbar">
    <a href="index3.php" class="brand-link">
      <i class="fa-solid fa-utensils"></i> Cook Together
    </a>
    <div class="nav-actions">
      <a href="viewRecipeDetails.php?recipe_id=<?php echo $recipeId; ?>" class="btn-secondary">
        <i class="fa-solid fa-xmark"></i> Hủy bỏ
      </a>
      <button type="button" class="btn-primary" onclick="submitForm()">
        <i class="fa-solid fa-check"></i> Lưu thay đổi
      </button>
    </div>
  </nav>

  <div class="editor-container">
    <form id="editForm" method="post" enctype="multipart/form-data">
      <input type="hidden" name="current_photo" value="<?php echo htmlspecialchars($recipe['photo']); ?>">

      <!-- CARD 1: HÌNH ẢNH MÓN ĂN -->
      <div class="editor-card">
        <h2 class="section-title">
          <i class="fa-solid fa-camera text-primary"></i> Hình ảnh món ăn
        </h2>

        <div style="margin-bottom: 16px; display: flex; align-items: center; gap: 16px; background: #fffaf5; padding: 12px 16px; border-radius: 12px; border: 1px solid #fed7aa;">
          <img src="<?php echo htmlspecialchars($recipe['photo'] ? $recipe['photo'] : 'upload.jpeg'); ?>" alt="Ảnh hiện tại" style="width: 80px; height: 80px; border-radius: 10px; object-fit: cover;" onerror="this.src='landing_page.jpg'">
          <div>
            <div style="font-weight: 700; color: #1f2937;">Ảnh hiện tại</div>
            <div style="font-size: 13px; color: #6b7280; word-break: break-all;"><?php echo htmlspecialchars($recipe['photo']); ?></div>
          </div>
        </div>

        <div class="image-type-toggle">
          <button type="button" class="image-type-btn active" id="btnUploadType" onclick="switchImageType('upload')">
            <i class="fa-solid fa-arrow-up-from-bracket"></i> Tải ảnh mới từ máy
          </button>
          <button type="button" class="image-type-btn" id="btnUrlType" onclick="switchImageType('url')">
            <i class="fa-solid fa-link"></i> Đổi link ảnh trực tiếp (URL)
          </button>
        </div>

        <!-- UPLOAD TỪ MÁY -->
        <div id="uploadBox" class="photo-upload-zone" onclick="document.getElementById('imageInput').click()">
          <input type="file" id="imageInput" name="image" accept="image/png,image/jpeg,image/webp,image/jpg" style="display: none;" onchange="previewImageFile(this)">
          <div id="uploadPlaceholder">
            <i class="fa-solid fa-cloud-arrow-up upload-icon"></i>
            <div class="upload-text">Nhấp nếu muốn tải ảnh mới thay thế</div>
            <div class="upload-hint">Bỏ qua nếu muốn giữ nguyên ảnh hiện tại</div>
          </div>
          <img id="photoPreview" class="photo-preview" alt="Xem trước ảnh mới">
        </div>

        <!-- NHẬP URL ẢNH -->
        <div id="urlBox" style="display: none; margin-bottom: 16px;">
          <div class="form-group">
            <label class="form-label">Đổi đường dẫn ảnh (URL Image)</label>
            <input type="url" id="imageUrlInput" name="photo_url" class="form-control" placeholder="https://..." value="<?php echo (strpos($recipe['photo'], 'http') === 0) ? htmlspecialchars($recipe['photo']) : ''; ?>" oninput="previewImageUrl(this.value)">
          </div>
          <div style="text-align: center;">
            <img id="urlPhotoPreview" style="max-height: 250px; border-radius: 12px; display: none; margin: 0 auto; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" alt="Xem trước URL">
          </div>
        </div>
      </div>

      <!-- CARD 2: THÔNG TIN CƠ BẢN -->
      <div class="editor-card">
        <h2 class="section-title">
          <i class="fa-solid fa-pen-to-square text-primary"></i> Thông tin công thức
        </h2>

        <div class="form-group">
          <label class="form-label">Tên món ăn <span style="color: #ef4444;">*</span></label>
          <input type="text" name="title" class="input-title" value="<?php echo htmlspecialchars($recipe['title']); ?>" required>
        </div>

        <div class="form-group">
          <label class="form-label">Mô tả ngắn</label>
          <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($recipe['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Danh mục món <span style="color: #ef4444;">*</span></label>
            <select name="type" class="form-control" required>
              <option value="Vietnamese" <?php if ($recipe['type'] === 'Vietnamese') echo 'selected'; ?>>🍲 Món Việt (Vietnamese)</option>
              <option value="NorthIndian" <?php if ($recipe['type'] === 'NorthIndian') echo 'selected'; ?>>🥘 Món Bắc Ấn (North Indian)</option>
              <option value="SouthIndian" <?php if ($recipe['type'] === 'SouthIndian') echo 'selected'; ?>>🍛 Món Nam Ấn (South Indian)</option>
              <option value="Chinese" <?php if ($recipe['type'] === 'Chinese') echo 'selected'; ?>>🥢 Món Hoa (Chinese)</option>
              <option value="Dessert" <?php if ($recipe['type'] === 'Dessert') echo 'selected'; ?>>🍰 Món tráng miệng (Dessert)</option>
              <option value="Drinks" <?php if ($recipe['type'] === 'Drinks') echo 'selected'; ?>>🍹 Thức uống (Drinks)</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Thời gian nấu (phút) <span style="color: #ef4444;">*</span></label>
            <input type="number" name="cooking_time" class="form-control" value="<?php echo htmlspecialchars($recipe['cooking_time']); ?>" min="1" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Tóm tắt các bước</label>
          <input type="text" name="instructions" class="form-control" value="<?php echo htmlspecialchars($recipe['instructions'] ?? ''); ?>">
        </div>

        <div class="form-group">
          <label class="form-label">Video hướng dẫn YouTube</label>
          <input type="url" name="video_link" class="form-control" value="<?php echo htmlspecialchars($recipe['video_link'] ?? ''); ?>" placeholder="https://www.youtube.com/watch?v=...">
        </div>
      </div>

      <!-- CARD 3: NGUYÊN LIỆU -->
      <div class="editor-card">
        <h2 class="section-title">
          <i class="fa-solid fa-carrot text-primary"></i> Nguyên liệu chuẩn bị
        </h2>

        <div id="ingredientsList">
          <?php if (!empty($ingredients)): ?>
            <?php foreach ($ingredients as $ing): ?>
              <div class="ingredient-row">
                <input type="text" name="ingredients[]" class="form-control" value="<?php echo htmlspecialchars($ing['ingredient_name']); ?>" placeholder="Tên nguyên liệu" required>
                <input type="number" step="any" name="quantities[]" class="form-control" value="<?php echo htmlspecialchars($ing['quantity']); ?>" placeholder="Số lượng" required>
                <input type="text" name="units[]" class="form-control" value="<?php echo htmlspecialchars($ing['unit']); ?>" placeholder="Đơn vị" required>
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
          <i class="fa-solid fa-plus"></i> Thêm dòng nguyên liệu
        </button>
      </div>

      <!-- CARD 4: CÁC BƯỚC NẤU -->
      <div class="editor-card">
        <h2 class="section-title">
          <i class="fa-solid fa-fire-burner text-primary"></i> Các bước thực hiện chi tiết
        </h2>

        <div id="stepsList">
          <?php $sNum = 1; foreach ($existingSteps as $st): ?>
            <div class="step-row">
              <div class="step-number-badge"><?php echo $sNum; ?></div>
              <div style="flex: 1;">
                <textarea name="steps[]" class="form-control" rows="2" placeholder="Mô tả chi tiết bước <?php echo $sNum; ?>..." required><?php echo htmlspecialchars($st); ?></textarea>
              </div>
              <button type="button" class="btn-delete-row" onclick="removeStepRow(this)" title="Xóa bước này">
                <i class="fa-solid fa-trash-can"></i>
              </button>
            </div>
          <?php $sNum++; endforeach; ?>
        </div>

        <button type="button" class="btn-add-ingredient" onclick="addStepRow()" style="margin-top: 10px;">
          <i class="fa-solid fa-plus"></i> Thêm bước thực hiện
        </button>
      </div>

      <!-- BOTTOM SUBMIT BAR -->
      <div class="bottom-submit-bar">
        <a href="viewRecipeDetails.php?recipe_id=<?php echo $recipeId; ?>" class="btn-secondary">Hủy bỏ</a>
        <button type="submit" class="btn-primary">
          <i class="fa-solid fa-check"></i> Lưu thay đổi
        </button>
      </div>

    </form>
  </div>

  <script>
    let currentImageType = 'upload';

    function switchImageType(type) {
      currentImageType = type;
      if (type === 'upload') {
        document.getElementById('btnUploadType').classList.add('active');
        document.getElementById('btnUrlType').classList.remove('active');
        document.getElementById('uploadBox').style.display = 'block';
        document.getElementById('urlBox').style.display = 'none';
      } else {
        document.getElementById('btnUrlType').classList.add('active');
        document.getElementById('btnUploadType').classList.remove('active');
        document.getElementById('uploadBox').style.display = 'none';
        document.getElementById('urlBox').style.display = 'block';
      }
    }

    function previewImageFile(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
          document.getElementById('photoPreview').src = e.target.result;
          document.getElementById('photoPreview').style.display = 'block';
          document.getElementById('uploadPlaceholder').style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
      }
    }

    function previewImageUrl(url) {
      var img = document.getElementById('urlPhotoPreview');
      if (url.trim().length > 5) {
        img.src = url;
        img.style.display = 'block';
        img.onerror = function() {
          img.style.display = 'none';
        };
      } else {
        img.style.display = 'none';
      }
    }

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

    function addStepRow() {
      var list = document.getElementById("stepsList");
      var stepCount = list.getElementsByClassName("step-row").length + 1;
      var row = document.createElement("div");
      row.className = "step-row";
      row.innerHTML = `
        <div class="step-number-badge">${stepCount}</div>
        <div style="flex: 1;">
          <textarea name="steps[]" class="form-control" rows="2" placeholder="Mô tả chi tiết bước ${stepCount}..." required></textarea>
        </div>
        <button type="button" class="btn-delete-row" onclick="removeStepRow(this)" title="Xóa bước này">
          <i class="fa-solid fa-trash-can"></i>
        </button>
      `;
      list.appendChild(row);
      updateStepNumbers();
    }

    function removeStepRow(button) {
      var list = document.getElementById("stepsList");
      if (list.getElementsByClassName("step-row").length > 1) {
        button.closest(".step-row").remove();
        updateStepNumbers();
      } else {
        alert("Công thức cần ít nhất 1 bước thực hiện!");
      }
    }

    function updateStepNumbers() {
      var rows = document.querySelectorAll("#stepsList .step-row");
      rows.forEach(function(row, index) {
        row.querySelector(".step-number-badge").innerText = index + 1;
      });
    }

    function submitForm() {
      var form = document.getElementById('editForm');
      if (form.checkValidity()) {
        form.submit();
      } else {
        form.reportValidity();
      }
    }
  </script>
</body>
</html>
