# COOK TOGETHER – Tài liệu ôn demo (FE → BE → DB)

> Website chia sẻ công thức nấu ăn. **PHP thuần (không framework) + MySQL + HTML/CSS/JS thuần**, chạy trên **XAMPP** (Apache + MySQL).
> Tài liệu này giúp bạn: (1) nắm các chức năng, (2) trả lời được "nút này chạy code nào, ghi vào bảng nào", (3) chỉ cách mở F12 để lần từ màn hình xuống BE nếu quên.

---

## 0. TÓM TẮT 30 GIÂY (học thuộc câu này)

> "Em làm web chia sẻ công thức nấu ăn tên Cook Together. Frontend là HTML/CSS/JavaScript, backend là PHP, cơ sở dữ liệu MySQL, chạy trên XAMPP. Người dùng đăng ký/đăng nhập, xem và tìm kiếm món theo danh mục, đăng/sửa/xóa công thức của mình, xem chi tiết có co giãn khẩu phần nguyên liệu, đánh giá sao và bình luận, xem trang hồ sơ đầu bếp. FE gọi BE theo 2 cách: **form submit** (đăng nhập, đăng ký, đăng món, sửa món) và **AJAX/XMLHttpRequest** (danh sách món, tìm kiếm, tên user, đánh giá, bình luận). Đăng nhập được giữ bằng **PHP Session**."

**Kiến trúc:**

```
Trình duyệt (HTML/CSS/JS)  ──HTTP──>  Apache + PHP (XAMPP)  ──mysqli/PDO──>  MySQL: recipe_sharing_platform
   index3.php, index4.php…            login.php, getRecipes.php…                users, recipes, ingredients,
                                                                                categories, ratings, comments
```

Không có API REST riêng/không có framework: **mỗi file .php vừa là trang, vừa là "controller", vừa truy vấn DB**. Các file trả JSON (`getRecipes.php`, `getUserById.php`, `addRating.php`, `addComment.php`, `logs.php`) đóng vai trò "API".

---

## 1. CẤU TRÚC THƯ MỤC (chỉ cần nhớ thư mục gốc)

| File | Vai trò |
|---|---|
| `index.php` | Trang **Đăng nhập** (form → `login.php`) |
| `index2.php` | Trang **Đăng ký** (form → `register.php`) |
| `index3.php` | **Trang chủ**: sidebar danh mục, ô tìm kiếm, lưới món ăn |
| `index4.php` | Form **Đăng công thức mới** (→ `upload.php`) |
| `viewRecipes.php` | Danh sách món theo danh mục (`?category=...`) |
| `viewRecipeDetails.php` | **Chi tiết món** + co giãn nguyên liệu + đánh giá + bình luận |
| `viewUploadedRecipes.php` | **Kho món của tôi** (quản lý: xem / sửa / xóa) |
| `edit.php` | Sửa công thức (vừa hiển thị form, vừa xử lý POST) |
| `author.php` | **Hồ sơ đầu bếp** + các món của họ |
| `login.php`, `register.php`, `upload.php`, `delete.php` | Xử lý (không có giao diện) |
| `getRecipes.php`, `getUserById.php`, `addRating.php`, `addComment.php` | "API" cho AJAX |
| `logs.php`, `watch_logs.ps1` | Công cụ xem log Apache + sinh lệnh curl (để demo/debug) |
| `view.php` | Bản cũ của trang chi tiết (không còn dùng) |
| `recipe_sharing_platform.sql` | **Dump DB đầy đủ + dữ liệu mẫu** (import vào phpMyAdmin) |
| `MySQL code/SQL CODE.txt` | Script tạo bảng gốc (bản đầu) |
| `scratch/` | Script phụ: sinh dữ liệu mẫu (`generate_all_recipes.php`), kiểm tra schema/CRUD |
| `index*.css`, `user_upload_style.css`… | CSS |
| `recipe_<timestamp>_<rand>.jpg` | **Ảnh người dùng upload** (lưu ngay thư mục gốc) |
| `main page/`, `login and signup/`, `view recipes/`, `uploading and crud operation/`, `images/`, `Recipe-sharing-platform-master/` | **Bản sao cũ** của cùng code (giai đoạn làm nhóm/chia module). Code chạy thật là ở **thư mục gốc**. |

> Nếu cô hỏi "sao có nhiều thư mục giống nhau?": đó là các bản sao từ lúc chia module ban đầu; bản chạy chính là thư mục gốc.

