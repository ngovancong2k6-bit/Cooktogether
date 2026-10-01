<?php
$c = new mysqli('localhost', 'root', '', 'recipe_sharing_platform');
$c->query('ALTER TABLE `recipes` MODIFY `description` TEXT');
$c->query('ALTER TABLE `recipes` MODIFY `instructions` TEXT');
$c->query('ALTER TABLE `recipes` MODIFY `photo` TEXT');
$c->query('ALTER TABLE `recipes` MODIFY `video_link` TEXT');
echo "✓ Đã nâng cấp các cột trong bảng recipes thành công!\n";
