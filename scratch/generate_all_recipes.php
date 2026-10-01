<?php
// Script tạo 10 người dùng mới, 100 món Việt và 50 món quốc tế
$dsn = 'mysql:host=localhost;port=3306;dbname=recipe_sharing_platform;charset=utf8mb4';
$pdo = new PDO($dsn, 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
]);

echo "Bắt đầu khởi tạo dữ liệu mở rộng...\n";

// 1. Thêm 10 người dùng Việt Nam mới (ID 4 -> 13)
$users = [
    [4, 'Bếp Cô Ba Sài Gòn', 'coba@cooktogether.vn', '123456'],
    [5, 'Chef Nguyễn Thanh Hải', 'chefhai@cooktogether.vn', '123456'],
    [6, 'Nguyễn Thị Lan (Lan Bếp)', 'lannguyen@cooktogether.vn', '123456'],
    [7, 'Trần Văn Hùng (Ẩm Thực Phố)', 'hungtran@cooktogether.vn', '123456'],
    [8, 'Hoàng Yến (Món Ngon Mẹ Nấu)', 'hoangyen@cooktogether.vn', '123456'],
    [9, 'Lê Quang Minh', 'quangminh@cooktogether.vn', '123456'],
    [10, 'Phạm Mai Anh', 'maianh@cooktogether.vn', '123456'],
    [11, 'Đặng Thu Hà (Hà Chef)', 'thuha@cooktogether.vn', '123456'],
    [12, 'Vũ Đức Thắng', 'ducthang@cooktogether.vn', '123456'],
    [13, 'Bùi Minh Trí (Trí Foodie)', 'minhtri@cooktogether.vn', '123456'],
];

$userStmt = $pdo->prepare("INSERT INTO users (id, name, email, password) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE name=VALUES(name), email=VALUES(email), password=VALUES(password)");
foreach ($users as $u) {
    $userStmt->execute($u);
}
echo "✓ Đã thêm/cập nhật 10 người dùng mới thành công.\n";

// Ensure Vietnamese category exists
$pdo->exec("INSERT INTO categories (id, category_name) VALUES (6, 'Vietnamese') ON DUPLICATE KEY UPDATE category_name='Vietnamese'");

// Curated high quality food photos
$vietnamesePhotos = [
    'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?w=800', // Pho
    'https://images.unsplash.com/photo-1574484284002-952d92456975?w=800', // Rice / meat
    'https://images.unsplash.com/photo-1559847844-5315695dadae?w=800', // Spring rolls
    'https://images.unsplash.com/photo-1509722747041-616f39b57569?w=800', // Banh mi
    'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=800', // Noodles
    'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=800', // Fried rice
    'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=800', // Salad bowl
    'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=800', // Soup bowl
    'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=800', // Asian dish
    'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800', // Healthy salad
    'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=800', // Asian gourmet
    'https://images.unsplash.com/photo-1555126634-323283e090fa?w=800', // Grilled noodles
    'https://images.unsplash.com/photo-1547592180-85f173990554?w=800', // Stew
    'https://images.unsplash.com/photo-1541832676-9b763b0239ab?w=800', // Seafood
    'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=800', // Rolls
    'https://images.unsplash.com/photo-1626777552726-4a6b54c97e46?w=800', // Curry stew
    'https://images.unsplash.com/photo-1579871494447-9811cf80d66c?w=800', // Dumplings
    'https://images.unsplash.com/photo-1596797038530-2c107229654b?w=800', // Claypot
];