---

## 2. CƠ SỞ DỮ LIỆU (`recipe_sharing_platform`)

```
users (id PK, name, email, password)
   │1                                   │1
   │n                                   │n
recipes (id PK, title, cooking_time, recipe, type, category_id FK→categories,
         description, instructions, photo, video_link, user_id FK→users, uploaded_at)
   │1            │1             │1
   │n            │n             │n
ingredients   ratings         comments
(id, recipe_id, (id, user_id,  (id, recipe_id, user_id,
 ingredient_name, recipe_id,    content, created_at)
 quantity, unit) rating 2,1)
categories (id, category_name)   → 1 NorthIndian, 2 SouthIndian, 3 Chinese, 4 Dessert, 5 Drinks, 6 Vietnamese
recipe_ingredients (recipe_id, ingredient_id)  → bảng nối, KHÔNG được code dùng
```

| Bảng | Ý nghĩa | Ghi chú hay bị hỏi |
|---|---|---|
| `users` | Tài khoản | `password` lưu **văn bản thường** (chưa hash) |
| `categories` | 6 danh mục | `recipes.category_id` là khóa ngoại |
| `recipes` | Công thức | `recipe` = các bước nấu (mỗi bước 1 dòng "Bước 1: …"); `type` = tên danh mục (lưu trùng cùng `category_id`); `photo` = tên file hoặc URL |
| `ingredients` | Nguyên liệu của món | `quantity` DECIMAL(10,2) + `unit`; xóa món thì xóa theo (ON DELETE CASCADE) |
| `ratings` | Đánh giá 1–5 sao | `rating` DECIMAL(2,1); mỗi (user, món) chỉ 1 dòng do **code** kiểm tra (DB không có UNIQUE) |
| `comments` | Bình luận | `created_at` = `NOW()` |

**Quan hệ:** 1 user – n recipes; 1 recipe – n ingredients / ratings / comments; 1 category – n recipes.

---

## 3. CÁCH FE GỌI BE (2 kiểu) + SESSION

1. **Form submit (tải lại trang):** `<form action="xxx.php" method="post">` → PHP xử lý → `header("Location: ...")` hoặc in `<script>alert(...)</script>`.
   Dùng cho: đăng nhập, đăng ký, đăng món, sửa món.
2. **AJAX (không tải lại trang):** `XMLHttpRequest` → PHP trả **JSON** → JS vẽ lại HTML.
   Dùng cho: tải danh sách món/tìm kiếm/lọc danh mục, lấy tên user, gửi sao, gửi bình luận.
3. **Session:** `login.php` đúng mật khẩu → `$_SESSION['user_id'] = id`. Các file khác gọi `session_start()` rồi đọc `$_SESSION['user_id']` để biết ai đang đăng nhập. Cookie `PHPSESSID` (xem ở F12 → Application → Cookies) là "chìa khóa" của session.
   - Chưa đăng nhập mà vào `index4.php / viewUploadedRecipes.php / edit.php / delete.php` → bị đá về `index.php`.
   - Chưa đăng nhập mà gửi sao/bình luận → server trả **HTTP 401**.

---

## 4. LUỒNG CHI TIẾT TỪNG CHỨC NĂNG

> Mỗi chức năng: **Màn hình → FE → Request → BE → DB → Kết quả** + **cách xem bằng F12**.

### 4.1. Đăng ký

| | |
|---|---|
| Màn hình | `index2.php` – nhập Họ tên, Email, Mật khẩu |
| FE | `<form action="register.php" method="post">` (index2.php:305) |
| Request | `POST register.php` body: `name, email, password` |
| BE `register.php` | 1) kiểm tra mật khẩu dài 4–20 ký tự; 2) `SELECT * FROM users WHERE email='…'` (email đã tồn tại?); 3) nếu chưa: `INSERT INTO users (name,email,password) VALUES (?,?,?)` (prepared statement) |
| DB | thêm 1 dòng bảng **users** |
| Kết quả | alert "Đăng ký thành công" → chuyển về `index.php` |

**F12:** Network → bấm Đăng ký → thấy dòng `register.php` (Method POST) → tab **Payload** xem name/email/password → tab **Response** thấy đoạn `<script>alert(...)`.

### 4.2. Đăng nhập

