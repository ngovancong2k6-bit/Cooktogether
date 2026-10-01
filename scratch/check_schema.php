<?php
$c = new mysqli('localhost', 'root', '', 'recipe_sharing_platform');
$tables = ['recipes', 'ingredients', 'recipe_steps', 'categories', 'ratings', 'comments'];
foreach ($tables as $t) {
    echo "=== TABLE: $t ===\n";
    $res = $c->query("DESCRIBE `$t`");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            echo "  - {$row['Field']} ({$row['Type']})\n";
        }
    } else {
        echo "  [Không tồn tại]\n";
    }
}
