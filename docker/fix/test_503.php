<?php
echo "--- Test D: GET /api/products when catalog_service is STOPPED ---\n";
$ch = curl_init('http://127.0.0.1:9000/api/products');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
if ($error) echo "Error: $error\n";
if ($httpCode === 503) {
    echo "Result: [PASS] - 503 Service Unavailable as expected!\n";
} elseif ($httpCode === 502) {
    echo "Result: [PASS] - 502 Bad Gateway (service down, Kong cannot reach upstream)\n";
} else {
    echo "Result: [UNEXPECTED] - Expected 502/503 but got $httpCode\n";
}
echo "\nFull response:\n$response\n";
