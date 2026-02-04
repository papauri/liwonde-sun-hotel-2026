<?php
/**
 * Test Image Proxy Script
 * Debug Facebook image loading
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test URL (Facebook CDN image)
$testUrl = 'https://scontent-dub4-1.xx.fbcdn.net/v/t39.30808-6/481260385_619055544171795_8862875008433116455_n.jpg?_nc_cat=102&ccb=1-7&_nc_sid=833d8c&_nc_ohc=5zhqXjflH1YQ7kNvwGV_D-J&_nc_oc=Adk9MwtN20VvqYPjTTBN9m0Fi-xh7DXyhmoruPlEi6EhXiKgRxKUnWrRBoCIQwuwg_U&_nc_zt=23&';

echo "=== Image Proxy Test ===\n\n";
echo "Test URL: " . $testUrl . "\n\n";

// 1. Check if cURL is available
echo "1. Checking cURL availability...\n";
if (function_exists('curl_init')) {
    echo "   ✓ cURL is available\n\n";
} else {
    echo "   ✗ cURL is NOT available\n\n";
    exit(1);
}

// 2. Test cURL initialization
echo "2. Testing cURL initialization...\n";
$ch = curl_init($testUrl);
if ($ch) {
    echo "   ✓ cURL initialized\n\n";
} else {
    echo "   ✗ cURL initialization failed\n\n";
    exit(1);
}

// 3. Test cURL fetch with detailed options
echo "3. Testing cURL fetch...\n";
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS => 5,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    CURLOPT_REFERER => 'https://www.facebook.com/',
    CURLOPT_VERBOSE => true,
]);

$imageData = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
$error = curl_error($ch);
$errno = curl_errno($ch);

curl_close($ch);

echo "   HTTP Code: " . $httpCode . "\n";
echo "   Content Type: " . ($contentType ?: 'N/A') . "\n";
echo "   cURL Error: " . ($error ?: 'None') . "\n";
echo "   cURL Error Number: " . ($errno ?: 'None') . "\n";
echo "   Data Length: " . strlen($imageData ?: '') . " bytes\n\n";

if ($error || $httpCode != 200) {
    echo "   ✗ Failed to fetch image\n\n";
    echo "Full cURL error info:\n";
    print_r([
        'error' => $error,
        'errno' => $errno,
        'http_code' => $httpCode
    ]);
    exit(1);
}

if (empty($imageData)) {
    echo "   ✗ Empty image data\n\n";
    exit(1);
}

echo "   ✓ Image fetched successfully\n\n";

// 4. Test image validation
echo "4. Testing image validation...\n";
$imageInfo = @getimagesizefromstring($imageData);
if ($imageInfo) {
    echo "   ✓ Valid image detected\n";
    echo "   MIME Type: " . $imageInfo['mime'] . "\n";
    echo "   Width: " . $imageInfo[0] . "px\n";
    echo "   Height: " . $imageInfo[1] . "px\n\n";
} else {
    echo "   ✗ Invalid image data\n\n";
    exit(1);
}

// 5. Test cache directory
echo "5. Testing cache directory...\n";
$cacheDir = dirname(__FILE__) . '/data/image-cache/';
echo "   Cache directory: " . $cacheDir . "\n";

if (!file_exists($cacheDir)) {
    echo "   Creating cache directory...\n";
    if (mkdir($cacheDir, 0755, true)) {
        echo "   ✓ Cache directory created\n\n";
    } else {
        echo "   ✗ Failed to create cache directory\n\n";
        exit(1);
    }
} else {
    echo "   ✓ Cache directory exists\n\n";
}

if (is_writable($cacheDir)) {
    echo "   ✓ Cache directory is writable\n\n";
} else {
    echo "   ✗ Cache directory is NOT writable\n\n";
    exit(1);
}

// 6. Test saving to cache
echo "6. Testing cache save...\n";
$cacheFile = $cacheDir . md5($testUrl) . '.jpg';
$bytesWritten = file_put_contents($cacheFile, $imageData);

if ($bytesWritten !== false) {
    echo "   ✓ Image saved to cache\n";
    echo "   File: " . $cacheFile . "\n";
    echo "   Size: " . $bytesWritten . " bytes\n\n";
} else {
    echo "   ✗ Failed to save to cache\n\n";
    exit(1);
}

echo "=== All Tests Passed! ===\n\n";
echo "The image proxy should work correctly.\n";
echo "Cached image location: " . $cacheFile . "\n";
?>