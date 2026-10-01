-- Script tạo dữ liệu mẫu cho Recipe Sharing Platform
USE recipe_sharing_platform;

-- 1. Thêm người dùng mẫu (mật khẩu 123456)
INSERT INTO users (id, name, email, password) VALUES
(1, 'Chef John', 'john@example.com', '123456'),
(2, 'Gordon Ramsay', 'gordon@example.com', '123456'),
(3, 'Master Chef Anna', 'anna@example.com', '123456')
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- 2. Thêm các công thức nấu ăn phong phú cho cả 5 danh mục
INSERT INTO recipes (id, title, cooking_time, recipe, type, category_id, photo, video_link, description, instructions, user_id, uploaded_at) VALUES
-- North Indian (category_id = 1)
(1, 'Butter Chicken (Murgh Makhani)', 45, 
'1. Ướp gà với sữa chua, bột ớt kashmiri, tỏi, gừng và gia vị garam masala trong 30 phút.
2. Áp chảo gà cho đến khi xém cạnh thơm nức.
3. Nấu sốt cà chua với bơ, hạt điều xay nhuyễn và kem tươi thơm béo.
4. Cho gà vào sốt nấu riu riu 10 phút, rắc lá fenugreek khô và thưởng thức cùng bánh Naan.', 
'NorthIndian', 1, 
'https://images.unsplash.com/photo-1603894584373-5ac82b2ae398?w=800', 
'https://www.youtube.com/watch?v=a03U45jFxOI', 
'Món gà sốt bơ cà chua béo ngậy trứ danh của vùng Bắc Ấn Độ.', 
'Ướp gà -> Áp chảo -> Nấu sốt cà chua bơ kem -> Đun nhỏ lửa 10 phút', 
1, NOW()),

(2, 'Paneer Tikka Masala', 30, 
'1. Cắt phô mai paneer, ớt chuông và hành tây thành miếng vuông vừa ăn.
2. Trộn phô mai với hỗn hợp sốt sữa chua tẩm ướp cay nồng.
3. Xiên que và nướng trong lò hoặc chảo nướng cho đến khi các cạnh xém vàng.
4. Dọn kèm sốt bạc hà xanh mát và hành tây ngâm chanh.', 
'NorthIndian', 1, 
'https://images.unsplash.com/photo-1567188040759-fb8a883dc6d8?w=800', 
'https://www.youtube.com/watch?v=xW5pP0O_9E0', 
'Phô mai Paneer nướng gia vị thơm lừng, món ăn chay hoàng gia Bắc Ấn.', 
'Tẩm ướp phô mai và rau củ -> Xiên que nướng vàng thơm -> Ăn kèm sốt bạc hà', 
2, NOW()),

-- South Indian (category_id = 2)
(3, 'Crispy Masala Dosa', 40, 
'1. Ngâm gạo và đậu urad dal, xay mịn và lên men qua đêm để bột nở chua nhẹ.
2. Làm nhân khoai tây xào mù tạt vàng, lá cà ri, nghệ và hành tây giòn ngọt.
3. Tráng bột thật mỏng trên chảo gang nóng có thoa bơ ghee cho đến khi bánh giòn tan.
4. Cho nhân khoai tây vào giữa, cuộn lại và thưởng thức nóng hổi cùng tương dừa (coconut chutney) và súp sambar.', 
'SouthIndian', 2, 
'https://images.unsplash.com/photo-1668236543090-82eba5ee5976?w=800', 
'https://www.youtube.com/watch?v=CCab5uqazCA', 
'Bánh xèo Nam Ấn siêu giòn cuộn nhân khoai tây gia vị đậm đà.', 
'Tráng bột giòn trên chảo bơ ghee -> Thêm nhân khoai tây -> Cuộn tròn ăn kèm súp sambar', 
3, NOW()),

(4, 'Soft Idli with Sambar', 35, 
'1. Chuẩn bị bột gạo và đậu lên men xốp mịn.
2. Thoa dầu vào khuôn idli, múc bột vào khuôn và hấp cách thủy 10-12 phút.
3. Nấu nước dùng Sambar từ đậu lăng toor dal, me chua, rau củ và bột gia vị sambar.
4. Bánh chín phồng trắng xốp, chấm cùng sambar nóng hổi thanh mát.', 
'SouthIndian', 2, 
'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=800', 
'https://www.youtube.com/watch?v=VwQ9n8k_uX0', 
'Bánh gạo hấp xốp mềm thanh đạm cho bữa sáng chuẩn vị Nam Ấn.', 
'Hấp bánh idli mềm xốp -> Nấu súp đậu lăng sambar -> Thưởng thức nóng', 
1, NOW()),