| | |
|---|---|
| Màn hình | `index.php` |
| FE | `<form action="login.php" method="post">` (index.php:338) |
| Request | `POST login.php` body: `email, password` |
| BE `login.php` | `SELECT id FROM users WHERE email = ? AND password = ?` (prepared). Có dòng → `session_start(); $_SESSION['user_id'] = id; header("Location: index3.php")` |
| DB | đọc bảng **users** |
| Kết quả | Đúng → trang chủ `index3.php`. Sai → alert "Email hoặc mật khẩu không chính xác" |

**F12:** Network (bật **Preserve log**) → submit → `login.php` trả **302 Found**, header `Location: index3.php` và `Set-Cookie: PHPSESSID=…` → rồi tự gọi `index3.php`. Đây là bằng chứng "đăng nhập = session".

### 4.3. Trang chủ: tải danh sách món, lọc danh mục, tìm kiếm

| | |
|---|---|
| Màn hình | `index3.php` – sidebar (Trang chủ, Món Việt, Bắc Ấn, Nam Ấn, Món Hoa, Tráng miệng, Thức uống), ô tìm kiếm, lưới thẻ món |
| FE | `window.onload` → `fetchUserInfo()` + `fetchRecipes()` (index3.php:164). Bấm danh mục → `filterCategory()` đổi biến `currentCategory` rồi `fetchRecipes()`. Gõ tìm kiếm → `handleSearch()` có **debounce 300 ms** rồi `fetchRecipes()` |
| Request | `GET getRecipes.php?category=Vietnamese&q=phở` và `GET getUserById.php` |
| BE `getRecipes.php` | Đọc tham số `category`, `q`, `recipe_id`, `user_id` → ghép `WHERE` (dùng `real_escape_string`/`intval`). Tìm kiếm: `title LIKE '%q%' OR recipe LIKE … OR description LIKE …`. Truy vấn: `recipes LEFT JOIN users LEFT JOIN ratings LEFT JOIN comments … GROUP BY recipes.id ORDER BY id DESC`, tính **AVG(rating)** và **COUNT(comments)**. Trả JSON |
| BE `getUserById.php` | Lấy `$_SESSION['user_id']` → `SELECT name FROM users WHERE id=?` → trả `{"name": "..."}` để hiện tên + chữ cái avatar ở sidebar |
| DB | đọc **recipes, users, ratings, comments** |
| Kết quả | JS `renderRecipes()` (index3.php:219) tạo từng thẻ món (ảnh, tên, người đăng, thời gian, sao TB). Bấm thẻ → `viewRecipeDetails.php?recipe_id=…`; bấm tên người đăng → `author.php?user_id=…` |

**F12 (đây là chức năng dễ demo nhất):** Network → lọc **Fetch/XHR** → F5 trang → thấy `getRecipes.php?category=all` và `getUserById.php`. Bấm vào `getRecipes.php` → tab **Response/Preview** là mảng JSON các món (id, title, photo, avg_rating, uploader_name…). Đổi danh mục / gõ tìm kiếm → có request mới với `category=` hoặc `q=` thay đổi.

> Lưu ý: `viewRecipes.php?category=…` là trang danh sách riêng cho từng danh mục, dùng cùng `getRecipes.php`.

### 4.4. Xem chi tiết món

| | |
|---|---|
| Màn hình | `viewRecipeDetails.php?recipe_id=N` |
| FE | Trang **render phía server** (PHP in thẳng HTML), JS chỉ lo tương tác |
| BE | `session_start()`; `intval($_GET['recipe_id'])`; query chính: `recipes JOIN users LEFT JOIN categories LEFT JOIN ratings … GROUP BY` (lấy món + người đăng + danh mục + **AVG(rating)** + số lượt đánh giá); `SELECT * FROM ingredients WHERE recipe_id=…`; `SELECT c.*, u.name FROM comments c JOIN users u … ORDER BY created_at DESC`. Tách `recipe` theo dòng `\n` thành từng "bước"; nếu `video_link` là YouTube thì chuyển thành URL **embed** (hàm `getYouTubeEmbedUrl`) |
| DB | đọc **recipes, users, categories, ratings, ingredients, comments** |
| Kết quả | Ảnh, thông tin, nguyên liệu, các bước, video, đánh giá, bình luận. Nếu `user_id` của món = user đang đăng nhập (`$isOwner`) thì hiện nút **Chỉnh sửa / Xóa** |

