<?php
$opts = ['http' => ['timeout' => 10, 'ignore_errors' => true]];
$ctx = stream_context_create($opts);
$response = @file_get_contents('http://localhost:8000/api/ping', false, $ctx);
echo "HTTP Response Headers:\n";
if (isset($http_response_header)) {
    foreach ($http_response_header as $h) echo $h . "\n";
}
echo "\nBody:\n";
echo $response ?: "(empty)";
echo "\n";
