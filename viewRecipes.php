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
            flex-direction: column;
            gap: 14px;
            background: #ffffff;
            padding: 16px 20px;
            border-radius: 16px;
            border: 1px solid #f0ebe1;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }
        .filter-row-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
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

        /* SLIDER STYLES */
        .slider-box-compact {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            background: #fafaf9;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid #f0ebe1;
        }
        .slider-container-c {
            flex: 1;
            min-width: 200px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .c-range-slider {
            -webkit-appearance: none;
            appearance: none;
            width: 100%;
            height: 7px;
            border-radius: 9999px;
            background: #e5e7eb;
            outline: none;
            cursor: pointer;
        }
        .c-range-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #ffffff;
            border: 3px solid #e27227;
            box-shadow: 0 2px 6px rgba(226, 114, 39, 0.4);
            cursor: pointer;
            transition: transform 0.15s;
        }
        .c-range-slider::-webkit-slider-thumb:hover {
            transform: scale(1.15);
        }
        .c-ticks {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            font-weight: 700;
            color: #9ca3af;
            padding: 0 2px;
        }
        .c-ticks span {
            cursor: pointer;
        }
        .c-ticks span:hover {
            color: #e27227;
        }
        .badge-slider-val {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #fff7ed;
            border: 1px solid #fdba74;
            color: #c2410c;
            font-weight: 800;
            font-size: 12px;
            padding: 5px 12px;
            border-radius: 9999px;
            min-width: 150px;
            justify-content: center;
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
            <div class="filter-row-top">
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
            </div>

            <!-- RATING RANGE SLIDER -->
            <div class="slider-box-compact">
                <span style="font-size: 13px; font-weight: 700; color: #6b7280;"><i class="fa-solid fa-sliders text-primary"></i> Lọc sao:</span>
                <div class="slider-container-c">
                    <input 
                        type="range" 
                        id="cRatingSlider" 
                        class="c-range-slider" 
                        min="0" 
                        max="5" 
                        step="0.5" 
                        value="0"
                        oninput="onCSliderInput(this.value)"
                        onchange="onCSliderChange(this.value)"
                    >
                    <div class="c-ticks">
                        <span onclick="setCSliderVal(0)">0★ (Tất cả)</span>
                        <span onclick="setCSliderVal(1)">1★</span>
                        <span onclick="setCSliderVal(2)">2★</span>
                        <span onclick="setCSliderVal(3)">3★</span>
                        <span onclick="setCSliderVal(4)">4★</span>
                        <span onclick="setCSliderVal(4.5)">4.5★</span>
                        <span onclick="setCSliderVal(5)">5★</span>
                    </div>
                </div>
                <div class="badge-slider-val" id="cSliderBadge">
                    <i class="fa-solid fa-star" style="color: #f59e0b;"></i>
                    <span id="cSliderBadgeText">Tất cả sao (0★ - 5★)</span>
                </div>
                <button type="button" onclick="setCSliderVal(0)" style="padding: 4px 10px; border-radius: 9999px; border: 1px solid #d1d5db; background: #fff; font-size: 11px; cursor: pointer;">
                    <i class="fa-solid fa-rotate-left"></i> Đặt lại
                </button>
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
            updateCSliderTrack(0);
            fetchRecipes();
        };

        function setSort(sort, btn) {
            currentSort = sort;
            btn.parentElement.querySelectorAll(".f-pill").forEach(p => p.classList.remove("active"));
            btn.classList.add("active");
            fetchRecipes();
        }

        function onCSliderInput(val) {
            var r = parseFloat(val);
            updateCSliderTrack(r);
            updateCSliderBadge(r);
        }

        function onCSliderChange(val) {
            currentMinRating = parseFloat(val);
            fetchRecipes();
        }

        function setCSliderVal(val) {
            var slider = document.getElementById("cRatingSlider");
            slider.value = val;
            currentMinRating = parseFloat(val);
            updateCSliderTrack(currentMinRating);
            updateCSliderBadge(currentMinRating);
            fetchRecipes();
        }

        function updateCSliderTrack(val) {
            var slider = document.getElementById("cRatingSlider");
            var pct = (val / 5) * 100;
            slider.style.background = `linear-gradient(to right, #e27227 0%, #f97316 ${pct}%, #e5e7eb ${pct}%, #e5e7eb 100%)`;
        }

        function updateCSliderBadge(val) {
            var txt = document.getElementById("cSliderBadgeText");
            if (val === 0) {
                txt.innerText = "Tất cả sao (0★ - 5★)";
            } else {
                txt.innerHTML = `Từ <b>${val.toFixed(1)}★</b> trở lên`;
            }
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
                        <h3>Chưa có món nào từ ${currentMinRating}★ trở lên</h3>
                        <p style="color: #6b7280; margin-top: 4px;">Hãy kéo thanh trượt về mức sao thấp hơn nhé!</p>
                        <button onclick="setCSliderVal(0)" class="btn-pill" style="margin-top: 16px; background: var(--primary); color: white; border: none; cursor: pointer;">
                            <i class="fa-solid fa-rotate-left"></i> Kéo về xem tất cả sao
                        </button>
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
