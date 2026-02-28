<?php
$pdo = new PDO('mysql:host=localhost;port=3310;dbname=catalog_db', 'root', 'catalog_root_pass');
foreach ($pdo->query('SHOW TABLES') as $row) {
    echo $row[0] . "\n";
}
