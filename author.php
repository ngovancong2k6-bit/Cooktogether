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

if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    header("Location: index3.php");
    exit();
}

$authorId = intval($_GET['user_id']);

// Lấy thông tin đầu bếp kèm thống kê tổng số món, rating trung bình, tổng bình luận
$authorSql = "SELECT u.id, u.name, u.email,
                     COUNT(DISTINCT r.id) AS total_recipes,
                     AVG(rt.rating) AS avg_rating,
                     COUNT(DISTINCT c.id) AS total_comments
              FROM users u
              LEFT JOIN recipes r ON u.id = r.user_id
              LEFT JOIN ratings rt ON r.id = rt.recipe_id
              LEFT JOIN comments c ON r.id = c.recipe_id
              WHERE u.id = $authorId
              GROUP BY u.id";

$authorRes = $conn->query($authorSql);

if (!$authorRes || $authorRes->num_rows === 0) {
    echo "<div style='font-family: sans-serif; text-align: center; padding: 60px;'><h2>Không tìm thấy thông tin đầu bếp này!</h2><p><a href='index3.php'>Quay về trang chủ</a></p></div>";
    exit();
}

$author = $authorRes->fetch_assoc();
$authorName = htmlspecialchars($author['name']);
$initial = strtoupper(substr($authorName, 0, 1));
$avgRating = $author['avg_rating'] ? number_format((float)$author['avg_rating'], 1) : "5.0";
$totalRecipes = intval($author['total_recipes']);
$totalComments = intval($author['total_comments']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bếp của <?php echo $authorName; ?> - Cook Together</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
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
      --shadow-card: 0 4px 16px rgba(0, 0, 0, 0.06);
      --shadow-hover: 0 16px 32px -4px rgba(226, 114, 39, 0.16);
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
      display: block !important; /* Đảm bảo không bị layout flex ngang */
      width: 100%;
    }

    /* TOP NAVBAR */
    .author-navbar {
      background: var(--card-bg);
      border-bottom: 1px solid var(--border-color);
      padding: 16px 36px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      width: 100%;
      z-index: 100;
      box-shadow: 0 2px 10px rgba(0,0,0,0.03);
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
      gap: 12px;
    }

    .btn-nav-pill {
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

    .btn-nav-pill:hover {
      background: #f9fafb;
      border-color: #d1d5db;
    }

    .btn-nav-pill.primary {
      background: var(--primary);
      color: white;
      border: none;
    }
    .btn-nav-pill.primary:hover {
      background: var(--primary-hover);
    }

    /* MAIN CONTAINER */
    .container {
      max-width: 1200px;
      margin: 32px auto;
      padding: 0 24px 80px;
      width: 100%;
    }

    /* PROFILE HERO CARD */
    .profile-hero-card {
      background: #ffffff;
      border: 1px solid var(--border-color);
      border-radius: var(--radius-lg);
      padding: 36px 44px;
      box-shadow: var(--shadow-sm);
      margin-bottom: 32px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 24px;
      position: relative;
      overflow: hidden;
    }

    .profile-hero-card::after {
      content: '';
      position: absolute;
      top: -80px;
      right: -80px;
      width: 240px;
      height: 240px;
      background: radial-gradient(circle, rgba(226, 114, 39, 0.12) 0%, rgba(255,255,255,0) 70%);
      border-radius: 50%;
      pointer-events: none;
    }

    .profile-main-info {
      display: flex;
      align-items: center;
      gap: 24px;
    }

    .author-avatar-xl {
      width: 90px;
      height: 90px;
      border-radius: 50%;
      background: linear-gradient(135deg, #f97316, #e27227);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 38px;
      font-weight: 800;
      box-shadow: 0 10px 25px rgba(226, 114, 39, 0.35);
      flex-shrink: 0;
      border: 4px solid #ffffff;
    }

    .author-title-group h1 {
      font-size: 28px;
      font-weight: 800;
      color: var(--text-main);
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 6px;
    }

    .badge-verified {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      background: #eff6ff;
      color: #2563eb;
      font-size: 13px;
      font-weight: 700;
      padding: 4px 12px;
      border-radius: var(--radius-pill);
      border: 1px solid #bfdbfe;
    }

    .author-subtitle {
      color: var(--text-muted);
      font-size: 14.5px;
    }

    .stats-boxes-group {
      display: flex;
      gap: 14px;
      flex-wrap: wrap;
    }

    .stat-card {
      background: var(--bg-page);
      border: 1.5px solid var(--border-color);
      padding: 14px 24px;
      border-radius: var(--radius-md);
      text-align: center;
      min-width: 110px;
      transition: all 0.2s ease;
    }

    .stat-card:hover {
      border-color: var(--primary);
      transform: translateY(-2px);
    }

    .stat-value {
      font-size: 22px;
      font-weight: 800;
      color: var(--primary);
    }

    .stat-label {
      font-size: 12.5px;
      font-weight: 600;
      color: var(--text-muted);
      margin-top: 4px;
    }

    /* SECTION HEADER & SEARCH */
    .filter-section {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 16px;
      margin-bottom: 24px;
    }

    .section-headline {
      font-size: 22px;
      font-weight: 800;
      display: flex;
      align-items: center;
      gap: 10px;
      color: var(--text-main);
    }

    .author-search-box {
      position: relative;
      width: 340px;
      max-width: 100%;
    }

    .author-search-box input {
      width: 100%;
      padding: 11px 18px 11px 42px;
      border-radius: var(--radius-pill);
      border: 1.5px solid var(--border-color);
      background: white;
      font-size: 14px;
      outline: none;
      transition: all 0.2s;
    }

    .author-search-box input:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(226, 114, 39, 0.15);
    }

    .author-search-box i {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
    }

    /* RECIPES GRID & CARDS */
    .recipes-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 24px;
    }

    .recipe-card {
      background: var(--card-bg);
      border-radius: var(--radius-lg);
      border: 1px solid var(--border-color);
      overflow: hidden;
      cursor: pointer;
      display: flex;
      flex-direction: column;
      box-shadow: var(--shadow-card);
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      position: relative;
    }

    .recipe-card:hover {
      transform: translateY(-6px);
      box-shadow: var(--shadow-hover);
      border-color: #fed7aa;
    }

    .recipe-image-wrap {
      position: relative;
      width: 100%;
      height: 220px;
      overflow: hidden;
      background: #f3f4f6;
    }

    .recipe-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }

    .recipe-card:hover .recipe-img {
      transform: scale(1.06);
    }

    .badge-time {
      position: absolute;
      bottom: 12px;
      left: 12px;
      background: rgba(0, 0, 0, 0.7);
      backdrop-filter: blur(4px);
      color: white;
      font-size: 12px;
      font-weight: 700;
      padding: 4px 10px;
      border-radius: var(--radius-pill);
      display: flex;
      align-items: center;
      gap: 4px;
    }

    .badge-category {
      position: absolute;
      top: 12px;
      right: 12px;
      background: #ffffff;
      color: var(--primary);
      font-size: 12px;
      font-weight: 800;
      padding: 4px 12px;
      border-radius: var(--radius-pill);
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .recipe-body {
      padding: 20px;
      display: flex;
      flex-direction: column;
      flex: 1;
    }

    .recipe-title {
      font-size: 17px;
      font-weight: 800;
      line-height: 1.4;
      color: var(--text-main);
      margin-bottom: 8px;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .recipe-desc {
      font-size: 13.5px;
      color: var(--text-muted);
      line-height: 1.5;
      margin-bottom: 16px;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
      flex: 1;
    }

    .recipe-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding-top: 14px;
      border-top: 1px solid var(--border-subtle);
    }

    .rating-badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      background: #fef3c7;
      color: #d97706;
      font-size: 13px;
      font-weight: 800;
      padding: 4px 10px;
      border-radius: var(--radius-pill);
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
      .author-navbar {
        padding: 14px 20px;
      }
      .profile-hero-card {
        padding: 24px 20px;
        flex-direction: column;
        align-items: flex-start;
      }
      .stats-boxes-group {
        width: 100%;
        justify-content: space-between;
      }
      .stat-card {
        flex: 1;
        padding: 10px 14px;
        min-width: 80px;
      }
      .filter-section {
        flex-direction: column;
        align-items: flex-start;
      }
      .author-search-box {
        width: 100%;
      }
    }
  </style>
