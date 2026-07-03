<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$apiKey = config('settings.binance_api_key');
$apiSecret = config('settings.binance_api_secret');

if (!$apiKey || !$apiSecret) {
    die("Binance API keys are not set in the configuration.\n");
}

$timestamp = number_format(microtime(true) * 1000, 0, '.', '');
$queryString = "timestamp=" . $timestamp;
$signature = hash_hmac('sha256', $queryString, $apiSecret);

$url = "https://api.binance.com/sapi/v1/pay/transactions?" . $queryString . "&signature=" . $signature;

echo "=============================================\n";
echo "CURL COMMAND TO CHECK HISTORY\n";
echo "=============================================\n\n";

echo "curl -X GET \\\n";
echo "  \"{$url}\" \\\n";
echo "  -H \"X-MBX-APIKEY: {$apiKey}\"\n\n";

echo "=============================================\n";
echo "RESPONSE FROM BINANCE\n";
echo "=============================================\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-MBX-APIKEY: " . $apiKey
]);

$response = curl_exec($ch);
curl_close($ch);

// Pretty print JSON
$json = json_decode($response);
if ($json) {
    echo json_encode($json, JSON_PRETTY_PRINT) . "\n";
} else {
    echo $response . "\n";
}