-- Chinese (category_id = 3)
(5, 'Kung Pao Chicken (Gà Cung Bảo)', 25, 
'1. Thịt ức gà cắt hạt lựu, ướp với xì dầu, rượu hoa tiêu và tinh bột bắp.
2. Pha nước sốt Kung Pao: nước tương, giấm đen, đường, dầu mè và chút ớt bột.
3. Đun nóng dầu ăn, phi thơm ớt khô và tiêu Tứ Xuyên cho dậy mùi thơm cay nồng.
4. Cho thịt gà vào xào săn trên lửa lớn, thêm hành paro, ớt chuông và nước sốt.
5. Cuối cùng cho đậu phộng rang giòn đảo đều rồi tắt bếp.', 
'Chinese', 3, 
'https://images.unsplash.com/photo-1525755662778-989d0524087e?w=800', 
'https://www.youtube.com/watch?v=nZ4B_s0q_4E', 
'Món gà xào chua ngọt cay nồng Tứ Xuyên kết hợp cùng đậu phộng giòn rụm.', 
'Ướp gà -> Phi thơm tiêu Tứ Xuyên và ớt -> Xào lửa lớn cùng sốt -> Trộn đậu phộng rang', 
2, NOW()),

(6, 'Steamed Shrimp Shumai (Xíu Mại Tôm)', 30, 
'1. Tôm bóc vỏ băm nhuyễn một nửa, một nửa cắt khúc để giữ độ giòn.
2. Trộn tôm với thịt heo xay, nấm hương, dầu hào, tiêu trắng và dầu mè thơm lừng.
3. Gói nhân vào lá hoành thánh mỏng, vuốt mép tạo hình phễu xíu mại truyền thống.
4. Đặt vào xửng tre hấp cách thủy trong 8-10 phút đến khi vỏ mờ trong và nhân chín mọng.', 
'Chinese', 3, 
'https://images.unsplash.com/photo-1541696432-82c6da8ce7bf?w=800', 
'https://www.youtube.com/watch?v=2r1pQ20Wz70', 
'Dim sum xíu mại tôm thịt hấp trong xửng tre thơm nức, ngọt mọng nước.', 
'Trộn nhân tôm thịt nấm hương -> Gói lá hoành thánh -> Hấp trong xửng tre 10 phút', 
3, NOW()),

-- Dessert (category_id = 4)
(7, 'Royal Gulab Jamun', 40, 
'1. Trộn sữa bột (khoya), một ít bột mì và sữa tươi để nhồi thành khối bột dẻo mịn.
2. Vo tròn thành những viên bột nhỏ xinh không tì vết.
3. Chiên từ từ trong dầu nóng hoặc bơ ghee ở lửa nhỏ cho đến khi chuyển màu vàng nâu cánh gián.
4. Nấu siro đường hương thảo quả (cardamom), nghệ tây (saffron) và chút nước hoa hồng.
5. Thả những viên bánh nóng vào siro ngâm trong 2 tiếng cho ngấm ngọt lịm tan chảy.', 
'Dessert', 4, 
'https://images.unsplash.com/photo-1601050690597-df0568f70950?w=800', 
'https://www.youtube.com/watch?v=W_QhL9v8K1s', 
'Món bánh sữa ngâm siro thảo quả và hoa hồng ngọt ngào bậc nhất Ấn Độ.', 
'Vo viên bột sữa mịn -> Chiên lửa nhỏ đến vàng nâu -> Ngâm siro hoa hồng nghệ tây', 
1, NOW()),

(8, 'Creamy Mango Kulfi', 20, 
'1. Đun sữa tươi nguyên kem và sữa đặc trên lửa nhỏ, khuấy đều cho cô đặc lại còn một nửa.
2. Xay nhuyễn thịt xoài cát chín mọng ngọt thơm.
3. Trộn xoài xay cùng hỗn hợp sữa đã để nguội, thêm bột bạch đậu khấu và hạt dẻ cười đập vụn.
4. Rót vào khuôn làm kem que hoặc ly gốm truyền thống và để đông lạnh 6-8 tiếng.', 
'Dessert', 4, 
'https://images.unsplash.com/photo-1587314168485-3236d6710814?w=800', 
'https://www.youtube.com/watch?v=T_7xQJ3_mK0', 
'Kem xoài truyền thống Ấn Độ sánh mịn, thơm lừng hương thảo mộc và hạt dẻ.', 
'Đun cô đặc sữa tươi -> Trộn xoài xay và bạch đậu khấu -> Đổ khuôn cấp đông qua đêm', 
2, NOW()),

