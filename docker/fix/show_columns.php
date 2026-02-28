<?php
$pdo = new PDO('mysql:host=localhost;port=3310;dbname=catalog_db', 'root', 'catalog_root_pass');

foreach (['users', 'transactions', 'oders', 'carts'] as $table) {
    echo "=== {$table} ===\n";
    foreach ($pdo->query("DESCRIBE {$table}") as $row) {
        echo "  {$row['Field']} ({$row['Type']})\n";
    }
    echo "\n";
}
