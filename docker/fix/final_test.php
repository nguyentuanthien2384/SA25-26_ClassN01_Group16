<?php

$kong = 'http://localhost:9000';

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║       MICROSERVICES API ENDPOINT TEST RESULTS               ║\n";
echo "╠══════════════════════════════════════════════════════════════╣\n\n";

$tests = [
    ['GET',  '/api/catalog/products',         'Catalog Service — List Products'],
    ['GET',  '/api/catalog/products?search=dell&sort=price_asc', 'Catalog Service — Search + Sort'],
    ['GET',  '/api/catalog/categories',        'Catalog Service — List Categories'],
    ['GET',  '/api/orders',                    'Order Service — List Orders'],
    ['GET',  '/api/orders/89',                 'Order Service — Single Order + Items'],
    ['GET',  '/api/users',                     'User Service — List Users'],
    ['GET',  '/api/users/5',                   'User Service — Get Profile'],
    ['GET',  '/api/monitoring/health',         'Monitoring — Health Check'],
    ['GET',  '/api/monitoring/metrics',        'Monitoring — Prometheus Metrics'],
    ['GET',  '/api/monitoring/services',       'Monitoring — Service Discovery'],
    ['GET',  '/api/gateway/products',          'Gateway — Products (via token)'],
];

$pass = 0;
$fail = 0;

foreach ($tests as [$method, $path, $label]) {
    $headers = ['Accept: application/json'];
    if (strpos($path, 'gateway') !== false) {
        $headers[] = 'Authorization: Bearer valid-admin-token';
    }

    $ch = curl_init("{$kong}{$path}");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_HTTPHEADER     => $headers,
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $ok = ($code >= 200 && $code < 400);
    $icon = $ok ? 'PASS' : 'FAIL';
    if ($ok) $pass++; else $fail++;

    echo "[{$icon}] {$label}\n";
    echo "       {$method} {$path} => HTTP {$code}\n";

    if ($body && $code > 0) {
        $json = json_decode($body, true);
        if ($json) {
            $preview = json_encode($json, JSON_UNESCAPED_UNICODE);
            echo "       " . substr($preview, 0, 120) . "\n";
        } else {
            echo "       " . substr($body, 0, 120) . "\n";
        }
    }
    echo "\n";
}

echo "╠══════════════════════════════════════════════════════════════╣\n";
echo "║  TOTAL: {$pass} PASS / {$fail} FAIL  out of " . ($pass + $fail) . " tests             ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
