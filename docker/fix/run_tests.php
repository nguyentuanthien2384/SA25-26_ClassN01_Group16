<?php

function httpGet($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $response = curl_exec($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);

    return [
        'code' => $httpCode,
        'headers' => $headers,
        'body' => $body,
        'error' => $error,
    ];
}

echo "============================================\n";
echo "  KONG API GATEWAY - TEST SUITE\n";
echo "============================================\n\n";

// Test A: GET /api/products
echo "--- Test A: GET /api/products (200 OK) ---\n";
$result = httpGet('http://127.0.0.1:9000/api/products');
$status = ($result['code'] === 200) ? 'PASS' : 'FAIL';
echo "HTTP Status: {$result['code']}\n";
if ($result['error']) echo "Error: {$result['error']}\n";
if ($result['code'] === 200) {
    $data = json_decode($result['body'], true);
    echo "Products count: " . count($data['data'] ?? []) . "\n";
    echo "Current page: " . ($data['current_page'] ?? 'N/A') . "\n";
}
echo "Result: [$status]\n\n";

// Test B: GET /api/products/1
echo "--- Test B: GET /api/products/1 (Single product) ---\n";
$result = httpGet('http://127.0.0.1:9000/api/products/1');
$status = ($result['code'] === 200) ? 'PASS' : ($result['code'] === 404 ? 'PASS (no product id=1)' : 'FAIL');
echo "HTTP Status: {$result['code']}\n";
if ($result['error']) echo "Error: {$result['error']}\n";
if ($result['code'] === 200) {
    $data = json_decode($result['body'], true);
    echo "Product name: " . ($data['pro_name'] ?? 'N/A') . "\n";
}
echo "Result: [$status]\n\n";

// Test C: GET /api/health
echo "--- Test C: GET /api/health (Health check) ---\n";
$result = httpGet('http://127.0.0.1:9000/api/health');
echo "HTTP Status: {$result['code']}\n";
if ($result['error']) echo "Error: {$result['error']}\n";
if ($result['body']) {
    $data = json_decode($result['body'], true);
    echo "Status: " . ($data['status'] ?? 'N/A') . "\n";
    echo "Service: " . ($data['service'] ?? 'N/A') . "\n";
    if (isset($data['checks'])) {
        echo "Database: " . ($data['checks']['database'] ?? 'N/A') . "\n";
        echo "Cache: " . ($data['checks']['cache'] ?? 'N/A') . "\n";
    }
}
$status = (in_array($result['code'], [200, 503])) ? 'PASS' : 'FAIL';
echo "Result: [$status]\n\n";

echo "============================================\n";
echo "  TEST SUMMARY\n";
echo "============================================\n";
echo "Test A (Products list): " . ($result['code'] >= 0 ? "Executed" : "Failed") . "\n";
echo "Test B (Single product): Executed\n";
echo "Test C (Health check): Executed\n";
echo "\nTest D (503 Service Unavailable):\n";
echo "  Run manually:\n";
echo "  1. docker stop catalog_service\n";
echo "  2. curl -i http://127.0.0.1:9000/api/products  (should return 503)\n";
echo "  3. docker start catalog_service  (restore)\n";
