<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
$userId = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cook Together - Viết món mới</title>
  <link rel="stylesheet" href="index4.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>

  <!-- EDITOR NAVBAR -->
  <nav class="editor-navbar">
    <a href="index3.php" class="brand-link">
      <i class="fa-solid fa-utensils"></i> Cook Together
    </a>
    <div class="nav-actions">
      <a href="index3.php" class="btn-secondary">
        <i class="fa-solid fa-xmark"></i> Hủy bỏ
      </a>
      <button type="button" class="btn-primary" onclick="document.getElementById('recipeForm').submit()">
        <i class="fa-solid fa-cloud-arrow-up"></i> Đăng công thức
      </button>
    </div>
  </nav>

  <div class="editor-container">
    <form id="recipeForm" action="upload.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userId); ?>">

      <!-- CARD 1: THÔNG TIN CƠ BẢN & ẢNH -->
      <div class="editor-card">
        <h2 class="section-title">
          <i class="fa-solid fa-camera text-primary"></i> Hình ảnh & Tên món ăn
        </h2>

        <!-- PHOTO UPLOAD ZONE -->
        <div class="photo-upload-zone" onclick="document.getElementById('imageInput').click()">
          <input type="file" id="imageInput" name="image" accept="image/png,image/jpeg,image/webp" style="display: none;" onchange="previewImage(this)">
          <div id="uploadPlaceholder">
            <i class="fa-solid fa-cloud-arrow-up upload-icon"></i>
            <div class="upload-text">Nhấp để tải lên ảnh món ăn thành phẩm</div>
            <div class="upload-hint">Hỗ trợ định dạng JPG, PNG, WEBP (Tối đa 5MB)</div>
          </div>
          <img id="photoPreview" class="photo-preview" alt="Xem trước ảnh">
        </div>

        <div class="form-group">
          <label class="form-label">Tên món ăn <span style="color: #ef4444;">*</span></label>
          <input type="text" name="title" class="input-title" placeholder="ví dụ: Súp gà nấu nấm đông cô thơm ngọt..." required>
        </div>

        <div class="form-group">
          <label class="form-label">Mô tả ngắn về món ăn</label>
          <input type="text" name="description" class="form-control" placeholder="Chia sẻ cảm nghĩ hoặc câu chuyện thú vị về món ăn này...">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Danh mục món <span style="color: #ef4444;">*</span></label>
            <select name="type" class="form-control" required>
              <option value="" disabled selected>-- Chọn danh mục món --</option>
              <option value="Vietnamese">🇻🇳 Món Việt</option>
              <option value="NorthIndian">🥘 Món Bắc Ấn</option>
              <option value="SouthIndian">🍛 Món Nam Ấn</option>
              <option value="Chinese">🥢 Món Hoa</option>
              <option value="Dessert">🍰 Món tráng miệng</option>
              <option value="Drinks">🍹 Thức uống</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Thời gian nấu (phút) <span style="color: #ef4444;">*</span></label>
            <input type="number" name="cooking_time" class="form-control" placeholder="ví dụ: 45" min="1" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Video hướng dẫn (Tùy chọn)</label>
          <input type="url" name="video_link" class="form-control" placeholder="Dán link video YouTube (ví dụ: https://www.youtube.com/watch?v=...)">
        </div>
      </div>

      <!-- CARD 2: NGUYÊN LIỆU -->
      <div class="editor-card">
        <h2 class="section-title">
          <i class="fa-solid fa-carrot text-primary"></i> Nguyên liệu chuẩn bị
        </h2>

        <div id="ingredientsList">
          <div class="ingredient-row">
            <input type="text" name="ingredients[]" class="form-control" placeholder="Tên nguyên liệu (vd: Thịt ức gà)" required>
            <input type="number" step="any" name="quantities[]" class="form-control" placeholder="Số lượng (vd: 500)" required>
            <input type="text" name="units[]" class="form-control" placeholder="Đơn vị (vd: gram)" required>
            <button type="button" class="btn-delete-row" onclick="removeIngredientRow(this)" title="Xóa dòng">
              <i class="fa-solid fa-trash-can"></i>
            </button>
          </div>

          <div class="ingredient-row">
            <input type="text" name="ingredients[]" class="form-control" placeholder="Tên nguyên liệu (vd: Cà chua xay)" required>
            <input type="number" step="any" name="quantities[]" class="form-control" placeholder="Số lượng (vd: 2)" required>
            <input type="text" name="units[]" class="form-control" placeholder="Đơn vị (vd: quả)" required>
            <button type="button" class="btn-delete-row" onclick="removeIngredientRow(this)" title="Xóa dòng">
              <i class="fa-solid fa-trash-can"></i>
            </button>
          </div>
        </div>

        <button type="button" class="btn-add-ingredient" onclick="addIngredientRow()">
          <i class="fa-solid fa-plus"></i> Thêm nguyên liệu
        </button>
      </div>

      <!-- CARD 3: CÁCH LÀM (INSTRUCTIONS) -->
      <div class="editor-card">
        <h2 class="section-title">
          <i class="fa-solid fa-fire-burner text-primary"></i> Các bước thực hiện
        </h2>

        <div class="form-group">
          <label class="form-label">Hướng dẫn từng bước chi tiết <span style="color: #ef4444;">*</span></label>
          <textarea name="recipe" class="form-control" placeholder="Bước 1: Sơ chế và ướp nguyên liệu...&#10;Bước 2: Xào thơm gia vị và đun sôi...&#10;Bước 3: Cho ra đĩa và thưởng thức khi còn nóng." required></textarea>
        </div>
      </div>

      <!-- BOTTOM SUBMIT BAR -->
      <div class="bottom-submit-bar">
        <a href="index3.php" class="btn-secondary">Hủy bỏ</a>
        <button type="submit" class="btn-primary">
          <i class="fa-solid fa-cloud-arrow-up"></i> Đăng công thức ngay
        </button>
      </div>

    </form>
  </div>

  <script>
    function previewImage(input) {
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

    function addIngredientRow() {
      var list = document.getElementById("ingredientsList");
      var row = document.createElement("div");
      row.className = "ingredient-row";
      row.innerHTML = `
        <input type="text" name="ingredients[]" class="form-control" placeholder="Tên nguyên liệu" required>
        <input type="number" step="any" name="quantities[]" class="form-control" placeholder="Số lượng" required>
        <input type="text" name="units[]" class="form-control" placeholder="Đơn vị (vd: gram, thìa)" required>
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
