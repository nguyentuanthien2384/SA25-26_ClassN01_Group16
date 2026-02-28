<?php

$kongAdmin = 'http://localhost:9001';

function kongRequest($url, $method = 'GET', $data = null) {
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

echo "=== Setting up Kong routes for all services ===\n\n";

$services = [
    'catalog-service'      => ['url' => 'http://catalog-service:8000', 'paths' => ['/api/catalog']],
    'order-service'        => ['url' => 'http://order-service:8000',   'paths' => ['/api/orders']],
    'user-service'         => ['url' => 'http://user-service:8000',    'paths' => ['/api/users']],
    'notification-service' => ['url' => 'http://catalog-service:8000', 'paths' => ['/api/notifications']],
    'monitoring-service'   => ['url' => 'http://catalog-service:8000', 'paths' => ['/api/monitoring']],
];

foreach ($services as $name => $cfg) {
    echo "--- {$name} ---\n";

    // Check if service exists
    [$code, $existing] = kongRequest("{$kongAdmin}/services/{$name}");

    if ($code === 200) {
        // Update
        [$code, $result] = kongRequest("{$kongAdmin}/services/{$name}", 'PATCH', [
            'url'             => $cfg['url'],
            'connect_timeout' => 5000,
            'read_timeout'    => 10000,
            'retries'         => 2,
        ]);
        echo "  Updated service: HTTP {$code}\n";
    } else {
        // Create
        [$code, $result] = kongRequest("{$kongAdmin}/services", 'POST', [
            'name'            => $name,
            'url'             => $cfg['url'],
            'connect_timeout' => 5000,
            'read_timeout'    => 10000,
            'retries'         => 2,
        ]);
        echo "  Created service: HTTP {$code}\n";
    }

    // Check existing routes
    [$code, $routes] = kongRequest("{$kongAdmin}/services/{$name}/routes");
    $existingPaths = [];
    if ($code === 200 && !empty($routes['data'])) {
        foreach ($routes['data'] as $r) {
            $existingPaths = array_merge($existingPaths, $r['paths'] ?? []);
        }
    }

    foreach ($cfg['paths'] as $path) {
        if (in_array($path, $existingPaths)) {
            echo "  Route {$path} already exists\n";
            continue;
        }

        [$code, $result] = kongRequest("{$kongAdmin}/services/{$name}/routes", 'POST', [
            'paths'          => [$path],
            'strip_path'     => false,
            'preserve_host'  => false,
        ]);
        echo "  Created route {$path}: HTTP {$code}\n";
    }

    echo "\n";
}

echo "=== Kong setup complete ===\n";