**F12:** Ở trang này **không có XHR lúc tải** (do PHP in sẵn HTML). Muốn thấy dữ liệu: Network → click request đầu tiên `viewRecipeDetails.php?recipe_id=…` (Type: document) → tab Response là HTML đã chứa dữ liệu. Hoặc **Elements** để xem cấu trúc thẻ.

### 4.5. Co giãn nguyên liệu theo khẩu phần (chỉ FE, không gọi BE)

- Mỗi ô số lượng nguyên liệu (`.qty-input`) giữ số gốc trong `data-base-amount`.
- Sửa 1 ô → `handleIngredientChange()` tính `scaleFactor = số mới / số gốc` rồi nhân cho **tất cả** ô còn lại (làm tròn bởi `formatScaledAmount`).
- Nút preset 0.5x / 1x / 1.5x / 2x / 3x → `applyPresetScale()`; nút đặt lại → `resetIngredientScaling()`. Bấm vào nguyên liệu → gạch ngang đã chuẩn bị (`toggleIngredient`).
- **Không có request nào** (F12 → Network sẽ trống) – tính hoàn toàn bằng JavaScript. Ví dụ: sửa 500g thịt thành 1000g → mọi nguyên liệu khác tự ×2.

### 4.6. Đánh giá sao (1–5)

| | |
|---|---|
| Màn hình | Khối "Đánh giá & Bình luận" ở `viewRecipeDetails.php` – di chuột (`onStarHover`) và bấm (`onStarClick`) chọn sao, bấm nút gửi |
| FE | `submitRating()` (viewRecipeDetails.php:1239) – XHR `POST addRating.php`, `Content-Type: application/x-www-form-urlencoded`, body `recipe_id=N&rating=5` |
| BE `addRating.php` | Chưa đăng nhập → **401**. `rating` ngoài 1..5 hoặc `recipe_id<=0` → **400**. Tìm `SELECT id FROM ratings WHERE user_id=? AND recipe_id=?`: **có rồi → `UPDATE ratings SET rating=?`**; **chưa có → `INSERT INTO ratings`** (mỗi người chỉ 1 đánh giá/món, đánh giá lại thì ghi đè) |
| DB | ghi bảng **ratings** |
| Kết quả | Trả JSON `{"success":true,...}` → alert cảm ơn → `window.location.reload()` → điểm TB cập nhật |

**F12:** Network → Fetch/XHR → bấm gửi → `addRating.php` (POST, 200) → **Payload:** `recipe_id=…&rating=…` → **Response:** JSON thông báo. Thử khi chưa đăng nhập sẽ thấy **401**.

### 4.7. Bình luận

| | |
|---|---|
| FE | `submitComment()` (viewRecipeDetails.php:1261) – kiểm tra rỗng; XHR `POST addComment.php`, body `recipe_id=N&comment=<nội dung đã encodeURIComponent>` |
| BE `addComment.php` | Chưa đăng nhập → 401; `recipe_id<=0` hoặc nội dung rỗng → 400; `INSERT INTO comments (recipe_id,user_id,content,created_at) VALUES (?,?,?,NOW())` (prepared) |
| DB | ghi bảng **comments** |
| Kết quả | JSON `{"success":true}` → alert → reload → bình luận xuất hiện (PHP `SELECT … JOIN users ORDER BY created_at DESC`, dùng `htmlspecialchars` khi in để chống XSS) |

### 4.8. Đăng công thức mới

