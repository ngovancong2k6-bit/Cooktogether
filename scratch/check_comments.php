<?php
$c = new mysqli('localhost', 'root', '', 'recipe_sharing_platform');
$res = $c->query("SHOW COLUMNS FROM `comments`");
echo "Cột của bảng comments:\n";
while ($row = $res->fetch_assoc()) {
    echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
}