-- Drinks (category_id = 5)
(9, 'Refreshing Mango Lassi', 10, 
'1. Cho thịt xoài chín tươi (hoặc xoài xay đông lạnh) vào máy xay sinh tố.
2. Thêm sữa chua Hy Lạp không đường, một ít sữa tươi, đường hoặc mật ong tùy khẩu vị.
3. Cho thêm một nhúm bột thảo quả (cardamom) và vài viên đá lạnh.
4. Xay ở tốc độ cao trong 1-2 phút cho đến khi hỗn hợp nhuyễn mịn, sủi bọt kem hấp dẫn.
5. Rót ra ly, rắc thêm vài sợi nghệ tây và hạnh nhân thái lát lên trên.', 
'Drinks', 5, 
'https://images.unsplash.com/photo-1546173159-315724a31696?w=800', 
'https://www.youtube.com/watch?v=5Qj4O_tW_yM', 
'Thức uống sữa chua xoài kinh điển mát lạnh, sánh đặc và thơm béo.', 
'Cho xoài, sữa chua, sữa tươi và đá vào cối -> Xay mịn nhuyễn -> Trang trí hạt hạnh nhân', 
3, NOW()),

(10, 'Authentic Masala Chai', 15, 
'1. Giã dập gừng tươi, thảo quả xanh, đinh hương, hoa hồi và thanh quế.
2. Đun sôi nước cùng các loại gia vị thơm trong 3-4 phút để tinh dầu tiết ra.
3. Cho lá trà đen hảo hạng vào đun tiếp 2 phút cho nước trà đậm màu.
4. Rót sữa tươi nguyên kem vào, thêm đường thốt nốt đun đến khi trà sôi bùng lên.
5. Lọc bỏ bã gia vị qua rây và thưởng thức ly trà nóng thơm ngát nồng nàn.', 
'Drinks', 5, 
'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=800', 
'https://www.youtube.com/watch?v=1rXq3Z0z_2w', 
'Trà sữa gia vị Ấn Độ thảo mộc thơm lừng, ấm áp và bồi bổ sức khỏe.', 
'Đun sôi thảo mộc và gừng -> Thêm trà đen -> Nấu cùng sữa tươi -> Lọc bã uống nóng', 
1, NOW())
ON DUPLICATE KEY UPDATE title=VALUES(title), recipe=VALUES(recipe), photo=VALUES(photo);

-- 3. Thêm nguyên liệu (Ingredients) cho từng món
INSERT INTO ingredients (recipe_id, ingredient_name, quantity, unit) VALUES
-- Butter Chicken
(1, 'Thịt đùi gà rút xương', 500, 'gram'),
(1, 'Sữa chua không đường', 150, 'ml'),
(1, 'Bơ lạt (Butter)', 50, 'gram'),
(1, 'Kem tươi (Heavy Cream)', 100, 'ml'),
(1, 'Cà chua xay nhuyễn', 300, 'gram'),
(1, 'Bột gia vị Garam Masala', 2, 'thìa cà phê'),

-- Paneer Tikka
(2, 'Phô mai Paneer', 400, 'gram'),
(2, 'Ớt chuông các màu', 2, 'quả'),
(2, 'Hành tây đỏ', 1, 'củ'),
(2, 'Bột ớt Kashmiri cay thơm', 1.5, 'thìa cà phê'),
(2, 'Sốt bạc hà chấm kèm', 50, 'ml'),

-- Masala Dosa
(3, 'Bột gạo & đậu Urad Dal lên men', 500, 'ml'),
(3, 'Khoai tây luộc chín bóp nát', 3, 'củ'),
(3, 'Bơ lỏng Ghee', 30, 'ml'),
(3, 'Lá cà ri tươi & hạt mù tạt', 10, 'gram'),
(3, 'Súp đậu lăng Sambar', 200, 'ml'),

-- Soft Idli
(4, 'Bột làm bánh Idli lên men', 400, 'ml'),
(4, 'Đậu lăng Toor Dal', 100, 'gram'),
(4, 'Me chua lấy nước cốt', 20, 'ml'),
(4, 'Rau củ hỗn hợp (cà rốt, bí ngô)', 150, 'gram'),