| | |
|---|---|
| Màn hình | `index4.php` (cần đăng nhập). 4 khối: ảnh (tải file **hoặc** dán URL), thông tin (tên, mô tả, danh mục, thời gian, tóm tắt, link video), nguyên liệu (thêm/xóa dòng), các bước (thêm/xóa, tự đánh số) |
| FE | JS: `addIngredientRow()`, `removeIngredientRow()`, `addStepRow()`, `updateStepNumbers()`, `previewImageFile()` (xem trước ảnh), `submitForm()` → `form.checkValidity()` rồi `form.submit()`. Form: `action="upload.php" method="post" enctype="multipart/form-data"` (bắt buộc multipart để gửi file) |
| Request | `POST upload.php`: `title, description, type, cooking_time, instructions, video_link, photo_url, ingredients[], quantities[], units[], steps[]` + file `image` |
| BE `upload.php` | 1) Chưa đăng nhập → về `index.php`; 2) validate `title`, `type`; 3) ghép `steps[]` thành text `"Bước 1: …\nBước 2: …"`; 4) `SELECT id FROM categories WHERE category_name=:type` → `category_id` (không có → mặc định 6 Vietnamese); 5) **xử lý ảnh**: chỉ nhận jpg/jpeg/png/webp, đặt tên `recipe_<time>_<rand>.<ext>`, `move_uploaded_file` vào thư mục gốc (+ copy sang các thư mục đồng bộ); không upload thì dùng URL; không có gì thì dùng ảnh Unsplash mặc định; 6) `video_link` không đúng URL → bỏ; 7) `INSERT INTO recipes (…) VALUES (…, NOW())` rồi `lastInsertId()`; 8) lặp `INSERT INTO ingredients (recipe_id, ingredient_name, quantity, unit)` cho từng nguyên liệu |
| DB | ghi **recipes** (1 dòng) + **ingredients** (n dòng); ảnh nằm trên đĩa, DB chỉ lưu tên file |
| Kết quả | `header("Location: viewRecipeDetails.php?recipe_id=<id mới>")` |

**F12:** Network → submit → `upload.php` (POST, **302**) → **Payload** thấy cả `ingredients[]`, `steps[]` và `image` (multipart). Sau đó là request `viewRecipeDetails.php?recipe_id=…` với id mới. Tab Application → không liên quan; để xem file ảnh: tên `recipe_…` sẽ xuất hiện trong `<img src>` ở tab Elements.

### 4.9. Kho món của tôi (quản lý)

| | |
|---|---|
| Màn hình | `viewUploadedRecipes.php` (cần đăng nhập) – lưới món do **chính mình** đăng, mỗi thẻ có 3 nút: Xem / Sửa / Xóa |
| BE | PHP in sẵn HTML từ truy vấn: `recipes r LEFT JOIN categories LEFT JOIN ingredients LEFT JOIN ratings WHERE r.user_id = <user đăng nhập> GROUP BY r.id` (đếm số nguyên liệu, sao TB) |
| Điều hướng | Xem → `viewRecipeDetails.php?recipe_id=`; Sửa → `edit.php?recipe_id=`; Xóa → `confirmDelete()` (hộp confirm) → `delete.php?recipe_id=` |

### 4.10. Sửa công thức

| | |
|---|---|
| Màn hình | `edit.php?recipe_id=N` (form giống đăng món, điền sẵn dữ liệu cũ) |
| BE – GET | `SELECT * FROM recipes WHERE id=N AND user_id=<mình>` → không phải chủ sở hữu thì báo "không có quyền"; `SELECT * FROM ingredients WHERE recipe_id=N` để điền form |
| BE – POST (cùng file) | Ghép lại các bước; tra `category_id`; nếu có ảnh mới thì upload, không thì giữ `current_photo`; `UPDATE recipes SET … WHERE id=N AND user_id=<mình>`; nếu có nguyên liệu: **`DELETE FROM ingredients WHERE recipe_id=N` rồi `INSERT` lại toàn bộ** |
| DB | cập nhật **recipes**, thay thế **ingredients** |
| Kết quả | alert "Cập nhật thành công" → `viewRecipeDetails.php?recipe_id=N` |

**F12:** Network → lưu → `edit.php?recipe_id=N` (POST) → Payload (multipart) → Response chứa `<script>alert('✓ Cập nhật…')`.

### 4.11. Xóa công thức

| | |
|---|---|
| FE | Nút Xóa (ở kho món hoặc trang chi tiết nếu là chủ) → `confirm()` → `window.location.href = 'delete.php?recipe_id=N'` (GET) |
| BE `delete.php` | Kiểm tra đăng nhập; `SELECT … WHERE id=N AND user_id=<mình>` (**chỉ chủ món mới xóa được**); xóa theo thứ tự: `ratings` → `comments` → `ingredients` → `recipes`; nếu ảnh là file local thì `unlink` xóa file |
| DB | xóa ở 4 bảng |
| Kết quả | alert "Đã xóa…" → `viewUploadedRecipes.php` |

**Vì sao xóa theo thứ tự đó?** Bảng `comments` và `ratings` có khóa ngoại tới `recipes` – phải xóa bảng con trước, bảng cha sau, nếu không MySQL báo lỗi khóa ngoại.

### 4.12. Hồ sơ đầu bếp

