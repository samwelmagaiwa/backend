<?php
echo "Testing user-roles endpoint...\n";

// Test if the endpoint is accessible
$url = 'http://localhost:8000/api/user-roles';

// You'll need to add authentication token here
$headers = [
    'Accept: application/json',
    'Content-Type: application/json',
    // 'Authorization: Bearer YOUR_TOKEN_HERE'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";

if ($httpCode === 200) {
    echo "✅ Endpoint is working!\n";
} else {
    echo "❌ Endpoint has issues. Check authentication and server status.\n";
}
?>