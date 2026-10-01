<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cook Together - Danh sách món ngon</title>
    <link rel="stylesheet" href="index51.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
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

        <div id="recipe-list" class="recipes-grid">
            <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
                <i class="fa-solid fa-spinner fa-spin" style="font-size: 32px; color: var(--primary);"></i>
                <p style="margin-top: 10px; color: #6b7280;">Đang tìm món ngon...</p>
            </div>
        </div>
    </div>

    <script>
        var categoryParam = new URLSearchParams(window.location.search).get('category') || 'all';

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
            xhr.open("GET", "getRecipes.php?category=" + encodeURIComponent(categoryParam), true);
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
                    var avgRatingText = !isNaN(avg) && avg > 0 ? avg.toFixed(1) : "5.0";
                    var time = recipe.cooking_time ? recipe.cooking_time + " phút" : "30 phút";
                    var uploader = recipe.uploader_name || "Đầu bếp Cook Together";

                    card.innerHTML = `
                        <div class="recipe-img-wrap">
                            <img src="${photo}" alt="${recipe.title}" class="recipe-img" onerror="this.src='landing_page.jpg'">
                        </div>
                        <div class="recipe-body">
                            <h2 class="recipe-title">${recipe.title}</h2>
                            <p style="font-size: 13px; color: #6b7280; margin-bottom: 12px;">Đăng bởi: <strong>${uploader}</strong></p>
                            <div class="recipe-footer">
                                <span style="color: #4b5563; font-weight: 600;"><i class="fa-regular fa-clock"></i> ${time}</span>
                                <div class="rating-badge">
                                    <i class="fa-solid fa-star" style="color: var(--star-color);"></i>
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
                        <h3>Chưa có món nào trong danh mục này</h3>
                        <p style="color: #6b7280; margin-top: 4px;">Hãy là người đầu tiên chia sẻ công thức nhé!</p>
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