| | |
|---|---|
| Màn hình | `author.php?user_id=N` – tên, email, **tổng số món, điểm TB, tổng bình luận**, lưới món của người đó + ô tìm kiếm |
| BE | Thống kê (PHP, render sẵn): `users LEFT JOIN recipes LEFT JOIN ratings LEFT JOIN comments WHERE u.id=N GROUP BY u.id`. Danh sách món: JS gọi **`getRecipes.php?user_id=N&q=…`** (cùng API trang chủ, chỉ khác tham số) |

### 4.13. Công cụ xem log (phụ, dùng để demo luồng request)

- `logs.php?type=access&lines=15` đọc `C:\xampp\apache\logs\access.log`, trả JSON mỗi request (giờ, method, status, uri) kèm **câu lệnh curl tương ứng**. `?type=error` đọc error.log.
- `watch_logs.ps1`: PowerShell theo dõi log realtime, in ra lệnh curl.
- Dùng khi muốn chứng minh "mỗi thao tác trên giao diện sinh ra 1 request này ở server".

---

## 5. BẢNG TRA NHANH: MÀN HÌNH → FILE → BẢNG

| Thao tác trên màn hình | FE gọi | File BE | Bảng DB | Loại |
|---|---|---|---|---|
| Đăng ký | form POST | `register.php` | users (INSERT) | Form |
| Đăng nhập | form POST | `login.php` | users (SELECT) + session | Form |
| Mở trang chủ / lọc / tìm | XHR GET | `getRecipes.php` | recipes, users, ratings, comments (SELECT) | AJAX |
| Hiện tên ở sidebar | XHR GET | `getUserById.php` | users (SELECT) | AJAX |
| Xem chi tiết | link GET | `viewRecipeDetails.php` | recipes, users, categories, ratings, ingredients, comments | Server render |
| Gửi sao | XHR POST | `addRating.php` | ratings (INSERT/UPDATE) | AJAX |
| Gửi bình luận | XHR POST | `addComment.php` | comments (INSERT) | AJAX |
| Đăng món | form POST (multipart) | `upload.php` | recipes + ingredients (INSERT) + file ảnh | Form |
| Kho món của tôi | link GET | `viewUploadedRecipes.php` | recipes, categories, ingredients, ratings | Server render |
| Sửa món | form POST (multipart) | `edit.php` | recipes (UPDATE), ingredients (DELETE+INSERT) | Form |
| Xóa món | link GET | `delete.php` | ratings, comments, ingredients, recipes (DELETE) | Redirect |
| Hồ sơ đầu bếp | link GET + XHR | `author.php` + `getRecipes.php` | users, recipes, ratings, comments | Hỗn hợp |
| Đăng xuất | JS `confirm` → `window.location` về `index.php` | (không có file logout) | — | FE |

---

## 6. HƯỚNG DẪN F12 – LẦN TỪ MÀN HÌNH XUỐNG BE (nếu quên)

Mở F12 (hoặc chuột phải → Inspect; Mac: `Cmd+Option+I`).

### Cách 1 – Tab **Network** (quan trọng nhất)
1. Mở Network, tick **Preserve log** (giữ log khi trang chuyển).
2. Bấm **Clear** (🚫) cho sạch rồi thao tác trên giao diện (bấm nút).
3. Lọc loại request:
   - **Fetch/XHR** → các lệnh AJAX (`getRecipes.php`, `addRating.php`, `addComment.php`, `getUserById.php`).
   - **Doc** → chuyển trang và form submit (`login.php`, `upload.php`, `edit.php`, `delete.php`…).
4. Bấm vào 1 request, xem:
   - **Headers**: URL, Method (GET/POST), Status (200 OK, **302** = chuyển hướng, **401** chưa đăng nhập, **400** dữ liệu sai), `Set-Cookie`/`Cookie` (PHPSESSID).
   - **Payload**: dữ liệu FE gửi lên (name, email, rating…).
   - **Response / Preview**: dữ liệu BE trả (JSON hoặc HTML).
5. Câu trả lời mẫu: *"Em bấm nút này thì trình duyệt gửi request POST tới `addRating.php` kèm `recipe_id` và `rating`; file PHP kiểm tra session, rồi INSERT/UPDATE vào bảng `ratings`; trả JSON success."*

### Cách 2 – Tab **Elements**
- Click biểu tượng mũi tên (Ctrl+Shift+C) rồi trỏ vào phần tử trên màn hình → thấy thẻ HTML, `onclick="..."` (tên hàm JS), `action="..."` của form, `name="..."` của ô nhập → tên hàm / tên file BE sẽ được gọi.

