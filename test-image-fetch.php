<?php
/**
 * Simple test for file_get_contents method
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$testUrl = 'https://scontent-dub4-1.xx.fbcdn.net/v/t39.30808-6/481260385_619055544171795_8862875008433116455_n.jpg?_nc_cat=102&ccb=1-7&_nc_sid=833d8c&_nc_ohc=5zhqXjflH1YQ7kNvwGV_D-J&_nc_oc=Adk9MwtN20VvqYPjTTBN9m0Fi-xh7DXyhmoruPlEi6EhXiKgRxKUnWrRBoCIQwuwg_U&_nc_zt=23&';

echo "Testing file_get_contents with Facebook image...\n\n";

$opts = [
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n" .
                   "Referer: https://www.facebook.com/\r\n",
        'timeout' => 30,
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ]
];

$context = stream_context_create($opts);
$imageData = @file_get_contents($testUrl, false, $context);

if ($imageData === false) {
    echo "FAILED to fetch image\n";
    $error = error_get_last();
    print_r($error);
    exit(1);
}

echo "SUCCESS! Fetched " . strlen($imageData) . " bytes\n";

$imageInfo = @getimagesizefromstring($imageData);
if ($imageInfo) {
    echo "Valid image: " . $imageInfo['mime'] . " (" . $imageInfo[0] . "x" . $imageInfo[1] . ")\n";
    
    // Save test file
    file_put_contents('test-facebook-image.jpg', $imageData);
    echo "Saved to: test-facebook-image.jpg\n";
} else {
    echo "WARNING: Data fetched but doesn't look like a valid image\n";
}

echo "\nThe image proxy should now work!\n";
?>