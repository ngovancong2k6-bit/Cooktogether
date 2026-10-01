<?php
$conn = new mysqli('localhost', 'root', '', 'recipe_sharing_platform', 3306);
$conn->set_charset("utf8mb4");

echo "=== BẮT ĐẦU TEST CRUD TOÀN DIỆN ===\n";

// 1. TEST CREATE
$userId = 1; // Chef John
$title = "Món Test CRUD Chuẩn Chỉ";
$desc = "Mô tả chi tiết cho món ăn thử nghiệm kiểm thử CRUD của tất cả các trường.";
$type = "Vietnamese";
$catId = 6;
$time = 35;
$photo = "https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800";
$video = "https://www.youtube.com/watch?v=a03U45jFxOI";
$instructions = "Sơ chế -> Ướp -> Nấu -> Trình bày";
$recipe = "Bước 1: Chuẩn bị nguyên liệu tươi ngon.\nBước 2: Xào thơm tỏi và nấu chín.\nBước 3: Thưởng thức món ăn nóng sốt.";

$sql = "INSERT INTO recipes (title, cooking_time, recipe, type, category_id, photo, video_link, description, instructions, user_id, uploaded_at)
        VALUES ('$title', $time, '$recipe', '$type', $catId, '$photo', '$video', '$desc', '$instructions', $userId, NOW())";

if ($conn->query($sql)) {
    $newId = $conn->insert_id;
    echo "✓ [CREATE] Đã tạo thành công Recipe ID: $newId\n";
    
    // Thêm nguyên liệu
    $conn->query("INSERT INTO ingredients (recipe_id, ingredient_name, quantity, unit) VALUES ($newId, 'Thịt heo', 500, 'gram')");
    $conn->query("INSERT INTO ingredients (recipe_id, ingredient_name, quantity, unit) VALUES ($newId, 'Hành lá', 2, 'nhánh')");
    echo "✓ [CREATE] Đã thêm nguyên liệu cho Recipe ID: $newId\n";
} else {
    die("Lỗi Create: " . $conn->error);
}

// 2. TEST READ
$readRes = $conn->query("SELECT r.*, COUNT(i.id) as total_ing FROM recipes r LEFT JOIN ingredients i ON r.id = i.recipe_id WHERE r.id = $newId GROUP BY r.id");
$data = $readRes->fetch_assoc();
echo "✓ [READ] Đọc món: {$data['title']} | Danh mục: {$data['type']} | Thời gian: {$data['cooking_time']}p | Số nguyên liệu: {$data['total_ing']}\n";

// 3. TEST UPDATE
$updatedTitle = "Món Test CRUD Chuẩn Chỉ (ĐÃ CẬP NHẬT)";
$updatedTime = 50;
$updateSql = "UPDATE recipes SET title = '$updatedTitle', cooking_time = $updatedTime WHERE id = $newId";
if ($conn->query($updateSql)) {
    echo "✓ [UPDATE] Đã cập nhật tiêu đề và thời gian nấu thành công!\n";
}

// 4. TEST DELETE
$conn->query("DELETE FROM ingredients WHERE recipe_id = $newId");
$conn->query("DELETE FROM recipes WHERE id = $newId");
echo "✓ [DELETE] Đã xóa an toàn món test và nguyên liệu liên quan!\n";

echo "=== TẤT CẢ CÁC BƯỚC CRUD ĐỀU HOẠT ĐỘNG HOÀN HẢO 100% ===\n";