### Cách 3 – Tab **Sources**
- Mở file `.php` đã render (thực chất là HTML+JS) → đặt **breakpoint** trong hàm như `fetchRecipes`, `submitRating` để chạy từng bước. Có thể `Ctrl+Shift+F` tìm theo từ khóa `.php`, `xhr.open`.

### Cách 4 – Tab **Application** → Cookies
- Thấy `PHPSESSID` sau khi đăng nhập. Xóa cookie đó rồi F5 → bị đá về trang đăng nhập → chứng minh session.

### Cách 5 – **Console**
- Chạy thử API ngay: `fetch('getRecipes.php?category=Dessert').then(r=>r.json()).then(console.log)`.

### Cách 6 – Mở thẳng API trên thanh địa chỉ
- `http://localhost/<thư-mục-project>/getRecipes.php?category=Vietnamese` → thấy JSON.
- `http://localhost/<thư-mục-project>/getRecipes.php?q=phở`
- `http://localhost/<thư-mục-project>/getRecipes.php?user_id=1`
- `http://localhost/<thư-mục-project>/getUserById.php` (cần đã đăng nhập cùng trình duyệt)

### Từ request tới **DB** (xem dữ liệu thật)
- Mở **phpMyAdmin** (`http://localhost/phpmyadmin`) → DB `recipe_sharing_platform` → bảng tương ứng → **Browse**. Thao tác trên web rồi F5 bảng để thấy dòng mới (ví dụ gửi sao → bảng `ratings` có dòng mới hoặc đổi giá trị). Đây là cách chứng minh FE → BE → DB rõ nhất.
- Hoặc `logs.php?type=access&lines=15` để xem các request gần nhất.

### Quy trình "mất phương hướng thì làm gì" (3 bước)
1. F12 → Network → thao tác nút → ghi tên **file .php** ở request.
2. Mở file .php đó trong editor → tìm câu `INSERT / UPDATE / SELECT / DELETE` → biết **bảng**.
3. Mở phpMyAdmin xem bảng đó thay đổi.

---

## 7. KỊCH BẢN DEMO GỢI Ý (5–7 phút)

1. **Đăng ký** tài khoản mới → mở phpMyAdmin `users` thấy dòng mới. *(register.php → users)*
2. **Đăng nhập** → F12 Network: `login.php` 302 + cookie. *(session)*
3. **Trang chủ**: lọc danh mục, gõ tìm "phở" → Network `getRecipes.php?...&q=`. *(AJAX, JSON, JOIN + AVG)*
4. **Chi tiết món**: tùy ý thay đổi khẩu phần ăn (bấm `+`/`-`, gõ số người bất kỳ, hoặc bấm mốc nhanh 1, 2, 4, 6, 8 người) hoặc sửa lượng nguyên liệu → các ô khác và số khẩu phần tự quy đổi theo tỷ lệ (JS thuần). Chọn sao + gửi → `addRating.php`; viết bình luận → `addComment.php`; reload thấy hiển thị.
5. **Đăng món mới**: tải ảnh, thêm nguyên liệu/bước → `upload.php` → tự chuyển sang trang chi tiết; phpMyAdmin thấy `recipes` + `ingredients`.
6. **Kho món của tôi** → **Sửa** (`edit.php`) → **Xóa** (`delete.php`, xóa cả 4 bảng).
7. **Hồ sơ đầu bếp**: bấm tên người đăng → `author.php`.

**Tài khoản mẫu trong DB:** `ngovancong2k6@gmail.com` / `1234` (Chef John), `gordon@example.com` / `123456`, `anna@example.com` / `123456`, và các tài khoản `…@cooktogether.vn` / `123456` (do `scratch/generate_all_recipes.php` sinh).

---

## 8. CÂU HỎI CÔ GIÁO CÓ THỂ HỎI & GỢI Ý TRẢ LỜI

