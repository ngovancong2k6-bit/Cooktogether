<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cook Together - Danh sách món ngon</title>
    <link rel="stylesheet" href="index51.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        .filter-sort-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            background: #ffffff;
            padding: 14px 20px;
            border-radius: 16px;
            border: 1px solid #f0ebe1;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }
        .filter-pills-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .f-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 9999px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            font-size: 13px;
            font-weight: 600;
            color: #4b5563;
            cursor: pointer;
            transition: all 0.2s;
        }
        .f-pill:hover {
            background: #fff7ed;
            border-color: #fdba74;
            color: #e27227;
        }
        .f-pill.active {
            background: linear-gradient(135deg, #f97316, #e27227);
            border-color: transparent;
            color: #ffffff;
            font-weight: 700;
        }
        .f-pill.active i {
            color: #ffffff;
        }
    </style>
</head>
<body>

    <nav class="recipes-navbar">
        <a href="index3.php" class="brand-link">
            <i class="fa-solid fa-utensils"></i> Cook Together
        </a>
        <div style="display: flex; gap: 12px;">
            <a href="index3.php" class="btn-pill">
                <i class="fa-solid fa-house"></i> Trang chủ
            </a>
            <a href="index4.php" class="btn-pill" style="background: var(--primary); color: white; border: none;">
                <i class="fa-solid fa-plus"></i> Viết món mới
            </a>
        </div>
    </nav>

    <div class="container">
        <div class="category-header">
            <div>
                <h1 class="category-title" id="pageTitle">
                    <i class="fa-solid fa-fire text-primary"></i> Đang tải danh mục...
                </h1>
                <p style="color: #6b7280; font-size: 14px; margin-top: 4px;">Khám phá các công thức được yêu thích nhất từ cộng đồng bếp Cook Together.</p>
            </div>
            <button onclick="goBack()" class="btn-pill">
                <i class="fa-solid fa-arrow-left"></i> Quay lại
            </button>
        </div>

        <!-- FILTER & SORT TOOLBAR -->
        <div class="filter-sort-bar">
            <div class="filter-pills-group">
                <span style="font-size: 13px; font-weight: 700; color: #6b7280;"><i class="fa-solid fa-arrow-down-wide-short"></i> Sắp xếp:</span>
                <button type="button" class="f-pill active" onclick="setSort('top_rated', this)">
                    <i class="fa-solid fa-star"></i> ⭐ Đánh giá cao nhất
                </button>
                <button type="button" class="f-pill" onclick="setSort('newest', this)">
                    <i class="fa-solid fa-clock"></i> Mới nhất
                </button>
                <button type="button" class="f-pill" onclick="setSort('popular', this)">
                    <i class="fa-solid fa-heart"></i> Yêu thích nhất
                </button>
            </div>
            <div class="filter-pills-group">
                <span style="font-size: 13px; font-weight: 700; color: #6b7280;"><i class="fa-solid fa-filter"></i> Lọc sao:</span>
                <button type="button" class="f-pill active" onclick="setMinRating(0, this)">Tất cả</button>
                <button type="button" class="f-pill" onclick="setMinRating(4.5, this)">⭐ 4.5+</button>
                <button type="button" class="f-pill" onclick="setMinRating(4.0, this)">⭐ 4.0+</button>
            </div>
        </div>

        <div id="recipe-list" class="recipes-grid">
            <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
                <i class="fa-solid fa-spinner fa-spin" style="font-size: 32px; color: var(--primary);"></i>
                <p style="margin-top: 10px; color: #6b7280;">Đang tìm món ngon...</p>
            </div>
        </div>
    </div>

    <script>
        var categoryParam = new URLSearchParams(window.location.search).get('category') || 'all';
        var currentSort = 'top_rated';
        var currentMinRating = 0;

        window.onload = function () {
            var titleMap = {
                'all': 'Tất cả món ngon',
                'Vietnamese': '🇻🇳 Món ngon thuần Việt',
                'NorthIndian': '🥘 Món ngon phong vị Bắc Ấn',
                'SouthIndian': '🍛 Món ngon phong vị Nam Ấn',
                'Chinese': '🥢 Món ngon phong vị Trung Hoa',
                'Dessert': '🍰 Món bánh & Tráng miệng ngọt ngào',
                'Drinks': '🍹 Thức uống tươi mát'
            };
            document.getElementById("pageTitle").innerHTML = '<i class="fa-solid fa-utensils" style="color: var(--primary); margin-right: 8px;"></i> ' + (titleMap[categoryParam] || ('Danh mục: ' + categoryParam));
            fetchRecipes();
        };

        function setSort(sort, btn) {
            currentSort = sort;
            btn.parentElement.querySelectorAll(".f-pill").forEach(p => p.classList.remove("active"));
            btn.classList.add("active");
            fetchRecipes();
        }

        function setMinRating(rating, btn) {
            currentMinRating = rating;
            btn.parentElement.querySelectorAll(".f-pill").forEach(p => p.classList.remove("active"));
            btn.classList.add("active");
            fetchRecipes();
        }

        function fetchRecipes() {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    try {
                        var recipes = JSON.parse(xhr.responseText);
                        displayRecipes(recipes);
                    } catch(e) {
                        displayRecipes([]);
                    }
                }
            };
            var url = "getRecipes.php?category=" + encodeURIComponent(categoryParam) + "&sort=" + encodeURIComponent(currentSort);
            if (currentMinRating > 0) {
                url += "&min_rating=" + encodeURIComponent(currentMinRating);
            }
            xhr.open("GET", url, true);
            xhr.send();
        }

        function displayRecipes(recipes) {
            var recipeListDiv = document.getElementById("recipe-list");
            recipeListDiv.innerHTML = "";

            if (recipes && recipes.length > 0) {
                recipes.forEach(function (recipe) {
                    var card = document.createElement("div");
                    card.className = "recipe-card";
                    card.onclick = function() {
                        window.location.href = "viewRecipeDetails.php?recipe_id=" + recipe.id;
                    };

                    var photo = recipe.photo ? recipe.photo : 'upload.jpeg';
                    var avg = parseFloat(recipe.avg_rating);
                    var ratingCount = parseInt(recipe.rating_count) || 0;
                    var avgRatingText = !isNaN(avg) && avg > 0 && ratingCount > 0 ? avg.toFixed(1) + ` (${ratingCount})` : "5.0 (Mới)";
                    var time = recipe.cooking_time ? recipe.cooking_time + " phút" : "30 phút";
                    var uploader = recipe.uploader_name || "Đầu bếp Cook Together";

                    card.innerHTML = `
                        <div class="recipe-img-wrap">
                            <img src="${photo}" alt="${recipe.title}" class="recipe-img" onerror="this.src='landing_page.jpg'">
                        </div>
                        <div class="recipe-body">
                            <h2 class="recipe-title">${recipe.title}</h2>
                            <p style="font-size: 13px; color: #6b7280; margin-bottom: 12px; cursor: pointer;" onclick="event.stopPropagation(); window.location.href='author.php?user_id=' + (recipe.user_id || 1);" title="Xem tất cả món của ${uploader}">
                                Đăng bởi: <strong style="color: var(--primary);">${uploader}</strong>
                            </p>
                            <div class="recipe-footer">
                                <span style="color: #4b5563; font-weight: 600;"><i class="fa-regular fa-clock"></i> ${time}</span>
                                <div class="rating-badge">
                                    <i class="fa-solid fa-star" style="color: var(--star-color, #f59e0b);"></i>
                                    <span>${avgRatingText}</span>
                                </div>
                            </div>
                        </div>
                    `;

                    recipeListDiv.appendChild(card);
                });
            } else {
                recipeListDiv.innerHTML = `
                    <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px; background: white; border-radius: 20px; border: 1px dashed #f0ebe1;">
                        <i class="fa-solid fa-utensils" style="font-size: 40px; color: #d1d5db; margin-bottom: 12px;"></i>
                        <h3>Chưa có món nào phù hợp</h3>
                        <p style="color: #6b7280; margin-top: 4px;">Hãy thử điều chỉnh lại bộ lọc đánh giá nhé!</p>
                        <a href="index4.php" class="btn-pill" style="margin-top: 16px; background: var(--primary); color: white; border: none;">
                            <i class="fa-solid fa-plus"></i> Đăng món ngay
                        </a>
                    </div>
                `;
            }
        }

        function goBack() {
            window.location.href = "index3.php";
        }
    </script>
</body>
</html>