</head>
<body>

  <!-- TOP NAVBAR -->
  <nav class="author-navbar">
    <a href="index3.php" class="brand-link">
      <i class="fa-solid fa-utensils"></i> Cook Together
    </a>
    <div class="nav-actions">
      <a href="index3.php" class="btn-nav-pill">
        <i class="fa-solid fa-house"></i> Trang chủ
      </a>
      <a href="viewUploadedRecipes.php" class="btn-nav-pill">
        <i class="fa-solid fa-book-bookmark"></i> Kho món của tôi
      </a>
      <a href="index4.php" class="btn-nav-pill primary">
        <i class="fa-solid fa-plus"></i> Viết món mới
      </a>
    </div>
  </nav>

  <div class="container">
    
    <!-- CHEF PROFILE HEADER -->
    <div class="profile-hero-card">
      <div class="profile-main-info">
        <div class="author-avatar-xl">
          <?php echo $initial; ?>
        </div>
        <div class="author-title-group">
          <h1>
            <span><?php echo $authorName; ?></span>
            <span class="badge-verified"><i class="fa-solid fa-circle-check"></i> Đầu bếp</span>
          </h1>
          <p class="author-subtitle">Thành viên cộng đồng yêu bếp Cook Together</p>
        </div>
      </div>

      <div class="stats-boxes-group">
        <div class="stat-card">
          <div class="stat-value"><?php echo $totalRecipes; ?></div>
          <div class="stat-label"><i class="fa-solid fa-utensils"></i> Món ăn</div>
        </div>
        <div class="stat-card">
          <div class="stat-value"><i class="fa-solid fa-star" style="color: #f59e0b; font-size: 18px;"></i> <?php echo $avgRating; ?></div>
          <div class="stat-label">Đánh giá sao</div>
        </div>
        <div class="stat-card">
          <div class="stat-value"><?php echo $totalComments; ?></div>
          <div class="stat-label"><i class="fa-solid fa-comments"></i> Bình luận</div>
        </div>
      </div>
    </div>

    <!-- FILTER & SEARCH SECTION -->
    <div class="filter-section">
      <h2 class="section-headline">
        <i class="fa-solid fa-fire" style="color: var(--primary);"></i>
        <span>Tất cả công thức của <?php echo $authorName; ?></span>
      </h2>

      <div class="author-search-box">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="authorSearchInput" placeholder="Tìm món của đầu bếp này..." oninput="handleSearch(this.value)">
      </div>
    </div>

    <!-- RECIPES GRID -->
    <div class="recipes-grid" id="recipesGrid">
      <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
        <i class="fa-solid fa-spinner fa-spin" style="font-size: 32px; color: var(--primary);"></i>
        <p style="margin-top: 10px; color: var(--text-muted);">Đang tải các món ăn...</p>
      </div>
    </div>

  </div>

  <script>
    const authorId = <?php echo $authorId; ?>;
    let searchQuery = '';
    let searchDebounceTimer = null;

    window.onload = function() {
      fetchAuthorRecipes();
    };

    function fetchAuthorRecipes() {
      var url = "getRecipes.php?user_id=" + authorId;
      if (searchQuery.trim() !== "") {
        url += "&q=" + encodeURIComponent(searchQuery.trim());
      }

      var xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
          try {
            var recipes = JSON.parse(xhr.responseText);
            renderRecipes(recipes);
          } catch(e) {
            console.error(e);
          }
        }
      };
      xhr.open("GET", url, true);
      xhr.send();
    }

    function renderRecipes(recipes) {
      var grid = document.getElementById("recipesGrid");
      grid.innerHTML = "";

      if (!recipes || recipes.length === 0) {
        grid.innerHTML = `
          <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px; background: white; border-radius: 16px; border: 1px dashed #d1d5db;">
            <i class="fa-solid fa-utensils" style="font-size: 40px; color: #d1d5db; margin-bottom: 12px;"></i>
            <h3 style="font-size: 18px; font-weight: 700; color: #374151;">Không tìm thấy món ăn nào!</h3>
            <p style="color: #6b7280; font-size: 14px; margin-top: 4px;">Đầu bếp này chưa đăng món phù hợp với từ khóa tìm kiếm.</p>
          </div>
        `;
        return;
      }

      recipes.forEach(function (recipe) {
        var card = document.createElement("div");
        card.className = "recipe-card";
        card.onclick = function () {
          window.location.href = "viewRecipeDetails.php?recipe_id=" + recipe.id;
        };

        var photoUrl = recipe.photo ? recipe.photo : 'upload.jpeg';
        var ratingNum = parseFloat(recipe.avg_rating);
        var ratingDisplay = !isNaN(ratingNum) && ratingNum > 0 ? ratingNum.toFixed(1) : "5.0";
        var cookingTime = recipe.cooking_time ? recipe.cooking_time + " phút" : "30 phút";
        var desc = recipe.description || "Công thức món ngon chuẩn vị từ đầu bếp!";

        card.innerHTML = `
          <div class="recipe-image-wrap">
            <img src="${photoUrl}" alt="${recipe.title}" class="recipe-img" onerror="this.src='landing_page.jpg'">
            <span class="badge-time"><i class="fa-regular fa-clock"></i> ${cookingTime}</span>
            <span class="badge-category">${recipe.type || 'Món ngon'}</span>
          </div>
          <div class="recipe-body">
            <h3 class="recipe-title">${recipe.title}</h3>
            <p class="recipe-desc">${desc}</p>
            <div class="recipe-footer">
              <span style="font-size: 13px; color: var(--text-muted); font-weight: 600;">
                <i class="fa-solid fa-fire" style="color: var(--primary);"></i> ${recipe.type}
              </span>
              <div class="rating-badge">
                <i class="fa-solid fa-star"></i>
                <span>${ratingDisplay}</span>
              </div>
            </div>
          </div>
        `;

        grid.appendChild(card);
      });
    }

    function handleSearch(val) {
      clearTimeout(searchDebounceTimer);
      searchDebounceTimer = setTimeout(function () {
        searchQuery = val;
        fetchAuthorRecipes();
      }, 300);
    }
  </script>
</body>
</html>
