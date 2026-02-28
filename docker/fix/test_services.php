<?php

$kongBase = 'http://localhost:9000';

$tests = [
    // Catalog Service
    ['GET', '/api/catalog/products',      'Catalog: List products'],
    ['GET', '/api/catalog/categories',    'Catalog: List categories'],

    // Order Service
    ['GET', '/api/orders',               'Order: List orders'],

    // User Service
    ['GET', '/api/users',                'User: List users'],

    // Monitoring Service
    ['GET', '/api/monitoring/health',    'Monitoring: Health check'],
    ['GET', '/api/monitoring/metrics',   'Monitoring: Prometheus metrics'],
    ['GET', '/api/monitoring/services',  'Monitoring: Service discovery'],
];

echo "=== TESTING ALL MICROSERVICE ENDPOINTS ===\n\n";

foreach ($tests as [$method, $path, $label]) {
    $ch = curl_init("{$kongBase}{$path}");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_HTTPHEADER     => ['Accept: application/json'],
    ]);

    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $status = $code >= 200 && $code < 400 ? 'PASS' : 'FAIL';
    echo "[{$status}] {$label}\n";
    echo "  {$method} {$path} => HTTP {$code}\n";

    if ($body) {
        $json = json_decode($body, true);
        if ($json) {
            echo "  Response keys: " . implode(', ', array_keys($json)) . "\n";
        } else {
            echo "  Response (text): " . substr($body, 0, 200) . "\n";
        }
    }
    echo "\n";
}

// Test User register (POST)
echo "--- User Service: Register ---\n";
$ch = curl_init("{$kongBase}/api/users/register");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode([
        'u_name'                  => 'Test User',
        'u_email'                 => 'test_' . time() . '@example.com',
        'u_password'              => 'password123',
        'u_password_confirmation' => 'password123',
        'u_phone'                 => '0912345678',
    ]),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json',
    ],
    CURLOPT_TIMEOUT => 10,
]);
$body = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$status = $code === 201 ? 'PASS' : ($code === 422 ? 'PASS (dup)' : 'FAIL');
echo "[{$status}] POST /api/users/register => HTTP {$code}\n";
$json = json_decode($body, true);
if ($json) echo "  " . json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
echo "\n";

// Test User login (POST)
echo "--- User Service: Login ---\n";
$ch = curl_init("{$kongBase}/api/users/login");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode([
        'u_email'    => 'admin@example.com',
        'u_password' => 'password',
    ]),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json',
    ],
    CURLOPT_TIMEOUT => 10,
]);
$body = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "[INFO] POST /api/users/login => HTTP {$code}\n";
$json = json_decode($body, true);
if ($json) echo "  " . json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
echo "\n";

echo "=== ALL TESTS COMPLETE ===\n";
