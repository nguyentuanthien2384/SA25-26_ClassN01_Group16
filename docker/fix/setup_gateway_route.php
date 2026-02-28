<?php

$kongAdmin = 'http://127.0.0.1:9001';

function kongReq($method, $url, $data = null) {
    global $kongAdmin;
    $ch = curl_init($kongAdmin . $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $code, 'body' => json_decode($res, true)];
}

echo "=== Adding Gateway Route to Kong ===\n\n";

// Check if gateway-service already exists, delete it first
$existing = kongReq('GET', '/services/gateway-service');
if ($existing['code'] === 200) {
    $routes = kongReq('GET', '/services/gateway-service/routes');
    foreach ($routes['body']['data'] ?? [] as $r) {
        kongReq('DELETE', '/routes/' . $r['id']);
    }
    kongReq('DELETE', '/services/gateway-service');
    echo "Cleaned old gateway-service\n";
}

// Create gateway service pointing to catalog-service (which has the Laravel gateway routes)
$result = kongReq('POST', '/services', [
    'name' => 'gateway-service',
    'url' => 'http://catalog-service:8000',
    'connect_timeout' => 3000,
    'read_timeout' => 5000,
    'retries' => 1,
]);
echo "gateway-service: " . ($result['code'] === 201 ? 'Created' : 'Error ' . $result['code']) . "\n";

// Create route for /api/gateway
$result = kongReq('POST', '/services/gateway-service/routes', [
    'name' => 'gateway-route',
    'paths' => ['/api/gateway'],
    'strip_path' => false,
    'preserve_host' => false,
]);
echo "gateway-route /api/gateway: " . ($result['code'] === 201 ? 'Created' : 'Error ' . $result['code']) . "\n";

echo "\n=== Verification ===\n";
$svcs = kongReq('GET', '/services');
foreach ($svcs['body']['data'] ?? [] as $s) {
    echo "  {$s['name']} -> {$s['protocol']}://{$s['host']}:{$s['port']}\n";
}

$routes = kongReq('GET', '/routes');
echo "\nRoutes:\n";
foreach ($routes['body']['data'] ?? [] as $r) {
    echo "  " . implode(', ', $r['paths'] ?? []) . "\n";
}

echo "\n=== DONE ===\n";
