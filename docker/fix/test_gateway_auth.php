<?php

function httpReq($method, $url, $headers = [], $body = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HEADER, true);
    
    $curlHeaders = [];
    foreach ($headers as $k => $v) {
        $curlHeaders[] = "$k: $v";
    }
    if (!empty($curlHeaders)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);
    }
    if ($body) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }
    
    $response = curl_exec($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'headers' => substr($response, 0, $headerSize),
        'body' => substr($response, $headerSize),
        'error' => $error,
    ];
}

$base = 'http://127.0.0.1:9000/api/gateway/products';

echo "============================================================\n";
echo "  GATEWAY AUTHENTICATION & AUTHORIZATION TESTS\n";
echo "============================================================\n\n";

// Test 1: 401 - No Authorization header
echo "--- Test 1: 401 Unauthorized (no token) ---\n";
$r = httpReq('GET', $base);
echo "curl -i http://127.0.0.1:9000/api/gateway/products\n";
echo "HTTP Status: {$r['code']}\n";
$data = json_decode($r['body'], true);
echo "Response: " . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
echo "Result: " . ($r['code'] === 401 ? '[PASS]' : '[FAIL]') . "\n\n";

// Test 2: 401 - Invalid token
echo "--- Test 2: 401 Unauthorized (invalid token) ---\n";
$r = httpReq('GET', $base, ['Authorization' => 'Bearer wrong-token-here']);
echo "curl -i -H \"Authorization: Bearer wrong-token-here\" $base\n";
echo "HTTP Status: {$r['code']}\n";
$data = json_decode($r['body'], true);
echo "Response: " . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
echo "Result: " . ($r['code'] === 401 ? '[PASS]' : '[FAIL]') . "\n\n";

// Test 3: 200 - Valid admin token (GET)
echo "--- Test 3: 200 OK (admin token, GET products) ---\n";
$r = httpReq('GET', $base, ['Authorization' => 'Bearer valid-admin-token']);
echo "curl -i -H \"Authorization: Bearer valid-admin-token\" $base\n";
echo "HTTP Status: {$r['code']}\n";
if ($r['code'] === 200) {
    $data = json_decode($r['body'], true);
    echo "Products count: " . count($data['data'] ?? []) . "\n";
}
echo "Result: " . ($r['code'] === 200 ? '[PASS]' : '[FAIL - ' . $r['code'] . ']') . "\n\n";

// Test 4: 200 - Valid user token (GET)
echo "--- Test 4: 200 OK (user token, GET products) ---\n";
$r = httpReq('GET', $base, ['Authorization' => 'Bearer valid-user-token']);
echo "curl -i -H \"Authorization: Bearer valid-user-token\" $base\n";
echo "HTTP Status: {$r['code']}\n";
echo "Result: " . ($r['code'] === 200 ? '[PASS]' : '[FAIL - ' . $r['code'] . ']') . "\n\n";

// Test 5: 403 - User token trying POST (admin required)
echo "--- Test 5: 403 Forbidden (user token, POST) ---\n";
$r = httpReq('POST', $base, [
    'Authorization' => 'Bearer valid-user-token',
    'Content-Type' => 'application/json',
], json_encode(['pro_name' => 'Test Product']));
echo "curl -i -X POST -H \"Authorization: Bearer valid-user-token\" $base\n";
echo "HTTP Status: {$r['code']}\n";
$data = json_decode($r['body'], true);
echo "Response: " . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
echo "Result: " . ($r['code'] === 403 ? '[PASS]' : '[FAIL]') . "\n\n";

// Test 6: 403 - User token trying DELETE (admin required)
echo "--- Test 6: 403 Forbidden (user token, DELETE) ---\n";
$r = httpReq('DELETE', "$base/1", ['Authorization' => 'Bearer valid-user-token']);
echo "curl -i -X DELETE -H \"Authorization: Bearer valid-user-token\" $base/1\n";
echo "HTTP Status: {$r['code']}\n";
$data = json_decode($r['body'], true);
echo "Response: " . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
echo "Result: " . ($r['code'] === 403 ? '[PASS]' : '[FAIL]') . "\n\n";

echo "============================================================\n";
echo "  SUMMARY\n";
echo "============================================================\n";
echo "Test 1 (401 - no token):      " . ($r1 = 'checked') . "\n";
echo "Test 2 (401 - invalid token): checked\n";
echo "Test 3 (200 - admin GET):     checked\n";
echo "Test 4 (200 - user GET):      checked\n";
echo "Test 5 (403 - user POST):     checked\n";
echo "Test 6 (403 - user DELETE):   checked\n";
echo "\nScreenshot commands for Git Bash:\n";
echo "  # 401 screenshot:\n";
echo "  curl -i http://127.0.0.1:9000/api/gateway/products\n\n";
echo "  # 403 screenshot:\n";
echo "  curl -i -X POST -H \"Authorization: Bearer valid-user-token\" http://127.0.0.1:9000/api/gateway/products\n";