-- Kung Pao Chicken
(5, 'Thịt ức gà tươi', 400, 'gram'),
(5, 'Đậu phộng rang nguyên hạt', 60, 'gram'),
(5, 'Ớt khô Tứ Xuyên', 10, 'trái'),
(5, 'Tiêu Tứ Xuyên thơm cay', 1, 'thìa cà phê'),
(5, 'Nước tương đen & giấm Tiêu Thơm', 40, 'ml'),

-- Shrimp Shumai
(6, 'Tôm tươi lột vỏ', 300, 'gram'),
(6, 'Thịt heo xay có mỡ', 150, 'gram'),
(6, 'Nấm hương ngâm nở băm nhỏ', 4, 'tai'),
(6, 'Vỏ hoành thánh vàng mỏng', 20, 'lá'),
(6, 'Dầu mè nguyên chất', 1, 'thìa canh'),

-- Gulab Jamun
(7, 'Sữa bột Khoya nguyên chất', 200, 'gram'),
(7, 'Bột mì đa dụng', 40, 'gram'),
(7, 'Đường phèn hoặc đường cát trắng', 300, 'gram'),
(7, 'Nước hoa hồng hữu cơ', 15, 'ml'),
(7, 'Bột quả bạch đậu khấu', 1, 'thìa cà phê'),

-- Mango Kulfi
(8, 'Sữa tươi nguyên kem', 500, 'ml'),
(8, 'Thịt xoài chín xay nhuyễn', 250, 'gram'),
(8, 'Sữa đặc có đường', 100, 'ml'),
(8, 'Hạt dẻ cười Pistachio đập vụn', 30, 'gram'),

-- Mango Lassi
(9, 'Thịt xoài chín ngọt', 2, 'quả'),
(9, 'Sữa chua không đường', 200, 'ml'),
(9, 'Sữa tươi thanh trùng', 100, 'ml'),
(9, 'Mật ong nguyên chất', 2, 'thìa canh'),
(9, 'Đá viên tinh khiết', 1, 'ly'),

-- Masala Chai
(10, 'Trà đen Assam thượng hạng', 15, 'gram'),
(10, 'Sữa tươi có đường', 250, 'ml'),
(10, 'Gừng tươi đập dập', 15, 'gram'),
(10, 'Thảo quả, quế, đinh hương', 10, 'gram');

-- 4. Thêm đánh giá sao (Ratings từ 4.5 đến 5.0 sao)
INSERT INTO ratings (user_id, recipe_id, rating) VALUES
(1, 1, 5.0),
(2, 1, 4.8),
(3, 1, 5.0),
(1, 2, 4.7),
(2, 2, 4.5),
(1, 3, 5.0),
(2, 3, 4.9),
(3, 4, 4.6),
(1, 5, 4.8),
(2, 5, 5.0),
(3, 5, 4.7),
(1, 6, 5.0),
(2, 6, 4.9),
(1, 7, 5.0),
(2, 7, 4.8),
(3, 8, 4.9),
(1, 9, 5.0),
(2, 9, 4.8),
(3, 9, 5.0),
(1, 10, 4.9),
(2, 10, 5.0);

-- 5. Thêm bình luận tương tác (Comments)
INSERT INTO comments (recipe_id, user_id, content, created_at) VALUES
(1, 2, 'Công thức sốt bơ cà chua này quá chuẩn vị nhà hàng 5 sao! Ăn kèm bánh Naan tỏi hết sẩy.', NOW() - INTERVAL 2 DAY),
(1, 3, 'Gia đình mình ai cũng thích món này, hương vị béo ngậy rất dễ chịu.', NOW() - INTERVAL 1 DAY),
(3, 1, 'Bánh giòn rụm đúng chuẩn Nam Ấn, phần nhân khoai tây thơm lá cà ri tuyệt đỉnh.', NOW() - INTERVAL 3 DAY),
(5, 1, 'Thịt gà mềm thơm, sốt chua ngọt cay cay rất đưa cơm!', NOW() - INTERVAL 12 HOUR),
(6, 2, 'Dim sum nhân tôm cắn vào giòn sần sật, hấp dẫn vô cùng.', NOW() - INTERVAL 6 HOUR),
(7, 3, 'Bánh Gulab Jamun ngọt ngào ngậy sữa, ăn ấm với siro hoa hồng quá đỉnh.', NOW() - INTERVAL 1 DAY),
(9, 1, 'Mùa hè mà có ly Mango Lassi mát lạnh này uống thì sảng khoái không gì bằng!', NOW() - INTERVAL 4 HOUR),
(10, 2, 'Hương vị trà Masala Chai ấm nồng gừng quế, rất thích hợp uống vào sáng sớm.', NOW() - INTERVAL 2 HOUR);