// Danh sách 100 món ăn Việt Nam truyền thống & hiện đại
$vietnameseDishes = [
    // 1-10
    ["Phở Bò Tái Lăn Hà Nội", 60, "Món phở truyền thống với thịt bò thăn xào lăn nhanh trên lửa lớn thơm nức mùi tỏi và gừng, nước dùng hầm xương bò trong veo ngọt thanh.", [["Bánh phở tươi", 500, "gram"], ["Thịt bò thăn", 300, "gram"], ["Xương ống bò", 1, "kg"], ["Hành lá, rau mùi", 100, "gram"], ["Gừng, hoa hồi, quế", 30, "gram"]]],
    ["Bún Chả Hà Nội Nướng Than Hoa", 45, "Chả miếng và chả viên tẩm ướp đậm đà nướng xém cạnh trên than hoa, ăn kèm nước chấm chua ngọt đu đủ xanh và bún tươi.", [["Thịt ba chỉ", 400, "gram"], ["Thịt nạc vai xay", 300, "gram"], ["Bún tươi", 500, "gram"], ["Đu đủ xanh, cà rốt", 200, "gram"], ["Nước mắm ngon, tỏi, ớt", 50, "ml"]]],
    ["Bún Bò Huế Cố Đô Chuẩn Vị", 90, "Nước dùng bún bò thơm nồng mùi sả, ruốc Huế đậm đà hòa quyện cùng bắp bò mềm, chả cua và giò heo béo ngậy.", [["Bắp bò hoa", 500, "gram"], ["Giò heo", 500, "gram"], ["Mắm ruốc Huế", 3, "muỗng canh"], ["Sả cây đập dập", 6, "cây"], ["Bún sợi to", 500, "gram"], ["Huyết bò, chả cua", 200, "gram"]]],
    ["Bánh Mì Kẹp Thịt Xá Xíu & Pate", 20, "Bánh mì vỏ giòn rụm, nhân pate gan béo ngậy, thịt xá xíu thơm mềm, đồ chua, dưa leo và rau ngò tươi mát sốt bơ béo.", [["Bánh mì giòn", 4, "ổ"], ["Thịt xá xíu", 200, "gram"], ["Pate gan heo", 100, "gram"], ["Bơ trứng gà", 50, "gram"], ["Dưa leo, đồ chua", 100, "gram"]]],
    ["Cơm Tấm Sườn Bì Chả Sài Gòn", 50, "Miếng sườn cốt lết nướng mỡ hành óng ả, chả trứng hấp mềm mịn, bì heo dai giòn ăn cùng hạt cơm tấm dẻo ngọt và nước mắm kẹo.", [["Gạo tấm thơm", 400, "gram"], ["Sườn cốt lết heo", 4, "miếng"], ["Bì heo trộn thính", 150, "gram"], ["Trứng gà làm chả", 3, "quả"], ["Nước mắm tỏi ớt kẹo", 60, "ml"]]],
    ["Gỏi Cuốn Tôm Thịt Chấm Tương Đậu", 25, "Gỏi cuốn thanh mát với tôm tươi đỏ au, thịt ba chỉ luộc, bún tươi và rau thơm, chấm sốt tương đen bơ đậu phộng béo bùi.", [["Bánh tráng dẻo", 20, "lá"], ["Tôm sú tươi", 300, "gram"], ["Thịt ba chỉ luộc", 200, "gram"], ["Bún tươi", 200, "gram"], ["Tương đen bơ đậu phộng", 100, "gram"]]],
    ["Bánh Xèo Miền Tây Giòn Rụm", 40, "Bánh xèo vàng ươm bột nghệ và nước cốt dừa béo ngậy, nhân tôm thịt giá đỗ đầy đặn cuộn cùng rau rừng và nước mắm chua ngọt.", [["Bột bánh xèo pha sẵn", 400, "gram"], ["Nước cốt dừa", 200, "ml"], ["Tôm bạc đất", 250, "gram"], ["Thịt ba chỉ", 200, "gram"], ["Giá đỗ, cải xanh, rau thơm", 300, "gram"]]],
    ["Bún Riêu Cua Đồng Mộc Mạc", 50, "Gạch cua đồng béo ngậy đóng tảng thơm lừng, nước dùng chua thanh từ cà chua và giấm bỗng, ăn kèm đậu hũ chiên và rau muống chẻ.", [["Cua đồng giã nhuyễn", 500, "gram"], ["Cà chua chín mọng", 4, "quả"], ["Đậu hũ chiên vàng", 3, "miếng"], ["Giấm bỗng nếp", 50, "ml"], ["Bún tươi", 500, "gram"], ["Huyết heo, chả lụa", 150, "gram"]]],
    ["Cá Lóc Kho Tộ Đậm Đà Miền Tây", 40, "Cá lóc đồng kho keo trong nồi đất với nước dừa tươi, tiêu sọ cay nồng và mỡ hành béo ngậy ăn cùng cơm trắng nóng hổi.", [["Cá lóc đồng làm sạch", 600, "gram"], ["Thịt mỡ heo thái hạt lựu", 100, "gram"], ["Nước dừa tươi", 150, "ml"], ["Nước mắm cốt nhĩ", 4, "muỗng canh"], ["Tiêu sọ đập dập, ớt hiểm", 15, "gram"]]],
    ["Thịt Kho Tàu Hột Vịt Nước Dừa", 60, "Thịt ba chỉ cắt vuông to kho cùng trứng vịt trong nước dừa xiêm cho đến khi thịt mềm rục, mỡ trong veo thơm lừng.", [["Thịt ba rọi rút sườn", 700, "gram"], ["Trứng vịt luộc bóc vỏ", 8, "quả"], ["Nước dừa xiêm ngọt", 500, "ml"], ["Nước mắm, tỏi, hành tím", 50, "ml"]]],

    // 11-20
    ["Canh Chua Cá Lóc Nam Bộ", 30, "Canh chua thanh mát nấu từ me chín, cá lóc tươi, dọc mùng, đậu bắp, thơm và cà chua, rắc ngò gai rau om thơm phức.", [["Cá lóc cắt khúc", 400, "gram"], ["Dọc mùng (bạc hà)", 2, "cây"], ["Đậu bắp, cà chua, thơm", 200, "gram"], ["Nước cốt me chín", 50, "ml"], ["Ngò gai, rau om", 30, "gram"]]],
    ["Bò Kho Sốt Vang Ăn Bánh Mì", 75, "Thịt nạm bò và gân bò hầm mềm sốt rượu vang đỏ, quế hồi và cà rốt sánh mịn, mùi thơm quyến rũ cho ngày se lạnh.", [["Nạm bò nhiều gân", 600, "gram"], ["Cà rốt tỉa hoa", 2, "củ"], ["Rượu vang đỏ", 100, "ml"], ["Sốt cà chua paste", 50, "gram"], ["Bột quế, hoa hồi, sả", 20, "gram"]]],
    ["Chả Cá Lã Vọng Hà Thành", 35, "Cá lăng thái miếng dày ướp nghệ mẻ nướng vàng rồi xào trực tiếp trên chảo ngập thì là hành hoa, ăn cùng bún và mắm tôm.", [["Cá lăng phi lê", 500, "gram"], ["Nghệ tươi, cơm mẻ", 40, "gram"], ["Thì là, hành hoa", 200, "gram"], ["Đậu phộng rang", 50, "gram"], ["Mắm tôm đánh sủi bọt", 50, "ml"]]],
    ["Mì Quảng Tôm Thịt Trứng Cút", 40, "Sợi mì Quảng vàng óng chan nước nhưn tôm thịt đậm đà sóng sánh, rắc đậu phộng rang giòn và bẻ bánh tráng nướng ăn kèm.", [["Sợi mì Quảng", 500, "gram"], ["Tôm đất tươi", 200, "gram"], ["Thịt ba chỉ heo", 200, "gram"], ["Trứng cút luộc", 10, "quả"], ["Bánh tráng mè nướng", 2, "cái"], ["Rau sống bắp chuối", 200, "gram"]]],
    ["Nem Rán Hà Nội Giòn Tan", 45, "Nem rán nhân thịt băm, tôm tươi, miến rong, mộc nhĩ nấm hương và trứng gà cuộn bánh đa nem giòn rụm chấm nước mắm ớt tỏi.", [["Thịt nạc vai băm", 300, "gram"], ["Tôm tươi băm nhỏ", 150, "gram"], ["Miến dong, mộc nhĩ, nấm hương", 100, "gram"], ["Bánh đa nem gói giòn", 30, "lá"], ["Trứng gà", 2, "quả"]]],
    ["Gà Hấp Lá Chanh Thơm Lừng", 40, "Gà ta thả vườn da vàng ươm hấp cách thủy cùng lá chanh bánh tẻ, giữ trọn vị ngọt tự nhiên chấm muối tiêu chanh ớt.", [["Gà ta nguyên con", 1.4, "kg"], ["Lá chanh tươi", 20, "lá"], ["Muối hột, hạt tiêu", 30, "gram"], ["Gừng, sả tươi", 50, "gram"], ["Ớt hiểm đỏ", 2, "trái"]]],
    ["Vịt Nấu Chao Miền Tây Béo Bùi", 55, "Thịt vịt xiêm ướp chao đỏ chao trắng nấu cùng khoai môn dẻo bùi trong nước dừa, ăn lẩu kèm bún tươi và rau muống đồng.", [["Thịt vịt xiêm", 800, "gram"], ["Chao đỏ & chao trắng", 100, "gram"], ["Khoai môn dẻo", 300, "gram"], ["Nước dừa tươi", 400, "ml"], ["Rau muống, bún tươi", 400, "gram"]]],
    ["Sườn Non Xào Chua Ngọt Óng Ánh", 35, "Sườn non rán vàng xốc đều với sốt cà chua, giấm thơm, đường và ớt chuông tạo lớp sốt sánh đặc óng ả bắt mắt.", [["Sườn non heo", 500, "gram"], ["Cà chua chín xay", 2, "quả"], ["Giấm gạo thơm, đường cát", 40, "ml"], ["Tỏi băm, ớt chuông", 50, "gram"], ["Hành lá", 20, "gram"]]],
    ["Bún Đậu Mắm Tôm Thập Cẩm", 30, "Mẹt bún lá cắt miếng ăn kèm đậu hũ mơ chiên giòn, chả cốm chiên, thịt luộc chân giò và chén mắm tôm đánh chanh sủi bọt ngút ngàn.", [["Bún lá ép bánh", 500, "gram"], ["Đậu hũ non chiên giòn", 4, "miếng"], ["Chả cốm Hà Nội", 200, "gram"], ["Thịt bắp giò luộc", 300, "gram"], ["Mắm tôm Thanh Hóa", 50, "ml"], ["Tía tô, kinh giới", 100, "gram"]]],
    ["Bánh Cuốn Nóng Tráng Tay", 35, "Bánh cuốn vỏ mỏng tang mướt mịn nhân thịt băm mộc nhĩ rắc hành phi thơm giòn, ăn kèm chả lụa và nước mắm chấm ấm nồng.", [["Bột gạo pha bột năng", 300, "gram"], ["Thịt heo băm", 200, "gram"], ["Mộc nhĩ nấm mèo", 50, "gram"], ["Hành phi vàng giòn", 50, "gram"], ["Chả lụa lợn", 200, "gram"]]],

    // 21-30
    ["Bánh Bèo Chén Xứ Huế", 30, "Bánh bèo đúc trong từng chén sứ nhỏ, rắc tôm chấy cháy vàng, tóp mỡ giòn rụm và mỡ hành thơm phức chan mắm ớt cay xé.", [["Bột gạo tẻ", 200, "gram"], ["Tôm tươi làm chấy", 200, "gram"], ["Tóp mỡ heo chiên", 50, "gram"], ["Hành lá phi mỡ", 30, "gram"], ["Nước mắm chua ngọt Huế", 50, "ml"]]],
    ["Bánh Bột Lọc Tôm Thịt Gói Lá Chuối", 45, "Bánh bột lọc dẻo trong veo nhìn thấu con tôm đỏ au và miếng thịt mỡ kho đậm vị, gói lá chuối thơm nức mùi quê.", [["Bột năng thượng hạng", 300, "gram"], ["Tôm thẻ rim", 200, "gram"], ["Thịt ba rọi rim mặn ngọt", 150, "gram"], ["Lá chuối tươi hơ lửa", 20, "miếng"], ["Nước mắm ruốc ớt cay", 40, "ml"]]],
    ["Bánh Canh Cua Đồng Bến Tre", 50, "Sợi bánh canh bột gạo dai mềm nấu cùng nước súp cua biển ngọt lịm, gạch cua xào thơm, tôm tươi và nấm rơm.", [["Sợi bánh canh tươi", 500, "gram"], ["Thịt cua biển gỡ sẵn", 200, "gram"], ["Tôm sú tươi", 200, "gram"], ["Nấm rơm búp", 150, "gram"], ["Trứng cút, huyết heo", 150, "gram"]]],
    ["Bò Lúc Lắc Khoai Tây Chiên", 25, "Thịt bò phi lê cắt vuông quân cờ xào lửa lớn với bơ tỏi, ớt chuông và hành tây giòn ngọt ăn kèm đĩa khoai tây chiên vàng giòn.", [["Thịt thăn bò mềm", 400, "gram"], ["Ớt chuông 3 màu", 150, "gram"], ["Hành tây trắng", 1, "củ"], ["Bơ lạt thơm", 30, "gram"], ["Khoai tây chiên", 200, "gram"]]],
    ["Gà Nướng Muối Ớt Da Giòn", 50, "Gà nguyên con ướp sốt muối ớt cay nồng nướng trên lửa than đến khi da gà giòn rụm vàng bóng, thịt bên trong ngọt mềm mọng nước.", [["Gà ta thả vườn", 1.3, "kg"], ["Muối hột Tây Ninh", 2, "thìa canh"], ["Ớt hiểm đỏ băm", 4, "trái"], ["Mật ong nguyên chất", 2, "thìa canh"], ["Lá chúc hoặc lá chanh", 10, "lá"]]],
    ["Cháo Lòng Miền Tây Thơm Ngon", 45, "Cháo hạt gạo rang thơm nấu cùng lòng heo luộc, dồi trường chiên giòn, huyết mềm và rau thơm tiêu đen ấm bụng.", [["Gạo tẻ rang vàng", 150, "gram"], ["Lòng non, dồi trường, gan", 400, "gram"], ["Dạ dày heo luộc", 150, "gram"], ["Huyết heo luộc", 100, "gram"], ["Gừng, tiêu, hành ngò", 30, "gram"]]],
    ["Lẩu Thái Hải Sản Kiểu Việt", 40, "Nồi lẩu chua cay đậm đà hương lá chanh thái, sả ớt hòa quyện cùng tôm, mực, nghêu tươi sống và các loại nấm tươi mát.", [["Tôm sú biển", 300, "gram"], ["Mực ống tươi", 300, "gram"], ["Nghêu sống sạch cát", 500, "gram"], ["Gói cốt lẩu Thái cay", 1, "gói"], ["Nấm kim châm, rau muống", 300, "gram"]]],
    ["Ếch Xào Lăn Nước Cốt Dừa", 35, "Thịt ếch đồng săn chắc xào bột cà ri, sả ớt và nước cốt dừa sánh béo, rắc đậu phộng rang giòn ăn cùng bánh mì nóng.", [["Thịt đùi ếch đồng", 500, "gram"], ["Bột cà ri thơm", 1, "gói"], ["Nước cốt dừa đậm đặc", 150, "ml"], ["Sả, ớt băm, hành tây", 50, "gram"], ["Đậu phộng rang thơm", 30, "gram"]]],
    ["Cá Điêu Hồng Hấp Xì Dầu Nấm Đông Cô", 35, "Cá điêu hồng nguyên con hấp cách thủy cùng gừng thái chỉ, nấm đông cô, hành hoa và sốt xì dầu thơm ngào ngạt ngọt thanh.", [["Cá điêu hồng tươi sống", 1, "kg"], ["Nấm đông cô ngâm nở", 50, "gram"], ["Gừng tươi thái sợi", 30, "gram"], ["Xì dầu thượng hạng, dầu hào", 50, "ml"], ["Hành lá, ớt sừng", 40, "gram"]]],
    ["Mực Nhồi Thịt Sốt Cà Chua", 40, "Mực ống tươi giòn nhồi nhân thịt heo mộc nhĩ miến dong, chiên sơ rồi rim trong sốt cà chua sánh mịn đậm đà.", [["Mực ống tươi loại vừa", 500, "gram"], ["Thịt nạc vai băm", 200, "gram"], ["Mộc nhĩ, miến ngâm mềm", 50, "gram"], ["Cà chua chín mọng", 3, "quả"], ["Hành hoa, tiêu xay", 20, "gram"]]],

    // 31-40
    ["Cơm Chiên Dương Châu Hải Sản", 25, "Hạt cơm vàng tơi xốp chiên cùng tôm khô, lạp xưởng, đậu Hà Lan, cà rốt và trứng gà béo ngậy thơm lừng.", [["Cơm nguội tơi xốp", 4, "chén"], ["Lạp xưởng Mai Quế Lộ", 2, "cây"], ["Tôm tươi lột vỏ", 150, "gram"], ["Đậu Hà Lan, cà rốt", 100, "gram"], ["Trứng gà", 2, "quả"]]],
    ["Gỏi Ngó Sen Tôm Thịt Chua Ngọt", 25, "Ngó sen trắng giòn ngâm chua ngọt trộn cùng tôm sú đỏ au, thịt tai heo giòn sần sật, rau răm và đậu phộng bùi bùi.", [["Ngó sen tươi chẻ đôi", 300, "gram"], ["Tôm sú luộc bóc vỏ", 200, "gram"], ["Tai heo luộc thái mỏng", 150, "gram"], ["Cà rốt thái chỉ, rau răm", 80, "gram"], ["Nước mắm chua ngọt trộn gỏi", 60, "ml"]]],
    ["Chả Giò Rế Tôm Cua Giòn Rụm", 35, "Chả giò bọc bánh tráng rế chiên vàng giòn rụm tan trong miệng, nhân thịt cua biển, tôm tươi và củ sắn ngọt mát.", [["Bánh tráng rế chiên", 25, "lá"], ["Thịt cua biển tươi", 150, "gram"], ["Tôm băm nhuyễn", 150, "gram"], ["Củ sắn, khoai môn thái sợi", 150, "gram"], ["Trứng gà, nấm mèo", 50, "gram"]]],
    ["Bún Cá Rô Đồng Rau Cải", 45, "Thịt cá rô đồng chiên vàng giòn và cá hấp gỡ xương, chan nước dùng ngọt thanh nấu từ xương cá và rau cải xanh giòn ngọt.", [["Cá rô đồng tươi", 600, "gram"], ["Rau cải xanh cắt khúc", 200, "gram"], ["Bún sợi nhỏ", 500, "gram"], ["Thì là, gừng tươi", 30, "gram"], ["Gia vị mắm muối cốt", 30, "ml"]]],
    ["Lẩu Mắm Miền Tây Đậm Tình Đất Mũi", 60, "Nồi lẩu mắm cá linh cá sặc thơm ngào ngạt, nhúng cùng tôm càng, mực tươi, thịt ba rọi và hơn 10 loại rau đồng nội.", [["Mắm cá linh, mắm cá sặc", 200, "gram"], ["Tôm càng sông", 300, "gram"], ["Thịt ba chỉ thái mỏng", 250, "gram"], ["Cà tím cắt khúc", 2, "trái"], ["Rau đắng, bông súng, điên điển", 300, "gram"]]],
    ["Heo Quay Kho Trứng Cút Đậm Vị", 35, "Thịt heo quay da giòn rụm chặt miếng vuông kho cùng trứng cút trong nước dừa và tiêu sọ thơm lừng đưa cơm.", [["Thịt heo quay giòn bì", 400, "gram"], ["Trứng cút luộc bóc vỏ", 15, "quả"], ["Nước dừa tươi ngọt", 150, "ml"], ["Nước mắm ngon, tỏi ớt", 40, "ml"]]],
    ["Bò Tơ Củ Chi Cuộn Bánh Tráng", 40, "Thịt bò tơ mềm ngọt hấp gừng sả thái mỏng, cuộn cùng bánh tráng Trảng Bàng, rau rừng Tây Ninh và chấm mắm nêm đậm đà.", [["Bắp bò tơ non mềm", 500, "gram"], ["Gừng tươi thái lát, sả", 50, "gram"], ["Bánh tráng phơi sương", 20, "lá"], ["Rau rừng Tây Ninh tổng hợp", 300, "gram"], ["Mắm nêm pha thơm tỏi ớt", 70, "ml"]]],
    ["Bánh Canh Trảng Bàng Giò Heo", 50, "Bánh canh sợi bột gạo hấp mềm dẻo, nước dùng ninh từ xương giò heo ngọt trong thanh tao, ăn cùng thịt giò nạc mỡ đan xen.", [["Bánh canh bột gạo Trảng Bàng", 500, "gram"], ["Khoanh giò heo rút xương", 600, "gram"], ["Hành ngò, tiêu xay thơm", 30, "gram"], ["Giá đỗ, chanh ớt", 100, "gram"]]],
    ["Ốc Bươu Nhồi Thịt Hấp Sả", 35, "Vỏ ốc bươu nhồi nhân thịt heo giòn sần sật nấm hương và sả băm, hấp cách thủy thơm nức mũi chấm nước mắm gừng cay.", [["Ốc bươu to sạch ruột", 1, "kg"], ["Thịt giò sống nạc", 200, "gram"], ["Sả cây cắt khúc lót ốc", 10, "cây"], ["Mộc nhĩ, tiêu sọ", 30, "gram"], ["Nước mắm gừng lá chanh", 50, "ml"]]],
    ["Cá Kèo Kho Rau Răm Nồi Đất", 30, "Cá kèo tươi sống ướp mắm tiêu kho keo cùng rau răm cay the trong nồi đất, ăn cùng cơm nóng và đĩa rau luộc đồng quê.", [["Cá kèo tươi bơi", 400, "gram"], ["Rau răm tươi thái nhỏ", 50, "gram"], ["Nước mắm cốt, nước màu", 40, "ml"], ["Ớt hiểm đỏ, tiêu sọ", 15, "gram"]]],

    // 41-50
    ["Cơm Gà Hải Nam Kiểu Sài Gòn", 50, "Cơm nấu nước luộc gà dẻo thơm ngậy béo, gà ta luộc da giòn vàng ươm ăn cùng sốt gừng hành và nước tương đặc.", [["Gà ta thả vườn", 1.2, "kg"], ["Gạo tẻ thơm", 400, "gram"], ["Gừng tươi giã nhuyễn", 50, "gram"], ["Hành lá phi mỡ gà", 50, "gram"], ["Nước chấm gừng tỏi ớt", 60, "ml"]]],
    ["Bún Thịt Nướng Chả Giò Miền Nam", 35, "Thịt nạc dăm ướp sả mè nướng thơm lừng trên than hoa, chả giò giòn rụm, mỡ hành, đậu phộng và nước mắm chua ngọt.", [["Thịt nạc dăm heo", 400, "gram"], ["Sả băm, hạt mè trắng", 40, "gram"], ["Bún tươi sợi mềm", 500, "gram"], ["Chả giò chiên giòn", 4, "cuốn"], ["Mỡ hành, đậu phộng rang", 50, "gram"]]],
    ["Lẩu Gà Lá É Phú Yên", 45, "Nước lẩu gà nấu nấm ngọt thanh hòa cùng vị the nồng the cay đặc trưng của lá é tươi, thịt gà giòn sần sật mọng nước.", [["Gà ta đồi chặt miếng", 1, "kg"], ["Lá é tươi sạch", 300, "gram"], ["Nấm bào ngư xám", 200, "gram"], ["Măng le chua ngọt", 150, "gram"], ["Ớt xiêm xanh giã dập", 15, "trái"]]],
    ["Cơm Cháy Chà Bông Mỡ Hành", 30, "Cơm cháy chiên vàng giòn rụm, phủ đều lớp mỡ hành xanh mướt, chà bông heo tơi xốp và nước sốt mắm ớt cay ngọt.", [["Cơm ép đáy nồi sấy khô", 300, "gram"], ["Chà bông heo tơi xốp", 100, "gram"], ["Hành lá làm mỡ hành", 50, "gram"], ["Nước sốt mắm đường ớt", 40, "ml"]]],
    ["Bánh Khọt Vũng Tàu Tôm Tươi", 35, "Bánh khọt chiên khuôn gang vàng giòn, con tôm sú ngọt lịm nằm giữa bánh rắc bột tôm cháy và mỡ hành thơm nức.", [["Bột gạo bánh khọt", 300, "gram"], ["Tôm sú tươi lột vỏ", 250, "gram"], ["Nước cốt dừa", 100, "ml"], ["Bột tôm cháy khô", 30, "gram"], ["Rau xà lách, cải xanh, đu đủ bào", 250, "gram"]]],
    ["Bò Cuộn Lá Lốt Nướng Than", 30, "Thịt bò băm ướp gia vị đậm đà cuộn trong lá lốt bánh tẻ, nướng trên than hoa thơm nức mũi, rắc đậu phộng mỡ hành.", [["Thịt bò nạc xay", 350, "gram"], ["Mỡ heo thái nhỏ", 80, "gram"], ["Lá lốt tươi rửa sạch", 30, "lá"], ["Sả băm, tỏi băm", 30, "gram"], ["Bánh tráng, bún, mắm nêm", 200, "gram"]]],
    ["Gà Xào Sả Ớt Đậm Đà", 25, "Thịt gà ta chặt miếng vừa ăn xào săn cùng sả ớt băm thơm lừng, nước kho sánh vàng óng ả cay thơm nồng nàn.", [["Thịt gà chặt miếng", 500, "gram"], ["Sả băm nhỏ", 4, "cây"], ["Ớt sừng, ớt hiểm", 3, "trái"], ["Nước mắm, bột nghệ", 30, "ml"], ["Hành tím, tỏi băm", 20, "gram"]]],
    ["Tôm Rim Thịt Ba Rọi Mặn Ngọt", 30, "Tôm đất đồng nguyên vỏ rim cùng thịt ba rọi cháy cạnh cho đến khi đường mắm keo lại đỏ au bóng bẩy giòn béo.", [["Tôm đất tươi", 300, "gram"], ["Thịt ba rọi thái lát mỏng", 200, "gram"], ["Nước mắm nhĩ, đường vàng", 40, "ml"], ["Hành tím, tỏi, tiêu sọ", 25, "gram"]]],
    ["Canh Cua Rau Đay Mướp Hương", 25, "Bát canh cua đồng ngọt mát mùa hè nấu cùng rau đay mướt mịn, mướp hương thơm lừng ăn kèm vài quả cà pháo muối giòn.", [["Cua đồng giã lọc nước", 400, "gram"], ["Rau đay, mồng tơi", 200, "gram"], ["Mướp hương thái vát", 1, "quả"], ["Cà pháo muối giòn", 50, "gram"]]],
    ["Cá Chẽm Chiên Xù Sốt Chua Ngọt", 35, "Cá chẽm phi lê tẩm bột chiên xù vàng ươm giòn rụm, rưới sốt dứa cà chua hành tây chua ngọt đẹp mắt.", [["Phi lê cá chẽm tươi", 500, "gram"], ["Bột chiên xù giòn", 150, "gram"], ["Dứa thơm, cà chua, hành tây", 200, "gram"], ["Sốt chua ngọt chuẩn vị", 60, "ml"]]],

    // 51-100 (Thêm 50 món Việt tiếp theo để đủ 100 món)
    ["Bún Mắm Miền Tây Hải Sản", 45, "Bún mắm nước dùng đậm đà hương cá sặc cá linh, topping tôm sú, mực, heo quay, chả ớt nhồi thịt và cà tím.", [["Bún tươi", 500, "gram"], ["Mắm cá sặc linh", 150, "gram"], ["Tôm, mực tươi", 300, "gram"], ["Heo quay", 150, "gram"], ["Cà tím, rau muống chẻ", 200, "gram"]]],
    ["Bánh Đa Cua Hải Phòng", 40, "Bánh đa đỏ sợi mềm dai nấu nước riêu cua đồng béo ngậy, chả lá lốt thơm lừng, tôm tươi và rau muống giòn.", [["Bánh đa đỏ Hải Phòng", 400, "gram"], ["Cua đồng tươi", 400, "gram"], ["Chả lá lốt", 150, "gram"], ["Chả cá Hải Phòng", 150, "gram"], ["Rau muống, hành phi", 100, "gram"]]],
    ["Bánh Tằm Bì Nước Cốt Dừa", 30, "Sợi bánh tằm mềm mướt ăn cùng bì heo trộn thính, thịt nạc luộc, rau thơm dưa leo và chan nước cốt dừa béo ngậy.", [["Bánh tằm tươi", 400, "gram"], ["Bì heo trộn thính", 150, "gram"], ["Thịt heo nạc luộc", 150, "gram"], ["Nước cốt dừa đậm đặc", 150, "ml"], ["Nước mắm chua ngọt", 50, "ml"]]],
    ["Gà Không Lối Thoát (Gà Bọc Xôi Chiên)", 60, "Gà ta nguyên con tẩm ướp thảo mộc bọc kín trong lớp xôi nếp nương chiên vàng giòn rụm bên ngoài, gà ngọt mềm bên trong.", [["Gà ta thả vườn", 1.2, "kg"], ["Gạo nếp nương dẻo", 500, "gram"], ["Hạt sen, nấm hương", 100, "gram"], ["Gia vị tẩm ướp nướng", 40, "gram"]]],
    ["Lẩu Cá Kèo Lá Giang Chua Thanh", 40, "Cá kèo tươi roi rói nhúng vào nồi nước lẩu lá giang chua thanh dịu nhẹ, ăn cùng bún tươi và hoa chuối bào giòn mát.", [["Cá kèo tươi sống", 500, "gram"], ["Lá giang tươi vò nát", 150, "gram"], ["Bún tươi sợi nhỏ", 400, "gram"], ["Hoa chuối bào, ngò om", 150, "gram"]]],
    ["Cơm Hến Xứ Huế Cay Nồng", 35, "Cơm nguội trộn hến xào đậm đà, tóp mỡ giòn, đậu phộng rang, bạc hà, xoài băm chan nước luộc hến nóng hổi cay nồng.", [["Thịt hến xào", 300, "gram"], ["Nước luộc hến", 500, "ml"], ["Cơm nguội dẻo", 3, "chén"], ["Bắp chuối, rau thơm, ớt xào", 150, "gram"], ["Mắm ruốc Huế pha cay", 40, "ml"]]],
    ["Măng Nhồi Thịt Hấp Cách Thủy", 35, "Từng khúc măng tươi luộc sạch nhồi thịt heo mọc nấm hương béo ngọt hấp chín tới thơm lừng.", [["Măng tre tươi củ", 500, "gram"], ["Thịt nạc dăm xay", 250, "gram"], ["Mộc nhĩ, nấm hương", 40, "gram"], ["Hành lá, tiêu sọ", 20, "gram"]]],
    ["Chả Mực Giã Tay Hạ Long", 40, "Mực mai nang dày giã tay truyền thống tạo độ dai giòn sần sật tự nhiên, rán vàng ruộm thơm nức mũi.", [["Mực mai nang tươi", 700, "gram"], ["Mỡ phần heo", 100, "gram"], ["Thì là, hành hoa", 30, "gram"], ["Tiêu hạt, nước mắm", 20, "gram"]]],
    ["Gỏi Gà Măng Cụt Chua Ngọt", 30, "Măng cụt giòn ngọt chua nhẹ trộn gỏi cùng thịt gà ta xé phay, rau răm, hành tây và sốt mắm chua ngọt đặc biệt.", [["Măng cụt chua giòn", 1, "kg"], ["Thịt gà ta luộc xé", 400, "gram"], ["Rau răm, hành tây", 80, "gram"], ["Đậu phộng rang giòn", 50, "gram"], ["Nước sốt gỏi chua ngọt", 60, "ml"]]],
    ["Vịt Quay Bắc Kinh Kiểu Chợ Lớn", 60, "Vịt quay da nâu cánh gián bóng bẩy giòn rụm, thịt thơm mềm ướp ngũ vị hương và mật ong chuẩn vị ẩm thực Chợ Lớn.", [["Vịt cỏ béo tròn", 1.8, "kg"], ["Mật ong, giấm đỏ", 50, "ml"], ["Ngũ vị hương, hoa hồi, thảo quả", 20, "gram"], ["Bánh mì ăn kèm", 4, "ổ"]]],
    ["Bò Nhúng Dấm Cuộn Bánh Tráng", 35, "Bắp bò hoa thái mỏng nhúng trong nồi nước dừa dấm chua ngọt thanh tao, cuộn rau sống bánh tráng chấm mắm nêm cay.", [["Bắp bò hoa thái mỏng", 500, "gram"], ["Nước dừa tươi pha dấm gạo", 500, "ml"], ["Sả đập dập, hành tây", 50, "gram"], ["Bánh tráng, chuối chát, khế", 200, "gram"], ["Mắm nêm pha thơm", 60, "ml"]]],
    ["Cá Tai Tượng Chiên Xù Đứng Miền Tây", 40, "Cá tai tượng nguyên con chiên xù giòn rụm đứng thẳng trên đĩa, thịt ngọt trắng tinh cuốn bánh tráng rau rừng.", [["Cá tai tượng tươi sống", 1.2, "kg"], ["Bánh tráng phơi sương", 20, "lá"], ["Rau sống các loại", 300, "gram"], ["Nước mắm me chua ngọt", 70, "ml"]]],
    ["Bánh Đúc Nóng Thịt Băm Hà Nội", 30, "Bát bánh đúc dẻo quánh ấm nóng múc ngập thịt băm mộc nhĩ xào thơm, chan nước mắm chua ngọt ấm bụng ngày đông.", [["Bột gạo tẻ pha bột năng", 250, "gram"], ["Thịt heo băm nhuyễn", 200, "gram"], ["Mộc nhĩ nấm hương", 40, "gram"], ["Nước mắm chua ngọt nấu ấm", 150, "ml"], ["Rau mùi ta, hành phi", 30, "gram"]]],
    ["Gỏi Bò Bóp Thấu Chua Cay", 25, "Thịt bắp bò tái chanh trộn cùng khế chua, chuối chát, hành tây giòn và đậu phộng rang thơm nức mũi.", [["Bắp bò thái mỏng", 300, "gram"], ["Khế chua, chuối xanh", 2, "quả"], ["Hành tây, ớt sừng", 100, "gram"], ["Đậu phộng rang, mè rang", 40, "gram"], ["Nước cốt chanh, nước mắm", 40, "ml"]]],
    ["Lẩu Bò Nhúng Topping Đầy Đặn", 50, "Nồi lẩu bò nước dùng xương hầm quế hồi thơm lừng, nhúng gầu bò, bắp bò, đuôi bò hầm nhừ và rau cải thảo tươi non.", [["Gầu bò, bắp bò", 500, "gram"], ["Đuôi bò hầm mềm", 300, "gram"], ["Đậu hũ non", 2, "miếng"], ["Nấm kim châm, cải thảo", 300, "gram"], ["Gói gia vị lẩu bò thơm", 1, "gói"]]],
    ["Súp Cua Bắp Non Trứng Cút", 25, "Bát súp cua sánh đặc trong veo đầy ắp thịt cua biển, bắp ngọt non, nấm tuyết và trứng cút rưới dầu mè tiêu thơm.", [["Thịt cua biển", 200, "gram"], ["Bắp ngọt tách hạt", 100, "gram"], ["Nấm tuyết ngâm mềm", 50, "gram"], ["Bột năng tạo độ sánh", 40, "gram"], ["Trứng cút, trứng gà", 8, "quả"]]],
    ["Chả Đùm Nam Bộ Hấp Bánh Phồng Tôm", 40, "Chả đùm nhân thịt bò, thịt heo, gan và mỡ chài bọc kín hấp chín, ăn cùng bánh phồng tôm giòn rụm béo bùi.", [["Thịt bò xay", 200, "gram"], ["Thịt heo nạc dăm", 200, "gram"], ["Gan heo băm", 100, "gram"], ["Mỡ chài bọc ngoài", 150, "gram"], ["Bánh phồng tôm chiên", 1, "gói"]]],
    ["Bánh Canh Ghẹ Nguyên Con", 45, "Con ghẹ biển tươi xanh luộc ngọt lịm nằm trên bát bánh canh bột lọc nước dùng sánh đỏ gạch cua thơm nức.", [["Ghẹ biển tươi sống", 2, "con"], ["Sợi bánh canh bột lọc", 400, "gram"], ["Nấm rơm, huyết heo", 150, "gram"], ["Trứng cút", 6, "quả"], ["Hành ngò, ớt xay", 20, "gram"]]],
    ["Cánh Gà Chiên Nước Mắm Kẹo", 25, "Cánh gà chiên giòn tan lắc đều trong sốt nước mắm tỏi ớt kẹo lại óng ả đậm đà cực kỳ hao cơm.", [["Cánh gà tươi ngon", 500, "gram"], ["Nước mắm ngon loại 1", 3, "muỗng canh"], ["Đường cát vàng", 2, "muỗng canh"], ["Tỏi ớt băm nhuyễn", 30, "gram"]]],
    ["Gỏi Cuốn Nem Nướng Nha Trang", 30, "Nem nướng thơm lừng cuộn bánh tráng chiên giòn rụm, xoài xanh, dưa leo và chấm sốt tương nếp thịt băm béo bùi.", [["Nem nướng thịt heo", 400, "gram"], ["Bánh tráng chiên giòn", 10, "cuốn"], ["Xoài xanh, dưa leo", 150, "gram"], ["Bánh tráng cuốn mềm", 20, "lá"], ["Nước sốt tương nếp gan heo", 100, "ml"]]],
    ["Canh Sườn Nấu Sấu Hà Nội", 30, "Sườn non hầm mềm nấu cùng quả sấu xanh chua dịu thanh mát, cà chua và hành hoa giúp giải nhiệt ngày hè.", [["Sườn non heo", 400, "gram"], ["Quả sấu xanh", 5, "quả"], ["Cà chua chín", 2, "quả"], ["Hành lá, mùi tàu", 30, "gram"]]],
    ["Cá Thu Sốt Cà Chua Đậm Đà", 30, "Khúc cá thu biển rán vàng ươm rim trong sốt cà chua tươi sánh mịn thơm ngậy mùi hành ngò tiêu sọ.", [["Cá thu tươi cắt khúc", 400, "gram"], ["Cà chua chín", 3, "quả"], ["Hành tím, tỏi băm", 20, "gram"], ["Nước mắm, tiêu sọ", 30, "ml"]]],
    ["Lẩu Đầu Cá Hồi Nấu Măng Chua", 35, "Đầu cá hồi béo ngậy nấu măng chua cay nồng ấm bụng, nước lẩu ngọt thơm không hề tanh ăn cùng bún tươi.", [["Đầu cá hồi tươi", 600, "gram"], ["Măng chua muối", 250, "gram"], ["Cà chua, thơm ngọt", 150, "gram"], ["Ngò gai, ngò om, ớt", 30, "gram"], ["Bún tươi", 400, "gram"]]],
    ["Bò Nướng Mỡ Chài Thơm Nức", 35, "Thịt bò tẩm ướp tiêu sả cuộn mỡ chài mỏng nướng trên than hoa mỡ chảy xèo xèo thơm ngào ngạt.", [["Thịt bò xay mềm", 400, "gram"], ["Mỡ chài tươi sạch", 150, "gram"], ["Sả băm, tỏi tiêu", 30, "gram"], ["Đậu phộng, mỡ hành", 40, "gram"]]],
    ["Mì Xào Giòn Hải Sản Thập Cẩm", 25, "Vắt mì chiên giòn phồng xốp, rưới đĩa sốt tôm mực xào rau củ nóng hổi làm sợi mì mềm dai vừa ăn thơm béo.", [["Mì trứng tươi chiên giòn", 3, "vắt"], ["Tôm tươi, mực ống", 250, "gram"], ["Cải ngọt, cà rốt, nấm đông cô", 200, "gram"], ["Sốt dầu hào xào sệt", 50, "ml"]]],
    ["Canh Khổ Qua Nhồi Thịt", 35, "Quả khổ qua xanh mướt nhồi thịt heo mộc nhĩ hầm nước dùng xương ngọt thanh mát lành thanh lọc cơ thể.", [["Khổ qua (mướp đắng)", 3, "quả"], ["Thịt nạc vai băm", 250, "gram"], ["Mộc nhĩ ngâm nở", 30, "gram"], ["Hành hoa, tiêu trắng", 20, "gram"]]],
    ["Gà Luộc Nước Dừa Chấm Muối Ớt", 40, "Gà ta luộc trong nước dừa xiêm giữ trọn độ ngọt thơm mềm mọng, chấm muối ớt đỏ cay xé lưỡi.", [["Gà ta nguyên con", 1.3, "kg"], ["Nước dừa xiêm ngọt", 600, "ml"], ["Gừng tươi, hành tím", 40, "gram"], ["Muối ớt lá chanh", 30, "gram"]]],
    ["Bánh Mì Chảo Bò Bít Tết Trứng Ốp La", 20, "Chảo gang nóng xèo xèo miếng bít tết bò mềm thơm, trứng ốp la lòng đào, pate béo ngậy và xíu mại chan sốt bơ.", [["Thịt bò mềm phi lê", 200, "gram"], ["Trứng gà", 2, "quả"], ["Pate gan heo", 50, "gram"], ["Xúc xích, xíu mại", 100, "gram"], ["Bánh mì giòn", 2, "ổ"]]],
    ["Chả Giò Cá Trích Phú Quốc", 35, "Cá trích tươi phi lê gói bánh tráng cùng dừa nạo và rau sống chiên giòn rụm chấm nước mắm đậu phộng đậm vị đảo ngọc.", [["Cá trích tươi phi lê", 300, "gram"], ["Dừa nạo sợi", 80, "gram"], ["Bánh tráng cuốn giòn", 20, "lá"], ["Nước mắm đậu phộng Phú Quốc", 60, "ml"]]],
    ["Cơm Chiên Cá Mặn Gà Xé", 25, "Cơm rang hạt tơi giòn cùng khô cá mặn thơm nồng, thịt ức gà xé sợi và hành hoa xào béo ngậy.", [["Cơm nguội", 3, "chén"], ["Khô cá mặn lọc thịt", 50, "gram"], ["Ức gà luộc xé sợi", 150, "gram"], ["Trứng gà, hành hoa", 2, "quả"]]],
    ["Nộm Bò Khô Đu Đủ Phố Cổ", 20, "Đu đủ xanh bào sợi giòn sần sật trộn bò khô cay, gan sấy, rau kinh giới và chan nước mắm giấm đường chua ngọt.", [["Đu đủ xanh bào sợi", 300, "gram"], ["Thịt bò khô xé sợi", 100, "gram"], ["Gan sấy thơm", 50, "gram"], ["Rau kinh giới, đậu phộng", 40, "gram"], ["Nước mắm giấm chua ngọt", 60, "ml"]]],
    ["Bún Bò Xào Nam Bộ Thanh Mát", 25, "Bún tươi trộn cùng thịt bò xào sả hành tây thơm lừng, rau sống xắt nhỏ, đậu phộng rang và nước mắm chua ngọt.", [["Thịt bò thăn", 300, "gram"], ["Bún tươi", 400, "gram"], ["Sả băm, hành tây", 50, "gram"], ["Đậu phộng rang, hành phi", 40, "gram"], ["Rau xà lách, giá đỗ", 150, "gram"]]],
    ["Cá Diêu Hồng Chiên Xù Cuốn Bánh Tráng", 35, "Cá diêu hồng chiên nguyên con vàng ươm giòn rụm cuốn bánh tráng rau sống chấm mắm nêm đậm đà thơm ngát.", [["Cá diêu hồng nguyên con", 1, "kg"], ["Bánh tráng cuốn", 20, "lá"], ["Rau sống tổng hợp", 300, "gram"], ["Mắm nêm pha tỏi ớt", 60, "ml"]]],
    ["Chả Ram Tôm Đất Bình Định", 30, "Từng cuốn chả ram nhỏ xinh nhân con tôm đất ngọt lịm chiên giòn rụm nhai rôm rốp cuốn rau sống chấm tương đậu.", [["Tôm đất tươi", 300, "gram"], ["Thịt mỡ heo thái nhỏ", 100, "gram"], ["Bánh tráng phơi sương Bình Định", 30, "lá"], ["Rau cải non, xà lách", 200, "gram"]]],
    ["Thịt Ba Chỉ Luộc Chấm Mắm Tôm Chua", 25, "Thịt ba chỉ luộc chín tới thái mỏng cuốn bánh tráng, khế chua, chuối xanh chấm mắm tôm chua xứ Huế cay nồng.", [["Thịt ba chỉ heo ngon", 500, "gram"], ["Mắm tôm chua Huế", 100, "gram"], ["Khế chua, chuối xanh", 2, "quả"], ["Bánh tráng dẻo", 15, "lá"]]],
    ["Bún Mọc Sườn Heo Dọc Mùng", 40, "Bát bún nước trong ngọt từ sườn non ninh kỹ, từng viên mọc giò sống nấm hương giòn sần sật và dọc mùng xanh mướt.", [["Sườn non heo", 400, "gram"], ["Giò sống làm mọc", 200, "gram"], ["Mộc nhĩ, nấm hương", 30, "gram"], ["Dọc mùng tước vỏ", 2, "cây"], ["Bún tươi", 400, "gram"]]],
    ["Cút Lộn Xào Me Chua Ngọt Cay Cay", 20, "Trứng cút lộn luộc bóc vỏ xào cùng sốt me sánh đặc chua ngọt, rắc rau răm và đậu phộng rang giòn ăn vặt cực đỉnh.", [["Trứng cút lộn", 20, "quả"], ["Nước cốt me chín", 60, "ml"], ["Đường vàng, ớt băm", 30, "gram"], ["Rau răm tươi, đậu phộng", 40, "gram"]]],
    ["Lẩu Riêu Cua Bắp Bò Sườn Sụn", 45, "Nồi lẩu riêu cua đồng sôi sùng sục, nhúng sườn sụn giòn sần sật, bắp bò hoa tươi rói và đĩa rau muống hoa chuối.", [["Cua đồng giã lấy gạch", 500, "gram"], ["Bắp bò hoa thái mỏng", 350, "gram"], ["Sườn sụn non", 300, "gram"], ["Cà chua, giấm bỗng nếp", 60, "ml"], ["Rau muống chẻ, hoa chuối", 250, "gram"]]],
    ["Gà Hấp Muối Hột Lá Chuối", 45, "Gà ta đặt trên lớp muối hột dày và lá chuối đậy kín nung nóng, thịt gà chín bằng hơi muối ngọt đậm đà không ngấy.", [["Gà ta nguyên con", 1.3, "kg"], ["Muối hột to", 1, "kg"], ["Lá chuối tươi, sả", 5, "nhánh"], ["Muối tiêu chanh ớt", 30, "gram"]]],
    ["Bánh Giò Nóng Thịt Băm Mộc Nhĩ", 40, "Chiếc bánh giò bột gạo mềm mịn thơm mùi lá chuối, nhân thịt nạc băm, mộc nhĩ nấm hương và trứng cút béo bùi.", [["Bột gạo tẻ pha bột năng", 300, "gram"], ["Nước hầm xương ngọt", 600, "ml"], ["Thịt heo nạc xay", 200, "gram"], ["Trứng cút luộc", 6, "quả"], ["Lá chuối gói bánh", 10, "lá"]]],
    ["Gỏi Tai Heo Dưa Leo Giòn Sần Sật", 25, "Tai heo luộc trắng giòn trộn cùng dưa leo thái lát, rau thơm, đậu phộng và nước mắm tỏi ớt chua cay mặn ngọt.", [["Tai heo làm sạch luộc chín", 300, "gram"], ["Dưa leo tươi", 2, "quả"], ["Cà rốt thái sợi, rau răm", 80, "gram"], ["Nước mắm trộn gỏi", 50, "ml"]]],
    ["Bún Cá Chấm Hải Phòng", 35, "Cá rô phi chiên giòn rụm để riêng đĩa chấm cùng nước mắm tỏi ớt chua ngọt gừng tươi, ăn kèm bát bún nước dùng thanh ngọt.", [["Cá rô phi phi lê", 500, "gram"], ["Bún tươi", 400, "gram"], ["Thì là, hành hoa", 40, "gram"], ["Nước mắm tỏi ớt gừng", 60, "ml"]]],
    ["Sườn Ram Mặn Ngọt Màu Cánh Gián", 30, "Sườn non chặt miếng vừa ăn đảo cháy cạnh cùng đường nước mắm kẹo lại màu cánh gián óng ả thơm lừng.", [["Sườn non heo", 500, "gram"], ["Nước mắm nhĩ ngon", 3, "muỗng canh"], ["Đường cát vàng, tiêu", 2, "muỗng canh"], ["Hành tím, tỏi băm", 20, "gram"]]],
    ["Canh Ngao Nấu Chua Thì Là", 20, "Ngao biển tươi luộc ngọt nước nấu cùng cà chua, dứa thơm, quả dọc chua và thì là hành hoa thanh mát cơ thể.", [["Ngao trắng tươi sống", 800, "gram"], ["Cà chua chín, dứa ngọt", 200, "gram"], ["Thì là, hành hoa", 30, "gram"], ["Quả me hoặc quả sấu", 2, "quả"]]],
    ["Bánh Chuối Nướng Nước Cốt Dừa", 45, "Bánh chuối xiêm chín ngào bơ bánh mì ngâm sữa dừa nướng lò thơm nức mũi mềm dẻo béo ngậy ngọt ngào.", [["Chuối sứ chín mùi", 8, "trái"], ["Bánh mì cũ xé nhỏ", 2, "ổ"], ["Nước cốt dừa béo", 250, "ml"], ["Sữa đặc có đường", 100, "ml"], ["Bơ lạt nướng", 30, "gram"]]],
    ["Chè Bưởi An Giang Giòn Sần Sật", 40, "Cùi bưởi sơ chế khử đắng giòn sần sật nấu đậu xanh bùi bùi và nước cốt dừa sánh béo thơm hương hoa bưởi.", [["Cùi bưởi da xanh", 1, "quả"], ["Đậu xanh cà vỏ", 150, "gram"], ["Bột năng thượng hạng", 100, "gram"], ["Đường thốt nốt", 150, "gram"], ["Nước cốt dừa béo ngậy", 150, "ml"]]],
    ["Chè Ba Màu Nước Cốt Dừa", 30, "Ly chè ba màu gồm đậu đỏ ngọt bùi, đậu xanh dẻo thơm và thạch lá dứa giòn mát chan nước cốt dừa đá nhuyễn mát lạnh.", [["Đậu đỏ hầm mềm", 100, "gram"], ["Đậu xanh tán nhuyễn", 100, "gram"], ["Thạch sương sa lá dứa", 100, "gram"], ["Nước cốt dừa sánh béo", 150, "ml"], ["Đá bào tinh khiết", 1, "ly"]]],
    ["Bánh Bò Nướng Nước Cốt Dừa Rễ Tre", 45, "Bánh bò nướng nở rễ tre xốp mềm thơm mùi lá dứa và béo ngậy vị nước cốt dừa nướng vàng óng thơm phức.", [["Bột năng pha bột gạo", 200, "gram"], ["Nước cốt dừa đậm đặc", 200, "ml"], ["Nước cốt lá dứa thơm", 50, "ml"], ["Men nở bánh mì", 5, "gram"], ["Đường cát trắng", 150, "gram"]]],
    ["Cà Phê Muối Xứ Huế Thơm Béo", 10, "Cà phê phin Robusta đậm đà rót trên lớp kem muối béo ngậy mằn mặn hòa quyện đắng ngọt say đắm.", [["Cà phê bột rang xay", 25, "gram"], ["Sữa đặc có đường", 30, "ml"], ["Kem béo thực vật (Rich)", 50, "ml"], ["Muối biển mịn", 1, "nhúm nhỏ"], ["Đá viên tinh khiết", 1, "ly"]]],
    ["Trà Đào Cam Sả Tươi Mát", 15, "Trà đen ủ thơm lừng nấu cùng nước sả tươi, nước cam ép ngọt lịm và miếng đào ngâm giòn ngọt mát lạnh giải nhiệt.", [["Trà túi lọc hương đào", 2, "gói"], ["Cam sành mọng nước", 1, "quả"], ["Sả tươi đập dập nấu nước", 3, "cây"], ["Đào ngâm đóng hộp", 3, "miếng"], ["Siro đào ngọt thanh", 20, "ml"]]],
];

