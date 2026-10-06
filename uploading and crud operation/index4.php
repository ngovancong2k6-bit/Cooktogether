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
  <title>Đăng công thức mới - Cook Together</title>
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

  <!-- EDITOR NAVBAR -->
  <nav class="editor-navbar">
    <a href="index3.php" class="brand-link">
      <i class="fa-solid fa-utensils"></i> Cook Together
    </a>
    <div class="nav-actions">
      <a href="index3.php" class="btn-secondary">
        <i class="fa-solid fa-xmark"></i> Hủy bỏ
      </a>
      <button type="button" class="btn-primary" onclick="submitForm()">
        <i class="fa-solid fa-cloud-arrow-up"></i> Đăng công thức
      </button>
    </div>
  </nav>

  <div class="editor-container">
    <form id="recipeForm" action="upload.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userId); ?>">

      <!-- CARD 1: HÌNH ẢNH MÓN ĂN -->
      <div class="editor-card">
        <h2 class="section-title">
          <i class="fa-solid fa-camera text-primary"></i> Hình ảnh món ăn
        </h2>

        <div class="image-type-toggle">
          <button type="button" class="image-type-btn active" id="btnUploadType" onclick="switchImageType('upload')">
            <i class="fa-solid fa-arrow-up-from-bracket"></i> Tải ảnh từ máy
          </button>
          <button type="button" class="image-type-btn" id="btnUrlType" onclick="switchImageType('url')">
            <i class="fa-solid fa-link"></i> Dán link ảnh trực tiếp (URL)
          </button>
        </div>

        <!-- UPLOAD TỪ MÁY -->
        <div id="uploadBox" class="photo-upload-zone" onclick="document.getElementById('imageInput').click()">
          <input type="file" id="imageInput" name="image" accept="image/png,image/jpeg,image/webp,image/jpg" style="display: none;" onchange="previewImageFile(this)">
          <div id="uploadPlaceholder">
            <i class="fa-solid fa-cloud-arrow-up upload-icon"></i>
            <div class="upload-text">Nhấp để chọn ảnh món ăn từ thiết bị của bạn</div>
            <div class="upload-hint">Hỗ trợ định dạng JPG, PNG, WEBP (Tối đa 5MB)</div>
          </div>
          <img id="photoPreview" class="photo-preview" alt="Xem trước ảnh">
        </div>

        <!-- NHẬP URL ẢNH -->
        <div id="urlBox" style="display: none; margin-bottom: 16px;">
          <div class="form-group">
            <label class="form-label">Đường dẫn ảnh (URL Image)</label>
            <input type="url" id="imageUrlInput" name="photo_url" class="form-control" placeholder="https://images.unsplash.com/photo-..." oninput="previewImageUrl(this.value)">
          </div>
          <div style="text-align: center;">
            <img id="urlPhotoPreview" style="max-height: 250px; border-radius: 12px; display: none; margin: 0 auto; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" alt="Xem trước URL">
          </div>
        </div>
      </div>

      <!-- CARD 2: THÔNG TIN CƠ BẢN -->
      <div class="editor-card">
        <h2 class="section-title">
          <i class="fa-solid fa-pen-fancy text-primary"></i> Thông tin cơ bản
        </h2>

        <div class="form-group">
          <label class="form-label">Tên món ăn <span style="color: #ef4444;">*</span></label>
          <input type="text" name="title" class="input-title" placeholder="ví dụ: Phở Bò Tái Lăn Hà Nội..." required>
        </div>

        <div class="form-group">
          <label class="form-label">Mô tả ngắn về món ăn</label>
          <textarea name="description" class="form-control" rows="3" placeholder="Chia sẻ câu chuyện, hương vị đặc trưng hoặc cảm nghĩ về món ăn này..."></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Danh mục món <span style="color: #ef4444;">*</span></label>
            <select name="type" class="form-control" required>
              <option value="" disabled selected>-- Chọn danh mục món --</option>
              <option value="Vietnamese">🍲 Món Việt (Vietnamese)</option>
              <option value="NorthIndian">🥘 Món Bắc Ấn (North Indian)</option>
              <option value="SouthIndian">🍛 Món Nam Ấn (South Indian)</option>
              <option value="Chinese">🥢 Món Hoa (Chinese)</option>
              <option value="Dessert">🍰 Món tráng miệng (Dessert)</option>
              <option value="Drinks">🍹 Thức uống (Drinks)</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Thời gian nấu (phút) <span style="color: #ef4444;">*</span></label>
            <input type="number" name="cooking_time" class="form-control" placeholder="ví dụ: 45" min="1" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Tóm tắt các bước thực hiện</label>
          <input type="text" name="instructions" class="form-control" placeholder="ví dụ: Sơ chế -> Tẩm ướp -> Xào lăn -> Thưởng thức nóng">
        </div>

        <div class="form-group">
          <label class="form-label">Video hướng dẫn YouTube (Tùy chọn)</label>
          <input type="url" name="video_link" class="form-control" placeholder="Dán link video YouTube (ví dụ: https://www.youtube.com/watch?v=...)">
        </div>
      </div>

      <!-- CARD 3: NGUYÊN LIỆU -->
      <div class="editor-card">
        <h2 class="section-title">
          <i class="fa-solid fa-carrot text-primary"></i> Nguyên liệu chuẩn bị
        </h2>

        <div id="ingredientsList">
          <div class="ingredient-row">
            <input type="text" name="ingredients[]" class="form-control" placeholder="Tên nguyên liệu (vd: Thịt bò thăn)" required>
            <input type="number" step="any" name="quantities[]" class="form-control" placeholder="Số lượng (vd: 500)" required>
            <input type="text" name="units[]" class="form-control" placeholder="Đơn vị (vd: gram, thìa, quả)" required>
            <button type="button" class="btn-delete-row" onclick="removeIngredientRow(this)" title="Xóa dòng">
              <i class="fa-solid fa-trash-can"></i>
            </button>
          </div>

          <div class="ingredient-row">
            <input type="text" name="ingredients[]" class="form-control" placeholder="Tên nguyên liệu (vd: Bánh phở tươi)" required>
            <input type="number" step="any" name="quantities[]" class="form-control" placeholder="Số lượng (vd: 1)" required>
            <input type="text" name="units[]" class="form-control" placeholder="Đơn vị (vd: kg)" required>
            <button type="button" class="btn-delete-row" onclick="removeIngredientRow(this)" title="Xóa dòng">
              <i class="fa-solid fa-trash-can"></i>
            </button>
          </div>
        </div>

        <button type="button" class="btn-add-ingredient" onclick="addIngredientRow()">
          <i class="fa-solid fa-plus"></i> Thêm dòng nguyên liệu
        </button>
      </div>

      <!-- CARD 4: HƯỚNG DẪN CHI TIẾT TỪNG BƯỚC -->
      <div class="editor-card">
        <h2 class="section-title">
          <i class="fa-solid fa-fire-burner text-primary"></i> Các bước thực hiện chi tiết
        </h2>

        <div id="stepsList">
          <div class="step-row">
            <div class="step-number-badge">1</div>
            <div style="flex: 1;">
              <textarea name="steps[]" class="form-control" rows="2" placeholder="Mô tả chi tiết bước 1 (vd: Thịt bò thái mỏng, ướp với gừng, tỏi băm, hạt nêm và nước mắm trong 15 phút)..." required></textarea>
            </div>
            <button type="button" class="btn-delete-row" onclick="removeStepRow(this)" title="Xóa bước này">
              <i class="fa-solid fa-trash-can"></i>
            </button>
          </div>

          <div class="step-row">
            <div class="step-number-badge">2</div>
            <div style="flex: 1;">
              <textarea name="steps[]" class="form-control" rows="2" placeholder="Mô tả chi tiết bước 2 (vd: Đun nóng chảo dầu, phi thơm tỏi rồi xào thịt bò trên lửa lớn trong 2 phút rồi trút ra đĩa)..." required></textarea>
            </div>
            <button type="button" class="btn-delete-row" onclick="removeStepRow(this)" title="Xóa bước này">
              <i class="fa-solid fa-trash-can"></i>
            </button>
          </div>
        </div>

        <button type="button" class="btn-add-ingredient" onclick="addStepRow()" style="margin-top: 10px;">
          <i class="fa-solid fa-plus"></i> Thêm bước thực hiện
        </button>
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
      var form = document.getElementById('recipeForm');
      if (form.checkValidity()) {
        form.submit();
      } else {
        form.reportValidity();
      }
    }
  </script>
</body>
</html>
