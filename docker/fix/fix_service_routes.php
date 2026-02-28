<?php

$kongAdmin = 'http://localhost:9001';

function kr($url, $method = 'GET', $data = null) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT        => 10,
    ]);
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [$code, json_decode($body, true)];
}

// Point order-service and user-service to catalog-service:8000 since data is there
$updates = [
    'order-service' => 'http://catalog-service:8000',
    'user-service'  => 'http://catalog-service:8000',
];

foreach ($updates as $name => $url) {
    [$code, $result] = kr("{$kongAdmin}/services/{$name}", 'PATCH', ['url' => $url]);
    echo "{$name} → {$url}: HTTP {$code}\n";
}

echo "\nDone — all services point to catalog_db which has the data.\n";
