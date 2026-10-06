<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cook Together - Khám phá món ngon mỗi ngày</title>
  <link rel="stylesheet" href="index3.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    /* FILTER & SORT CONTROLS STYLES */
    .filter-sort-wrapper {
      background: #ffffff;
      border: 1px solid var(--border-color);
      border-radius: var(--radius-lg);
      padding: 18px 22px;
      margin-bottom: 28px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .filter-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 12px;
    }

    .filter-group {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
    }

    .filter-label {
      font-size: 13px;
      font-weight: 700;
      color: #6b7280;
      display: flex;
      align-items: center;
      gap: 6px;
      margin-right: 4px;
    }

    .sort-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 7px 14px;
      border-radius: var(--radius-pill);
      background: #f9fafb;
      border: 1px solid #e5e7eb;
      font-size: 13px;
      font-weight: 600;
      color: #4b5563;
      cursor: pointer;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      user-select: none;
    }

    .sort-pill:hover {
      background: #fff7ed;
      border-color: #fdba74;
      color: var(--primary);
    }

    .sort-pill.active {
      background: linear-gradient(135deg, #f97316, #e27227);
      border-color: transparent;
      color: #ffffff;
      font-weight: 700;
      box-shadow: 0 3px 10px rgba(226, 114, 39, 0.3);
    }

    .sort-pill.active i {
      color: #ffffff;
    }

    /* RATING SLIDER STYLES */
    .slider-filter-box {
      display: flex;
      align-items: center;
      gap: 20px;
      flex-wrap: wrap;
      width: 100%;
      background: #fafaf9;
      border: 1px solid #f0ebe1;
      padding: 14px 18px;
      border-radius: var(--radius-md);
    }

    .slider-track-container {
      flex: 1;
      min-width: 220px;
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .custom-range-slider {
      -webkit-appearance: none;
      appearance: none;
      width: 100%;
      height: 8px;
      border-radius: 9999px;
      background: #e5e7eb;
      outline: none;
      transition: background 0.2s;
      cursor: pointer;
    }

    .custom-range-slider::-webkit-slider-thumb {
      -webkit-appearance: none;
      appearance: none;
      width: 22px;
      height: 22px;
      border-radius: 50%;
      background: #ffffff;
      border: 3px solid #e27227;
      box-shadow: 0 2px 8px rgba(226, 114, 39, 0.4);
      cursor: pointer;
      transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .custom-range-slider::-webkit-slider-thumb:hover {
      transform: scale(1.18);
      box-shadow: 0 4px 12px rgba(226, 114, 39, 0.55);
    }

    .custom-range-slider::-moz-range-thumb {
      width: 22px;
      height: 22px;
      border-radius: 50%;
      background: #ffffff;
      border: 3px solid #e27227;
      box-shadow: 0 2px 8px rgba(226, 114, 39, 0.4);
      cursor: pointer;
      transition: transform 0.15s ease;
    }

    .custom-range-slider::-moz-range-thumb:hover {
      transform: scale(1.18);
    }

    .slider-ticks {
      display: flex;
      justify-content: space-between;
      font-size: 11px;
      font-weight: 700;
      color: #9ca3af;
      padding: 0 4px;
      user-select: none;
    }

    .slider-ticks span {
      cursor: pointer;
      transition: color 0.15s;
    }

    .slider-ticks span:hover {
      color: var(--primary);
    }

    .slider-value-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: #fff7ed;
      border: 1.5px solid #fdba74;
      color: #c2410c;
      font-weight: 800;
      font-size: 13px;
      padding: 6px 14px;
      border-radius: var(--radius-pill);
      white-space: nowrap;
      min-width: 170px;
      justify-content: center;
      box-shadow: 0 2px 6px rgba(249, 115, 22, 0.1);
    }

    .btn-reset-filter {
      padding: 6px 12px;
      border-radius: var(--radius-pill);
      background: #ffffff;
      border: 1px solid #d1d5db;
      color: #4b5563;
      font-size: 12px;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      transition: all 0.2s;
    }

    .btn-reset-filter:hover {
      background: #fee2e2;
      border-color: #fca5a5;
      color: #dc2626;
    }

    .results-count-badge {
      font-size: 13px;
      color: #6b7280;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .results-count-badge b {
      color: var(--primary);
      font-weight: 800;
    }

    /* TOP RATED BADGE ON CARDS */
    .top-rated-tag {
      position: absolute;
      top: 12px;
      left: 12px;
      background: linear-gradient(135deg, #f59e0b, #d97706);
      color: #ffffff;
      font-size: 11px;
      font-weight: 800;
      padding: 4px 10px;
      border-radius: var(--radius-pill);
      display: inline-flex;
      align-items: center;
      gap: 5px;
      box-shadow: 0 4px 10px rgba(217, 119, 6, 0.4);
      z-index: 2;
      letter-spacing: 0.3px;
    }

    .rating-badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      background: #fffbeb;
      border: 1px solid #fde68a;
      color: #b45309;
      font-weight: 700;
      font-size: 12px;
      padding: 4px 8px;
      border-radius: var(--radius-pill);
    }

    .rating-badge i {
      color: #f59e0b;
      font-size: 12px;
    }

    .rating-count-sub {
      color: #9ca3af;
      font-size: 11px;
      font-weight: 500;
      margin-left: 2px;
    }
  </style>
</head>
<body>
  <div class="app-container">
    
    <!-- LEFT SIDEBAR (COOKPAD STYLE) -->
    <aside class="sidebar" id="appSidebar">
      <div class="sidebar-brand">
        <a href="index3.php" class="brand-logo">
          <i class="fa-solid fa-utensils"></i>
          <span>Cook Together</span>
        </a>
      </div>

      <div class="sidebar-nav">
        <button class="nav-item active" onclick="filterCategory('all', this)">
          <i class="fa-solid fa-house"></i>
          <span>Trang chủ</span>
        </button>

        <div class="nav-section-title">Danh mục món ngon</div>
        <button class="nav-item" onclick="filterCategory('Vietnamese', this)">
          <i class="fa-solid fa-bowl-food"></i>
          <span>Món Việt</span>
        </button>
        <button class="nav-item" onclick="filterCategory('NorthIndian', this)">
          <i class="fa-solid fa-bowl-rice"></i>
          <span>Món Bắc Ấn</span>
        </button>
        <button class="nav-item" onclick="filterCategory('SouthIndian', this)">
          <i class="fa-solid fa-pepper-hot"></i>
          <span>Món Nam Ấn</span>
        </button>
        <button class="nav-item" onclick="filterCategory('Chinese', this)">
          <i class="fa-solid fa-fire-burner"></i>
          <span>Món Hoa</span>
        </button>
        <button class="nav-item" onclick="filterCategory('Dessert', this)">
          <i class="fa-solid fa-cake-candles"></i>
          <span>Món tráng miệng</span>
        </button>
        <button class="nav-item" onclick="filterCategory('Drinks', this)">
          <i class="fa-solid fa-martini-glass-citrus"></i>
          <span>Thức uống</span>
        </button>

        <div class="nav-section-title">Cá nhân</div>
        <a href="viewUploadedRecipes.php" class="nav-item">
          <i class="fa-solid fa-book-bookmark"></i>
          <span>Kho món ngon của bạn</span>
        </a>
        <a href="index4.php" class="nav-item">
          <i class="fa-solid fa-circle-plus"></i>
          <span>Đăng công thức mới</span>
        </a>
      </div>

      <div class="sidebar-footer">
        <div class="user-profile-widget">
          <div class="user-avatar" id="sidebarAvatar">U</div>
          <div class="user-meta">
            <div class="user-meta-name" id="sidebarUserName">Đang tải...</div>
            <div class="user-meta-role">Thành viên Cook Together</div>
          </div>
          <button class="btn-logout" title="Đăng xuất" onclick="logout()">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
          </button>
        </div>
      </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div class="main-wrapper">
      
      <!-- TOP HEADER -->
      <header class="top-header">
        <button class="mobile-toggle" onclick="toggleSidebar()">
          <i class="fa-solid fa-bars"></i>
        </button>

        <div class="search-box">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" id="searchInput" class="search-input" placeholder="Tìm tên món ăn, nguyên liệu, cách làm..." oninput="handleSearch(this.value)">
        </div>

        <div class="header-actions">
          <a href="index4.php" class="btn-create-recipe">
            <i class="fa-solid fa-plus"></i>
            <span>Viết món mới</span>
          </a>
        </div>
      </header>

      <!-- PAGE BODY CONTENT -->
      <main class="page-content">
        
        <!-- HERO BANNER (COOKPAD BANNER) -->
        <section class="hero-banner">
          <div class="hero-text">
            <h2>Chưa biết hôm nay nấu gì?</h2>
            <p>Khám phá hàng ngàn công thức nấu ăn ngon, chuẩn vị và dễ làm từ cộng đồng yêu bếp Cook Together!</p>
            <button class="hero-btn" onclick="setSort('top_rated', document.querySelector('.sort-pill[data-sort=top_rated]'))">
              <i class="fa-solid fa-star text-warning"></i>
              <span>Xem món đánh giá cao nhất</span>
            </button>
          </div>
        </section>

        <!-- CATEGORY QUICK FILTER BAR -->
        <section class="section-header">
          <h2 class="section-title" id="currentSectionTitle">
            <i class="fa-solid fa-fire text-primary"></i>
            <span>Gợi ý món ngon hôm nay</span>
          </h2>
          <div class="results-count-badge" id="resultsCountBadge">
            <i class="fa-solid fa-utensils"></i> Đang tải món...
          </div>
        </section>

        <!-- DANH MỤC MÓN ĂN -->
        <div class="category-bar">
          <button class="category-pill active" onclick="filterCategory('all', this)">
            <i class="fa-solid fa-border-all"></i> Tất cả
          </button>
          <button class="category-pill" onclick="filterCategory('Vietnamese', this)">
            🇻🇳 Món Việt
          </button>
          <button class="category-pill" onclick="filterCategory('NorthIndian', this)">
            🥘 Món Bắc Ấn
          </button>
          <button class="category-pill" onclick="filterCategory('SouthIndian', this)">
            🍛 Món Nam Ấn
          </button>
          <button class="category-pill" onclick="filterCategory('Chinese', this)">
            🥢 Món Hoa
          </button>
          <button class="category-pill" onclick="filterCategory('Dessert', this)">
            🍰 Món tráng miệng
          </button>
          <button class="category-pill" onclick="filterCategory('Drinks', this)">
            🍹 Thức uống
          </button>
        </div>

        <!-- BỘ LỌC ĐÁNH GIÁ DẠNG THANH TRƯỢT (RANGE SLIDER) & SẮP XẾP -->
        <div class="filter-sort-wrapper">
          <!-- DÒNG 1: SẮP XẾP ƯU TIÊN -->
          <div class="filter-row">
            <div class="filter-group">
              <span class="filter-label"><i class="fa-solid fa-arrow-down-wide-short"></i> Sắp xếp:</span>
              <button type="button" class="sort-pill active" data-sort="top_rated" onclick="setSort('top_rated', this)">
                <i class="fa-solid fa-star"></i> ⭐ Đánh giá cao nhất
              </button>
              <button type="button" class="sort-pill" data-sort="newest" onclick="setSort('newest', this)">
                <i class="fa-solid fa-clock-rotate-left"></i> Mới đăng
              </button>
              <button type="button" class="sort-pill" data-sort="popular" onclick="setSort('popular', this)">
                <i class="fa-solid fa-heart"></i> Yêu thích & Bình luận
              </button>
              <button type="button" class="sort-pill" data-sort="time_asc" onclick="setSort('time_asc', this)">
                <i class="fa-solid fa-bolt"></i> Nấu nhanh
              </button>
            </div>
          </div>

          <!-- DÒNG 2: BỘ LỌC ĐÁNH GIÁ DẠNG THANH TRƯỢT (RANGE SLIDER) -->
          <div class="slider-filter-box">
            <span class="filter-label">
              <i class="fa-solid fa-sliders text-primary"></i> Lọc điểm đánh giá:
            </span>

            <div class="slider-track-container">
              <input 
                type="range" 
                id="ratingSlider" 
                class="custom-range-slider" 
                min="0" 
                max="5" 
                step="0.5" 
                value="0" 
                oninput="onSliderInput(this.value)"
                onchange="onSliderChange(this.value)"
              >
              <div class="slider-ticks">
                <span onclick="setSliderValue(0)">0★ (Tất cả)</span>
                <span onclick="setSliderValue(1)">1★</span>
                <span onclick="setSliderValue(2)">2★</span>
                <span onclick="setSliderValue(3)">3★</span>
                <span onclick="setSliderValue(4)">4★</span>
                <span onclick="setSliderValue(4.5)">4.5★</span>
                <span onclick="setSliderValue(5)">5★</span>
              </div>
            </div>

            <div class="slider-value-badge" id="sliderValueBadge">
              <i class="fa-solid fa-star" style="color: #f59e0b;"></i>
              <span id="sliderValueText">Tất cả sao (0★ - 5★)</span>
            </div>

            <button type="button" class="btn-reset-filter" id="btnResetRating" onclick="setSliderValue(0)" title="Xem lại tất cả sao">
              <i class="fa-solid fa-rotate-left"></i> Đặt lại
            </button>
          </div>
        </div>

        <!-- RECIPES FEED GRID -->
        <div class="recipes-grid" id="recipesGrid">
          <!-- Loaded dynamically via JavaScript -->
        </div>

      </main>
    </div>
  </div>

  <script>
    let currentCategory = 'all';
    let currentSort = 'top_rated';
    let currentMinRating = 0;
    let searchQuery = '';
    let searchDebounceTimer = null;

    window.onload = function () {
      updateSliderTrack(0);
      fetchUserInfo();
      fetchRecipes();
    };

    function toggleSidebar() {
      document.getElementById('appSidebar').classList.toggle('open');
    }

    // Fetch user name from session API
    function fetchUserInfo() {
      var xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
          try {
            var response = JSON.parse(xhr.responseText);
            var userName = response.name || "Bếp Trưởng";
            document.getElementById("sidebarUserName").innerText = userName;
            document.getElementById("sidebarAvatar").innerText = userName.charAt(0).toUpperCase();
          } catch(e) {
            document.getElementById("sidebarUserName").innerText = "Bếp Trưởng";
          }
        }
      };
      xhr.open("GET", "getUserById.php", true);
      xhr.send();
    }

    // Sắp xếp
    function setSort(sortType, element) {
      currentSort = sortType;
      document.querySelectorAll(".sort-pill").forEach(btn => btn.classList.remove("active"));
      if (element) {
        element.classList.add("active");
      }
      fetchRecipes();
    }

    // Xử lý khi đang kéo thanh trượt (Live preview text & track color)
    function onSliderInput(val) {
      var rating = parseFloat(val);
      updateSliderTrack(rating);
      updateSliderBadge(rating);
    }

    // Xử lý khi nhả thanh trượt -> gọi API lọc
    function onSliderChange(val) {
      currentMinRating = parseFloat(val);
      fetchRecipes();
    }

    // Gán giá trị trực tiếp cho thanh trượt (khi click vào các mốc hoặc nút Đặt lại)
    function setSliderValue(val) {
      var slider = document.getElementById("ratingSlider");
      slider.value = val;
      currentMinRating = parseFloat(val);
      updateSliderTrack(currentMinRating);
      updateSliderBadge(currentMinRating);
      fetchRecipes();
    }

    function updateSliderTrack(rating) {
      var slider = document.getElementById("ratingSlider");
      var percentage = (rating / 5) * 100;
      slider.style.background = `linear-gradient(to right, #e27227 0%, #f97316 ${percentage}%, #e5e7eb ${percentage}%, #e5e7eb 100%)`;
    }

    function updateSliderBadge(rating) {
      var textEl = document.getElementById("sliderValueText");
      if (rating === 0) {
        textEl.innerText = "Tất cả sao (0★ - 5★)";
      } else {
        textEl.innerHTML = `Từ <b>${rating.toFixed(1)}★</b> trở lên`;
      }
    }

    // Fetch recipes from getRecipes.php
    function fetchRecipes() {
      var grid = document.getElementById("recipesGrid");
      grid.innerHTML = '<div class="empty-state"><i class="fa-solid fa-spinner fa-spin"></i><p>Đang tải món ngon theo đánh giá...</p></div>';

      var url = "getRecipes.php?category=" + encodeURIComponent(currentCategory) +
                "&sort=" + encodeURIComponent(currentSort);

      if (currentMinRating > 0) {
        url += "&min_rating=" + encodeURIComponent(currentMinRating);
      }

      if (searchQuery.trim() !== '') {
        url += "&q=" + encodeURIComponent(searchQuery.trim());
      }

      var xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
          try {
            var recipes = JSON.parse(xhr.responseText);
            renderRecipes(recipes);
          } catch (e) {
            console.error("Error parsing response", e);
            renderRecipes([]);
          }
        }
      };
      xhr.open("GET", url, true);
      xhr.send();
    }

    function renderRecipes(recipes) {
      var grid = document.getElementById("recipesGrid");
      var countBadge = document.getElementById("resultsCountBadge");
      grid.innerHTML = "";

      if (!recipes || recipes.length === 0) {
        countBadge.innerHTML = `Tìm thấy <b>0</b> món`;
        grid.innerHTML = `
          <div class="empty-state">
            <i class="fa-solid fa-utensils"></i>
            <h3>Chưa tìm thấy công thức nào từ ${currentMinRating}★ trở lên!</h3>
            <p>Hãy kéo thanh trượt về mức sao thấp hơn hoặc chọn từ khóa khác nhé.</p>
            <button class="hero-btn" onclick="setSliderValue(0)" style="margin-top: 16px;">
              <i class="fa-solid fa-rotate-left"></i> Kéo về xem tất cả sao
            </button>
          </div>
        `;
        return;
      }

      var filterText = currentMinRating > 0 ? ` (từ ${currentMinRating}★ trở lên)` : '';
      countBadge.innerHTML = `Hiển thị <b>${recipes.length}</b> món ngon${filterText}`;

      recipes.forEach(function (recipe, index) {
        var card = document.createElement("div");
        card.className = "recipe-card";
        card.onclick = function () {
          window.location.href = "viewRecipeDetails.php?recipe_id=" + recipe.id;
        };

        var photoUrl = recipe.photo ? recipe.photo : 'upload.jpeg';
        var ratingNum = parseFloat(recipe.avg_rating);
        var ratingCount = parseInt(recipe.rating_count) || 0;
        
        var hasRating = !isNaN(ratingNum) && ratingNum > 0 && ratingCount > 0;
        var ratingDisplay = hasRating ? ratingNum.toFixed(1) : "5.0";
        var ratingSubText = hasRating ? `(${ratingCount})` : '(Mới)';

        var uploader = recipe.uploader_name || "Đầu bếp Cook Together";
        var cookingTime = recipe.cooking_time ? recipe.cooking_time + " phút" : "30 phút";
        var desc = recipe.description || "Công thức món ngon thơm lừng, dễ làm cho cả gia đình!";

        // Tag Top Rated cho các món điểm cao nhất ở trang đầu khi sort top_rated
        var topRatedHtml = '';
        if (currentSort === 'top_rated' && hasRating && ratingNum >= 4.8 && index < 6) {
          topRatedHtml = `<span class="top-rated-tag"><i class="fa-solid fa-crown"></i> Top Đánh Giá</span>`;
        }

        card.innerHTML = `
          <div class="recipe-image-wrap">
            ${topRatedHtml}
            <img src="${photoUrl}" alt="${recipe.title}" class="recipe-img" onerror="this.src='landing_page.jpg'">
            <span class="badge-time"><i class="fa-regular fa-clock"></i> ${cookingTime}</span>
            <span class="badge-category">${recipe.type || 'Món ngon'}</span>
          </div>
          <div class="recipe-body">
            <h3 class="recipe-title">${recipe.title}</h3>
            <p class="recipe-desc">${desc}</p>
            <div class="recipe-footer">
              <div class="recipe-uploader" onclick="event.stopPropagation(); window.location.href='author.php?user_id=' + (recipe.user_id || 1);" title="Xem tất cả món của ${uploader}" style="cursor: pointer;">
                <div class="uploader-mini-avatar">${uploader.charAt(0).toUpperCase()}</div>
                <span style="transition: color 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='inherit'">${uploader}</span>
              </div>
              <div class="rating-badge" title="Đánh giá trung bình: ${ratingDisplay} / 5.0 (${ratingCount} lượt)">
                <i class="fa-solid fa-star"></i>
                <span>${ratingDisplay}</span>
                <span class="rating-count-sub">${ratingSubText}</span>
              </div>
            </div>
          </div>
        `;

        grid.appendChild(card);
      });
    }

    function filterCategory(category, element) {
      currentCategory = category;

      // Update sidebar active item
      document.querySelectorAll(".sidebar-nav .nav-item").forEach(btn => btn.classList.remove("active"));
      // Update category pill active
      document.querySelectorAll(".category-pill").forEach(btn => btn.classList.remove("active"));

      if (element) {
        element.classList.add("active");
      }

      var titleMap = {
        'all': 'Gợi ý món ngon hôm nay',
        'Vietnamese': '🇻🇳 Món ngon thuần Việt',
        'NorthIndian': '🥘 Món ngon phong vị Bắc Ấn',
        'SouthIndian': '🍛 Món ngon phong vị Nam Ấn',
        'Chinese': '🥢 Món ngon phong vị Trung Hoa',
        'Dessert': '🍰 Món bánh & Tráng miệng ngọt ngào',
        'Drinks': '🍹 Thức uống tươi mát'
      };

      document.getElementById("currentSectionTitle").querySelector("span").innerText = titleMap[category] || ('Danh mục: ' + category);
      fetchRecipes();
    }

    function handleSearch(val) {
      clearTimeout(searchDebounceTimer);
      searchDebounceTimer = setTimeout(function () {
        searchQuery = val;
        fetchRecipes();
      }, 300);
    }

    function logout() {
      if (confirm("Bạn có chắc chắn muốn đăng xuất không?")) {
        window.location.href = "index.php";
      }
    }
  </script>
</body>
</html>