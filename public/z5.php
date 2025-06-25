<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Content-Type: application/x-mpegURL");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$github_url = "https://raw.githubusercontent.com/alex4528/m3u/refs/heads/main/z5.m3u";

$context = stream_context_create([
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: Mozilla/5.0\r\nReferer: \r\n"
    ]
]);

$m3u_data = file_get_contents($github_url, false, $context);

if (!$m3u_data) {
    http_response_code(503);
    echo "#EXTM3U\n# Proxy Error: Failed to fetch content.\n";
    exit;
}

echo $m3u_data;
?>