| Câu hỏi | Trả lời |
|---|---|
| Hệ thống dùng công nghệ gì? | HTML/CSS/JS thuần ở FE, PHP thuần ở BE, MySQL, chạy trên XAMPP. Không dùng framework. |
| FE giao tiếp với BE thế nào? | Form POST (đăng nhập, đăng ký, đăng/sửa món) và AJAX XMLHttpRequest trả JSON (danh sách, tìm kiếm, đánh giá, bình luận). |
| Làm sao biết ai đang đăng nhập? | PHP Session: đăng nhập thì lưu `$_SESSION['user_id']`; mỗi file gọi `session_start()` để đọc. |
| Tìm kiếm hoạt động ra sao? | JS gọi `getRecipes.php?q=…` (debounce 300 ms); BE dùng `LIKE '%…%'` trên title, recipe, description. |
| Điểm sao trung bình tính ở đâu? | Trong SQL: `AVG(ratings.rating)` với `LEFT JOIN ratings … GROUP BY recipes.id`. |
| Một người đánh giá nhiều lần thì sao? | `addRating.php` kiểm tra đã có chưa: có thì UPDATE (ghi đè), chưa thì INSERT. |
| Ai được sửa/xóa món? | Chỉ chủ món: SQL có `WHERE id=? AND user_id=<session>`; nút chỉ hiện khi `$isOwner`. |
| Ảnh lưu ở đâu? | File lưu trên đĩa (thư mục project, tên `recipe_<time>_<rand>.<ext>`), DB chỉ lưu tên file (hoặc URL nếu dán link). Chỉ nhận jpg/jpeg/png/webp. |
| Vì sao xóa món phải xóa nhiều bảng? | Do khóa ngoại: xóa bảng con (ratings, comments, ingredients) rồi mới xóa recipes. |
| Co giãn nguyên liệu có gọi server không? | Không, tính bằng JS: hệ số = số mới / số gốc (`data-base-amount`). |
| Chống SQL Injection chưa? | Phần lớn dùng prepared statement (`login`, `addComment`, `addRating`, `upload`, `getUserById`) và `intval` / `real_escape_string`. **Còn điểm yếu** – xem mục 9. |

---

## 9. HẠN CHẾ ĐÃ BIẾT (nên nhận trước nếu cô hỏi "em cải tiến gì thêm?")

Trả lời trung thực, đây cũng là hướng phát triển:

1. **Mật khẩu lưu plaintext** → nên dùng `password_hash()` / `password_verify()`.
2. **`register.php`** nối trực tiếp email vào câu `SELECT` (`WHERE email='$email'`) → nguy cơ SQL Injection; nên dùng prepared statement. **`edit.php`** và **`delete.php`** dùng `real_escape_string`/`intval` + query nối chuỗi (an toàn hơn nhưng nên chuyển prepared).
3. Chưa có **trang/chức năng đăng xuất thật**: nút đăng xuất chỉ chuyển về `index.php`, chưa `session_destroy()`.
4. `index3.php`, `getRecipes.php` cho xem **không cần đăng nhập** (chủ ý để khách xem); chỉ các thao tác ghi mới yêu cầu đăng nhập.
5. Chưa có kiểm tra **CSRF**; thông tin DB (root, không mật khẩu) viết cứng trong từng file, lặp lại nhiều lần → nên gom vào một file `config.php`/`db.php`.
6. `ratings` không có ràng buộc UNIQUE(user_id, recipe_id) – đang dựa vào code; bảng `recipe_ingredients` có trong DB nhưng không dùng.
7. `recipes.type` và `recipes.category_id` lưu trùng thông tin danh mục.
8. `logs.php` đọc đường dẫn log Windows cố định (`C:\xampp\...`) và công khai → chỉ dùng khi demo, không nên đưa lên production.
9. Nhiều bản sao thư mục trùng code → nên dọn lại.

---

## 10. CÁCH CHẠY LẠI NẾU MÁY CÔ/MÁY BẠN MẤT CẤU HÌNH

1. Cài XAMPP, bật **Apache** + **MySQL**.
2. Chép thư mục project vào `C:\xampp\htdocs\recipe-sharing-platform` (đường dẫn mặc định log đang dùng tên này).
3. Mở `http://localhost/phpmyadmin` → tạo DB `recipe_sharing_platform` → **Import** file `recipe_sharing_platform.sql`.
4. Mở `http://localhost/recipe-sharing-platform/index.php` → đăng nhập bằng tài khoản mẫu ở mục 7.
5. Lỗi hay gặp: MySQL chạy cổng khác 3306 (code đang kết nối cổng **3306**, user `root`, mật khẩu rỗng); ảnh không hiện → kiểm tra file `recipe_*.jpg` có nằm cùng thư mục với trang PHP.
