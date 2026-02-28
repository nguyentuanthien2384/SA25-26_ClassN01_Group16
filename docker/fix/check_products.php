<?php
$data = json_decode(file_get_contents('http://127.0.0.1:9000/api/products'), true);
if (isset($data['data']) && count($data['data']) > 0) {
    echo "Available product IDs:\n";
    foreach ($data['data'] as $p) {
        echo "  ID: {$p['id']} - {$p['pro_name']}\n";
    }
    $firstId = $data['data'][0]['id'];
    echo "\nTesting GET /api/products/$firstId ...\n";
    $product = json_decode(file_get_contents("http://127.0.0.1:9000/api/products/$firstId"), true);
    echo "Result: " . ($product['pro_name'] ?? 'ERROR') . "\n";
} else {
    echo "No products found.\n";
}
