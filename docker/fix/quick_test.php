<?php

$endpoints = [
    '/api/monitoring/health'   => 'Monitoring Health',
    '/api/monitoring/metrics'  => 'Monitoring Metrics',
    '/api/catalog/products'    => 'Catalog Products',
    '/api/catalog/categories'  => 'Catalog Categories',
    '/api/users'               => 'User List',
    '/api/orders'              => 'Order List',
];

foreach ($endpoints as $path => $label) {
    $ch = curl_init("http://localhost:9000{$path}");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_HTTPHEADER     => ['Accept: application/json'],
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $icon = ($code >= 200 && $code < 400) ? 'OK' : 'ERR';
    echo "[{$icon}] {$label}: HTTP {$code}\n";
    if ($body && $code > 0) {
        echo "    " . substr($body, 0, 300) . "\n";
    }
}
