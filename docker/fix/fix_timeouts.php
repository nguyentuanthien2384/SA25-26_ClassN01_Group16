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

echo "Updating Kong services with shorter timeouts...\n\n";

$services = ['catalog-service', 'health-service', 'order-service', 'user-service'];
foreach ($services as $svc) {
    $result = kongReq('PATCH', "/services/$svc", [
        'connect_timeout' => 3000,
        'read_timeout' => 5000,
        'write_timeout' => 5000,
        'retries' => 1,
    ]);
    echo "$svc: " . ($result['code'] === 200 ? 'Updated' : 'Error ' . $result['code']) . "\n";
}

echo "\nNow test /api/products with catalog_service DOWN:\n";
$ch = curl_init('http://127.0.0.1:9000/api/products');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
if ($error) echo "Error: $error\n";
if ($httpCode === 502 || $httpCode === 503) {
    echo "Result: [PASS] - Service unavailable!\n";
} else {
    echo "Result: HTTP $httpCode\n";
}

$headerSize = strpos($response, "\r\n\r\n");
if ($headerSize !== false) {
    $body = substr($response, $headerSize + 4);
    $data = json_decode($body, true);
    if ($data) {
        echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
    }
}