// Thêm 100 món Việt
$recipeStmt = $pdo->prepare("INSERT INTO recipes (title, cooking_time, recipe, type, category_id, photo, video_link, description, instructions, user_id, uploaded_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
$ingStmt = $pdo->prepare("INSERT INTO ingredients (recipe_id, ingredient_name, quantity, unit) VALUES (?, ?, ?, ?)");
$ratingStmt = $pdo->prepare("INSERT INTO ratings (user_id, recipe_id, rating) VALUES (?, ?, ?)");
$commentStmt = $pdo->prepare("INSERT INTO comments (recipe_id, user_id, content, created_at) VALUES (?, ?, ?, NOW() - INTERVAL ? HOUR)");

$countVN = 0;
foreach ($vietnameseDishes as $index => $dish) {
    // Distribute among users 4 -> 13
    $assignedUserId = 4 + ($index % 10);
    $photo = $vietnamesePhotos[$index % count($vietnamesePhotos)];
    $title = $dish[0];
    $time = $dish[1];
    $desc = $dish[2];
    $steps = "Bước 1: Chuẩn bị và sơ chế sạch tất cả các nguyên liệu.\nBước 2: Tẩm ướp gia vị đậm đà trong khoảng 15-20 phút cho thấm đều.\nBước 3: Tiến hành chế biến theo nhiệt độ thích hợp cho đến khi món ăn chín thơm hoàn hảo.\nBước 4: Bày ra đĩa, trang trí rau thơm và thưởng thức nóng cùng gia đình!";
    $instructions = "Sơ chế nguyên liệu -> Tẩm ướp gia vị -> Nấu chín thơm -> Trình bày và thưởng thức";

    $recipeStmt->execute([$title, $time, $steps, 'Vietnamese', 6, $photo, 'https://www.youtube.com/results?search_query=' . urlencode($title), $desc, $instructions, $assignedUserId]);
    $newRecipeId = $pdo->lastInsertId();

    // Insert ingredients
    foreach ($dish[3] as $ing) {
        $ingStmt->execute([$newRecipeId, $ing[0], $ing[1], $ing[2]]);
    }

    // Insert ratings
    $ratingStmt->execute([($assignedUserId == 4 ? 5 : 4), $newRecipeId, 5.0]);
    $ratingStmt->execute([($assignedUserId == 5 ? 6 : 5), $newRecipeId, 4.8]);

    // Insert comment
    $commentAuthorId = ($assignedUserId == 4 ? 6 : 4);
    $commentStmt->execute([$newRecipeId, $commentAuthorId, "Món $title này mình làm theo công thức thành công ngay lần đầu, cả nhà khen tấm tắc!", rand(1, 48)]);

    $countVN++;
}
echo "✓ Đã thêm thành công $countVN công thức món Việt Nam vào cơ sở dữ liệu!\n";

// 2. Thêm 50 công thức cho các danh mục khác (10 món mỗi danh mục: NorthIndian, SouthIndian, Chinese, Dessert, Drinks)
$otherDishes = [
    // NorthIndian (cat 1)
    ['category_id' => 1, 'type' => 'NorthIndian', 'items' => [
        ["Dal Makhani", 40, "Đậu lăng đen hầm nhừ sốt bơ kem béo ngậy Bắc Ấn."],
        ["Aloo Gobi", 30, "Khoai tây xào súp lơ hoa với bột nghệ và thì là Ai Cập."],
        ["Chicken Tikka Masala", 45, "Gà nướng lò tandoor sốt cà ri kem cay nồng."],
        ["Palak Paneer", 35, "Phô mai paneer nấu sốt cải bó xôi mịn thơm."],
        ["Rogan Josh", 50, "Cà ri thịt cừu hầm ớt kashmiri thơm nồng ấm áp."],
        ["Garlic Naan Bread", 20, "Bánh mì naan nướng bơ tỏi giòn xốp."],
        ["Chole Bhature", 45, "Đậu gà cay ăn kèm bánh mì phồng chiên giòn."],
        ["Chicken Korma", 40, "Gà hầm sốt hạt điều sữa chua béo dịu nhẹ."],
        ["Mutton Biryani", 60, "Cơm basmati nấu thịt cừu gia vị hoàng gia."],
        ["Samosa Chaat", 25, "Bánh gối nhân khoai tây đậu hà lan sốt me cay."],
    ]],
    // SouthIndian (cat 2)
    ['category_id' => 2, 'type' => 'SouthIndian', 'items' => [
        ["Rava Dosa", 25, "Bánh xèo bột semolina giòn rụm chấm tương dừa."],
        ["Medu Vada", 30, "Bánh vòng đậu lăng chiên giòn xốp chấm súp sambar."],
        ["Hyderabadi Dum Biryani", 60, "Cơm chiên đút lò nồi đất thơm lừng xứ Nam Ấn."],
        ["Chettinad Pepper Chicken", 40, "Gà xào tiêu đen Chettinad cay nồng đậm đà."],
        ["Coconut Chutney", 15, "Sốt tương dừa nạo mù tạt lá cà ri béo bùi."],
        ["Tomato Rasam", 20, "Súp cà chua me thảo mộc cay ấm giải cảm."],
        ["Uttapam Bánh Bông Lan Mặn", 30, "Bánh pancake bột gạo rắc hành tây cà chua ớt."],
        ["Kerala Fish Curry", 40, "Cà ri cá biển nấu nước cốt dừa quả me kokum."],
        ["Bisi Bele Bath", 45, "Cơm đậu lăng rau củ phong cách Karnataka."],
        ["Curd Rice", 15, "Cơm trộn sữa chua thảo mộc lá cà ri thanh mát."],
    ]],
    // Chinese (cat 3)
    ['category_id' => 3, 'type' => 'Chinese', 'items' => [
        ["Vịt Quay Bắc Kinh", 60, "Vịt quay da giòn cuộn bánh tráng sốt tương ngọt."],
        ["Mapo Tofu (Đậu Hũ Tứ Xuyên)", 25, "Đậu hũ non sốt thịt băm tiêu Tứ Xuyên cay tê lưỡi."],
        ["Hoành Thánh Tôm Thịt Nước", 30, "Súp hoành thánh nước hầm xương gà ngọt trong."],
        ["Mì Xào Giòn Quảng Đông", 25, "Mì trứng xào tôm mực thịt heo rau cải sốt sệt."],
        ["Xá Xíu Mật Ong", 45, "Thịt nạc dăm nướng mật ong đỏ âu mềm ngọt."],
        ["Cá Hấp Hành Gừng Kiểu Hồng Kông", 30, "Cá biển hấp xì dầu gừng sợi thơm thanh tao."],
        ["Sườn Kinh Đô Chua Ngọt", 35, "Sườn heo chiên giòn đảo sốt giấm đen chua ngọt."],
        ["Bánh Bao Thượng Hải (Xiao Long Bao)", 45, "Bánh bao nhân thịt súp ngọt mọng nước bên trong."],
        ["Cơm Chiên Dương Châu", 20, "Cơm rang tơi xốp lạp xưởng tôm trứng giòn thơm."],
        ["Gà Xào Hạt Điều Cung Bảo", 25, "Thịt gà ức xào hạt điều ớt chuông cay ngọt."],
    ]],
    // Dessert (cat 4)
    ['category_id' => 4, 'type' => 'Dessert', 'items' => [
        ["Tiramisu Cà Phê Ý", 30, "Bánh mascarpone cà phê cacao tan chảy mềm mịn."],
        ["Bánh Flan Caramen Nước Cốt Dừa", 35, "Bánh flan trứng sữa mềm mịn sốt caramen đắng nhẹ."],
        ["Panna Cotta Sốt Dâu Tây", 25, "Kem sữa dẻo mềm rưới sốt dâu tây chua ngọt."],
        ["Chè Khúc Bạch Hạnh Nhân", 30, "Khúc bạch phô mai dẻo bùi nhãn tươi hạnh nhân lát."],
        ["Bánh Crepe Sầu Riêng Ngàn Lớp", 40, "Bánh crepe mỏng kẹp kem tươi và thịt sầu riêng béo ngậy."],
        ["Bánh Phô Mai Nướng Basque", 45, "Cheesecake cháy mặt kiểu Tây Ban Nha béo ngậy."],
        ["Bánh Mochi Nhật Bản Nhân Trà Xanh", 35, "Bánh bột nếp dẻo dai nhân kem matcha mát lạnh."],
        ["Chè Xoài Bột Báng Hong Kong", 20, "Súp xoài cốt dừa bưởi hồng thanh mát ngọt lịm."],
        ["Bánh Su Kem Vỏ Giòn Craquelin", 40, "Bánh su nhân kem vani béo ngậy vỏ giòn tan."],
        ["Bánh Brownie Socola Hạnh Nhân", 35, "Bánh socola đậm đặc hạnh nhân thơm bùi."],
    ]],
    // Drinks (cat 5)
    ['category_id' => 5, 'type' => 'Drinks', 'items' => [
        ["Trà Sữa Trân Châu Đường Đen", 15, "Trà sữa kem béo trân châu đường đen dẻo dai."],
        ["Nước Ép Dưa Hấu Bạc Hà", 10, "Nước ép dưa hấu tươi mát lá bạc hà sảng khoái."],
        ["Sinh Tố Bơ Sáp Sữa Đặc", 10, "Bơ sáp dẻo béo xay nhuyễn sữa đặc đá xay."],
        ["Trà Vải Hoa Hồng Mát Lạnh", 15, "Trà lài ủ lạnh vải thiều ngọt lịm hoa hồng thơm."],
        ["Matcha Latte Đá Xay", 15, "Bột trà xanh Uji Nhật Bản đánh sữa tươi béo thơm."],
        ["Nước Chanh Leo Tuyết Hạt Chia", 10, "Chanh dây thơm lừng hạt chia mát gan bổ dưỡng."],
        ["Cacao Nóng Marshmallow", 10, "Cacao nguyên chất béo ấm kẹo xốp marshmallow."],
        ["Sinh Tố Xoài Dứa Nhiệt Đới", 10, "Xoài chín kết hợp dứa ngọt tươi mát giàu vitamin."],
        ["Trà Hoa Cúc Mật Ong Dưỡng Nhan", 15, "Trà hoa cúc thanh nhiệt mật ong rừng ngọt dịu."],
        ["Mojito Dâu Tây Soda Mát Lạnh", 10, "Soda chanh dâu tây lá bạc hà the mát mùa hè."],
    ]]
];

$countOther = 0;
foreach ($otherDishes as $catData) {
    $catId = $catData['category_id'];
    $catType = $catData['type'];

    foreach ($catData['items'] as $item) {
        $assignedUserId = rand(1, 13);
        $title = $item[0];
        $time = $item[1];
        $desc = $item[2];
        $steps = "Bước 1: Sơ chế và chuẩn bị sẵn các nguyên liệu tươi ngon.\nBước 2: Nấu và tẩm ướp theo tỉ lệ gia vị chuẩn.\nBước 3: Hoàn thiện món ăn thơm ngon và thưởng thức cùng bạn bè.";
        $instructions = "Sơ chế -> Nấu chuẩn vị -> Thưởng thức";
        $photo = $vietnamesePhotos[rand(0, count($vietnamesePhotos) - 1)];

        $recipeStmt->execute([$title, $time, $steps, $catType, $catId, $photo, '', $desc, $instructions, $assignedUserId]);
        $rId = $pdo->lastInsertId();

        $ingStmt->execute([$rId, "Nguyên liệu chính", 300, "gram"]);
        $ingStmt->execute([$rId, "Gia vị tổng hợp", 50, "gram"]);

        $ratingStmt->execute([rand(1, 13), $rId, (rand(46, 50) / 10)]);
        $commentStmt->execute([$rId, rand(1, 13), "Công thức món này rất dễ làm và chuẩn vị!", rand(1, 24)]);
        $countOther++;
    }
}

echo "✓ Đã thêm thành công $countOther công thức quốc tế cho các danh mục còn lại!\n";
echo "==> TỔNG CỘNG: Đã nạp thành công 150 công thức món ăn mới vào hệ thống Cook Together!\n";
?>
