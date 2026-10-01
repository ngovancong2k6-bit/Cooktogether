<?php
session_start();

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
    header("Location: index3.php");
    exit();
}

$recipeId = intval($_GET['recipe_id']);
$currentUserId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// Fetch recipe details
$sql = "SELECT r.*, u.name AS uploader_name, c.category_name,
               AVG(rt.rating) AS avg_rating,
               COUNT(DISTINCT rt.id) AS total_ratings
        FROM recipes r
        JOIN users u ON r.user_id = u.id
        LEFT JOIN categories c ON r.category_id = c.id
        LEFT JOIN ratings rt ON r.id = rt.recipe_id
        WHERE r.id = $recipeId
        GROUP BY r.id";

$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo "<h1>Không tìm thấy công thức nấu ăn này!</h1><p><a href='index3.php'>Quay về trang chủ</a></p>";
    exit();
}

$recipe = $result->fetch_assoc();
$avgRating = $recipe['avg_rating'] ? number_format((float)$recipe['avg_rating'], 1) : "5.0";
$photoUrl = !empty($recipe['photo']) ? $recipe['photo'] : 'upload.jpeg';
$isOwner = ($currentUserId > 0 && $currentUserId === intval($recipe['user_id']));

// Helper function để lấy YouTube Embed URL
function getYouTubeEmbedUrl($url) {
    if (empty($url)) return '';
    $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i';
    if (preg_match($pattern, $url, $matches)) {
        return 'https://www.youtube.com/embed/' . $matches[1];
    }
    return '';
}
$youtubeEmbed = getYouTubeEmbedUrl($recipe['video_link'] ?? '');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($recipe['title']); ?> - Cook Together</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="index52.css?v=<?php echo time(); ?>">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
      --primary: #e27227;
      --primary-hover: #c95e18;
      --primary-light: #fff7ed;
      --primary-ultra-light: #fffaf5;
      --text-main: #1f2937;
      --text-muted: #6b7280;
      --bg-page: #fdfbf8;
      --card-bg: #ffffff;
      --border-color: #f0ebe1;
      --border-subtle: #f5f0e8;
      --star-color: #f59e0b;
      --radius-lg: 24px;
      --radius-md: 14px;
      --radius-pill: 9999px;
      --shadow-sm: 0 4px 20px rgba(0, 0, 0, 0.04);
      --shadow-md: 0 10px 30px -5px rgba(226, 114, 39, 0.1);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    body {
      background-color: var(--bg-page);
      color: var(--text-main);
      min-height: 100vh;
    }

    .recipe-navbar {
      background: var(--card-bg);
      border-bottom: 1px solid var(--border-color);
      padding: 16px 36px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 50;
    }

    .brand-link {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 22px;
      font-weight: 800;
      color: var(--primary);
      text-decoration: none;
    }

    .nav-actions {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .btn-nav {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 9px 18px;
      border-radius: var(--radius-pill);
      border: 1.5px solid var(--border-color);
      background: white;
      color: var(--text-main);
      font-weight: 600;
      font-size: 14px;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .btn-nav:hover {
      background: #f9fafb;
      border-color: #d1d5db;
    }

    .btn-nav-primary {
      background: var(--primary);
      color: white;
      border: none;
    }
    .btn-nav-primary:hover {
      background: var(--primary-hover);
    }

    .btn-nav-danger {
      background: #fee2e2;
      color: #dc2626;
      border: 1px solid #fca5a5;
    }
    .btn-nav-danger:hover {
      background: #fecaca;
    }

    .detail-container {
      max-width: 880px;
      margin: 32px auto;
      padding: 0 20px 80px;
    }

    .recipe-header-card {
      background: var(--card-bg);
      border-radius: var(--radius-lg);
      border: 1px solid var(--border-color);
      padding: 36px;
      box-shadow: var(--shadow-sm);
      margin-bottom: 24px;
    }

    .recipe-meta-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-bottom: 16px;
    }

    .meta-tag {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 14px;
      border-radius: var(--radius-pill);
      font-size: 13px;
      font-weight: 700;
    }

    .tag-category {
      background: var(--primary-light);
      color: var(--primary);
    }

    .tag-time {
      background: #f3f4f6;
      color: #4b5563;
    }

    .tag-rating {
      background: #fef3c7;
      color: #d97706;
    }

    .recipe-main-title {
      font-size: 32px;
      font-weight: 800;
      line-height: 1.3;
      color: var(--text-main);
      margin-bottom: 18px;
    }

    .uploader-info-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      margin-bottom: 24px;
      padding-bottom: 20px;
      border-bottom: 1px solid var(--border-subtle);
    }

    .uploader-left {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .uploader-avatar-large {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      background: linear-gradient(135deg, #f97316, #e27227);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      font-weight: 700;
    }

    .uploader-details .author-name {
      font-size: 15px;
      font-weight: 700;
      color: var(--text-main);
    }

    .uploader-details .upload-date {
      font-size: 13px;
      color: var(--text-muted);
    }

    .owner-actions {
      display: flex;
      gap: 8px;
    }

    .featured-photo-wrap {
      width: 100%;
      max-height: 440px;
      border-radius: var(--radius-lg);
      overflow: hidden;
      margin-bottom: 24px;
      background: #f3f4f6;
      box-shadow: var(--shadow-sm);
    }

    .featured-photo {
      width: 100%;
      height: 100%;
      max-height: 440px;
      object-fit: cover;
      display: block;
    }

    .recipe-intro-box {
      background: var(--primary-ultra-light);
      border-left: 4px solid var(--primary);
      padding: 16px 20px;
      border-radius: 0 var(--radius-md) var(--radius-md) 0;
      font-size: 15px;
      color: #7c2d12;
      line-height: 1.6;
      margin-bottom: 24px;
    }

    .content-card {
      background: var(--card-bg);
      border-radius: var(--radius-lg);
      border: 1px solid var(--border-color);
      padding: 32px;
      box-shadow: var(--shadow-sm);
      margin-bottom: 24px;
    }

    .card-title {
      font-size: 20px;
      font-weight: 800;
      color: var(--text-main);
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .ingredients-checklist {
      list-style: none;
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 12px;
    }

    .ingredient-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 16px;
      background: #faf8f5;
      border-radius: var(--radius-md);
      border: 1px solid var(--border-subtle);
      cursor: pointer;
      user-select: none;
      transition: all 0.2s ease;
    }

    .ingredient-item:hover {
      background: var(--primary-light);
      border-color: #fed7aa;
    }

    .ingredient-item input[type="checkbox"] {
      width: 18px;
      height: 18px;
      accent-color: var(--primary);
      cursor: pointer;
    }

    .ingredient-item.checked span {
      text-decoration: line-through;
      color: var(--text-muted);
    }

    .step-item-card {
      display: flex;
      gap: 18px;
      margin-bottom: 18px;
      padding: 18px;
      background: #faf8f5;
      border-radius: var(--radius-md);
      border: 1px solid var(--border-subtle);
    }

    .step-badge {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: var(--primary);
      color: white;
      font-weight: 800;
      font-size: 15px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .step-content {
      font-size: 15px;
      line-height: 1.7;
      color: #374151;
      padding-top: 4px;
    }

    .video-frame-wrap {
      position: relative;
      padding-bottom: 56.25%; /* 16:9 */
      height: 0;
      overflow: hidden;
      border-radius: var(--radius-md);
      box-shadow: var(--shadow-sm);
      margin-top: 14px;
    }
    .video-frame-wrap iframe {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      border: 0;
    }

    .interactive-rating-box {
      background: #fefce8;
      border: 1px solid #fef08a;
      padding: 18px 24px;
      border-radius: var(--radius-md);
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 16px;
      margin-bottom: 28px;
    }

    .rating-stars-select {
      padding: 8px 16px;
      border-radius: var(--radius-pill);
      border: 1.5px solid #fde047;
      background: white;
      font-weight: 700;
      outline: none;
    }

    .btn-rate {
      padding: 8px 20px;
      border-radius: var(--radius-pill);
      border: none;
      background: var(--star-color);
      color: white;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .btn-rate:hover {
      background: #d97706;
    }

    .comment-form {
      margin-bottom: 28px;
    }

    .comment-textarea {
      width: 100%;
      padding: 14px 16px;
      border-radius: var(--radius-md);
      border: 1.5px solid var(--border-color);
      background: #faf8f5;
      font-size: 14px;
      min-height: 90px;
      outline: none;
      margin-bottom: 12px;
      transition: all 0.2s ease;
    }

    .comment-textarea:focus {
      background: white;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(226, 114, 39, 0.12);
    }

    .comments-list {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .comment-item {
      display: flex;
      gap: 14px;
      padding: 16px;
      background: #faf8f5;
      border-radius: var(--radius-md);
      border: 1px solid var(--border-subtle);
    }

    .comment-avatar {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: #fed7aa;
      color: #c2410c;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      font-weight: 700;
      flex-shrink: 0;
    }

    .comment-content-wrap {
      flex: 1;
    }

    .comment-author {
      font-size: 14px;
      font-weight: 700;
      color: var(--text-main);
      margin-bottom: 2px;
    }

    .comment-time {
      font-size: 12px;
      color: var(--text-muted);
      margin-bottom: 6px;
    }

    .comment-text {
      font-size: 14px;
      color: #374151;
      line-height: 1.5;
    }

    @media (max-width: 640px) {
      .recipe-header-card, .content-card {
        padding: 24px 16px;
      }
      .recipe-main-title {
        font-size: 24px;
      }
      .recipe-navbar {
        padding: 14px 18px;
      }
      .detail-container {
        padding: 0 12px 60px;
      }
      .uploader-info-row {
        flex-direction: column;
        align-items: flex-start;
      }
    }
  </style>
</head>
<body>

  <!-- TOP NAVBAR -->
  <nav class="recipe-navbar">
    <a href="index3.php" class="brand-link">
      <i class="fa-solid fa-utensils"></i> Cook Together
    </a>
    <div class="nav-actions">
      <a href="index3.php" class="btn-nav">
        <i class="fa-solid fa-arrow-left"></i> Khám phá món khác
      </a>
      <a href="viewUploadedRecipes.php" class="btn-nav">
        <i class="fa-solid fa-book-bookmark"></i> Kho món của tôi
      </a>
      <a href="index4.php" class="btn-nav btn-nav-primary">
        <i class="fa-solid fa-plus"></i> Viết món mới
      </a>
    </div>
  </nav>

  <div class="detail-container">
    
    <!-- RECIPE HEADER CARD -->
    <div class="recipe-header-card">
      <div class="recipe-meta-tags">
        <span class="meta-tag tag-category">
          <i class="fa-solid fa-utensils"></i> <?php echo htmlspecialchars($recipe['category_name'] ?? $recipe['type']); ?>
        </span>
        <span class="meta-tag tag-time">
          <i class="fa-regular fa-clock"></i> <?php echo htmlspecialchars($recipe['cooking_time']); ?> phút
        </span>
        <span class="meta-tag tag-rating">
          <i class="fa-solid fa-star"></i> <?php echo $avgRating; ?> (<?php echo $recipe['total_ratings']; ?> đánh giá)
        </span>
      </div>

      <h1 class="recipe-main-title"><?php echo htmlspecialchars($recipe['title']); ?></h1>

      <div class="uploader-info-row">
        <div class="uploader-left">
          <div class="uploader-avatar-large">
            <?php echo strtoupper(substr($recipe['uploader_name'], 0, 1)); ?>
          </div>
          <div class="uploader-details">
            <div class="author-name"><?php echo htmlspecialchars($recipe['uploader_name']); ?></div>
            <div class="upload-date">Đăng vào ngày <?php echo date("d/m/Y", strtotime($recipe['uploaded_at'])); ?></div>
          </div>
        </div>

        <?php if ($isOwner): ?>
        <div class="owner-actions">
          <a href="edit.php?recipe_id=<?php echo $recipeId; ?>" class="btn-nav" style="background: #fff7ed; border-color: #fed7aa; color: #e27227;">
            <i class="fa-regular fa-pen-to-square"></i> Chỉnh sửa
          </a>
          <button onclick="confirmDelete(<?php echo $recipeId; ?>, '<?php echo addslashes($recipe['title']); ?>')" class="btn-nav btn-nav-danger">
            <i class="fa-regular fa-trash-can"></i> Xóa món
          </button>
        </div>
        <?php endif; ?>
      </div>

      <!-- FEATURED PHOTO -->
      <div class="featured-photo-wrap">
        <img src="<?php echo htmlspecialchars($photoUrl); ?>" alt="<?php echo htmlspecialchars($recipe['title']); ?>" class="featured-photo" onerror="this.src='landing_page.jpg'">
      </div>

      <?php if (!empty($recipe['description'])): ?>
      <div class="recipe-intro-box">
        <i class="fa-solid fa-quote-left" style="margin-right: 8px; opacity: 0.5;"></i>
        <?php echo nl2br(htmlspecialchars($recipe['description'])); ?>
      </div>
      <?php endif; ?>

      <?php if (!empty($recipe['instructions'])): ?>
      <div style="background: #f8fafc; border-radius: 12px; padding: 14px 18px; font-size: 14px; color: #475569; margin-top: 10px; border: 1px dashed #cbd5e1;">
        <strong><i class="fa-solid fa-list-check" style="color: var(--primary);"></i> Tóm tắt quy trình:</strong> 
        <?php echo htmlspecialchars($recipe['instructions']); ?>
      </div>
      <?php endif; ?>
    </div>

    <!-- INGREDIENTS CARD -->
    <div class="content-card">
      <h2 class="card-title">
        <i class="fa-solid fa-carrot" style="color: var(--primary);"></i> Nguyên liệu chuẩn bị
      </h2>
      
      <ul class="ingredients-checklist">
        <?php
        $ingredientSql = "SELECT * FROM ingredients WHERE recipe_id = $recipeId";
        $ingredientResult = $conn->query($ingredientSql);

        if ($ingredientResult && $ingredientResult->num_rows > 0) {
            while ($ing = $ingredientResult->fetch_assoc()) {
                $qty = (float)$ing['quantity'];
                $unit = htmlspecialchars($ing['unit']);
                $name = htmlspecialchars($ing['ingredient_name']);
                echo "
                <li class='ingredient-item' onclick='toggleIngredient(this)'>
                  <input type='checkbox' onclick='event.stopPropagation()'>
                  <span><strong>{$qty} {$unit}</strong> {$name}</span>
                </li>";
            }
        } else {
            echo "<p style='color: #9ca3af;'>Chưa có thông tin nguyên liệu chi tiết.</p>";
        }
        ?>
      </ul>
    </div>

    <!-- INSTRUCTIONS CARD -->
    <div class="content-card">
      <h2 class="card-title">
        <i class="fa-solid fa-fire-burner" style="color: var(--primary);"></i> Các bước thực hiện chi tiết
      </h2>
      
      <div class="instructions-steps-list">
        <?php
        $rawSteps = explode("\n", str_replace(["\r\n", "\r"], "\n", $recipe['recipe']));
        $stepIndex = 1;
        foreach ($rawSteps as $st) {
            $stTrim = trim($st);
            if (empty($stTrim)) continue;
            $cleanSt = preg_replace('/^(Bước\s*\d+[:.]?|\d+[.:])\s*/iu', '', $stTrim);
            echo "
            <div class='step-item-card'>
              <div class='step-badge'>{$stepIndex}</div>
              <div class='step-content'>".nl2br(htmlspecialchars($cleanSt))."</div>
            </div>";
            $stepIndex++;
        }
        ?>
      </div>

      <!-- VIDEO EMBED HOẶC LINK -->
      <?php if (!empty($youtubeEmbed)): ?>
      <div style="margin-top: 28px;">
        <h3 style="font-size: 16px; margin-bottom: 12px; font-weight: 700; color: var(--text-main);">
          <i class="fa-brands fa-youtube" style="color: #ef4444;"></i> Video hướng dẫn trực tiếp
        </h3>
        <div class="video-frame-wrap">
          <iframe src="<?php echo htmlspecialchars($youtubeEmbed); ?>" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
      </div>
      <?php elseif (!empty($recipe['video_link'])): ?>
      <div style="margin-top: 24px;">
        <a href="<?php echo htmlspecialchars($recipe['video_link']); ?>" target="_blank" class="btn-nav btn-nav-primary" style="padding: 12px 24px; font-size: 15px;">
          <i class="fa-solid fa-play"></i> Xem video thực hiện trên YouTube
        </a>
      </div>
      <?php endif; ?>
    </div>

    <!-- RATING & COMMENTS CARD -->
    <div class="content-card">
      <h2 class="card-title">
        <i class="fa-solid fa-comments" style="color: var(--primary);"></i> Đánh giá & Bình luận từ cộng đồng
      </h2>

      <!-- RATING BOX -->
      <div class="interactive-rating-box">
        <span style="font-weight: 700; font-size: 14px;">Bạn thấy món này thế nào?</span>
        <select id="ratingScore" class="rating-stars-select">
          <option value="5">⭐⭐⭐⭐⭐ Tuyệt vời (5 sao)</option>
          <option value="4">⭐⭐⭐⭐ Rất ngon (4 sao)</option>
          <option value="3">⭐⭐⭐ Ổn áp (3 sao)</option>
          <option value="2">⭐⭐ Tạm được (2 sao)</option>
          <option value="1">⭐ Cần cải thiện (1 sao)</option>
        </select>
        <button type="button" class="btn-rate" onclick="submitRating()">
          Gửi đánh giá
        </button>
      </div>

      <!-- COMMENT SUBMIT FORM -->
      <div class="comment-form">
        <textarea id="commentContent" class="comment-textarea" placeholder="Viết cảm nhận, mẹo nấu hoặc lời cảm ơn đến tác giả..."></textarea>
        <button type="button" class="btn-nav btn-nav-primary" style="font-weight: 700;" onclick="submitComment()">
          <i class="fa-solid fa-paper-plane"></i> Gửi bình luận
        </button>
      </div>

      <!-- COMMENTS LIST -->
      <div class="comments-list" id="commentsList">
        <?php
        $commentsSql = "SELECT c.*, u.name AS commenter_name
                        FROM comments c
                        JOIN users u ON c.user_id = u.id
                        WHERE c.recipe_id = $recipeId
                        ORDER BY c.created_at DESC";
        $commentsResult = $conn->query($commentsSql);

        if ($commentsResult && $commentsResult->num_rows > 0) {
            while ($comment = $commentsResult->fetch_assoc()) {
                $author = htmlspecialchars($comment['commenter_name']);
                $commentText = nl2br(htmlspecialchars($comment['comment']));
                $time = date("d/m/Y H:i", strtotime($comment['created_at']));
                $initial = strtoupper(substr($author, 0, 1));
                echo "
                <div class='comment-item'>
                  <div class='comment-avatar'>{$initial}</div>
                  <div class='comment-content-wrap'>
                    <div class='comment-author'>{$author}</div>
                    <div class='comment-time'>{$time}</div>
                    <div class='comment-text'>{$commentText}</div>
                  </div>
                </div>";
            }
        } else {
            echo "<p style='color: #9ca3af; text-align: center; padding: 20px;'>Chưa có bình luận nào. Hãy là người đầu tiên chia sẻ cảm nghĩ nhé!</p>";
        }
        ?>
      </div>
    </div>

  </div>

  <script>
    const recipeId = <?php echo $recipeId; ?>;

    function toggleIngredient(element) {
      const checkbox = element.querySelector('input[type="checkbox"]');
      checkbox.checked = !checkbox.checked;
      if (checkbox.checked) {
        element.classList.add('checked');
      } else {
        element.classList.remove('checked');
      }
    }

    function confirmDelete(id, title) {
      if (confirm('Bạn có chắc chắn muốn xóa công thức "' + title + '" không?\nHành động này không thể khôi phục!')) {
        window.location.href = 'delete.php?recipe_id=' + id;
      }
    }

    function submitRating() {
      const score = document.getElementById("ratingScore").value;
      const xhr = new XMLHttpRequest();
      xhr.open("POST", "addRating.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            alert("✓ Cảm ơn bạn đã đánh giá món ăn này!");
            window.location.reload();
          } else {
            alert("Không thể gửi đánh giá. Vui lòng thử lại!");
          }
        }
      };
      xhr.send("recipe_id=" + recipeId + "&rating=" + score);
    }

    function submitComment() {
      const content = document.getElementById("commentContent").value.trim();
      if (!content) {
        alert("Vui lòng nhập nội dung bình luận!");
        return;
      }

      const xhr = new XMLHttpRequest();
      xhr.open("POST", "addComment.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            alert("✓ Bình luận của bạn đã được đăng tải!");
            window.location.reload();
          } else {
            alert("Không thể gửi bình luận. Vui lòng thử lại!");
          }
        }
      };
      xhr.send("recipe_id=" + recipeId + "&comment=" + encodeURIComponent(content));
    }
  </script>
</body>
</html>
