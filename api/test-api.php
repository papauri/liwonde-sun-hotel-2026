<?php
/**
 * Test script for Liwonde Sun Hotel Booking API
 * 
 * This script tests the API endpoints with the sample API key
 * API Key: test_key_12345 (hashed in database)
 */

echo "<h1>Liwonde Sun Hotel Booking API Test</h1>";

// Test API key (from database insert)
$apiKey = 'test_key_12345';
$baseUrl = 'http://localhost' . dirname($_SERVER['PHP_SELF']) . '/../api/';

echo "<p>Base URL: $baseUrl</p>";
echo "<p>API Key: $apiKey</p>";

// Function to make API requests
function testApi($url, $method = 'GET', $data = null) {
    $ch = curl_init();
    
    $headers = [
        'X-API-Key: test_key_12345',
        'Content-Type: application/json'
    ];
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'response' => json_decode($response, true),
        'raw' => $response
    ];
}

// Test 1: Get API info
echo "<h2>Test 1: Get API Info</h2>";
$result = testApi($baseUrl);
echo "<pre>HTTP Code: " . $result['code'] . "</pre>";
echo "<pre>" . json_encode($result['response'], JSON_PRETTY_PRINT) . "</pre>";

// Test 2: Get rooms
echo "<h2>Test 2: Get Rooms</h2>";
$result = testApi($baseUrl . 'rooms');
echo "<pre>HTTP Code: " . $result['code'] . "</pre>";
if ($result['response']['success']) {
    echo "<p>Found " . count($result['response']['data']['rooms']) . " rooms</p>";
    echo "<pre>" . json_encode($result['response']['data']['rooms'][0] ?? 'No rooms', JSON_PRETTY_PRINT) . "</pre>";
} else {
    echo "<pre>" . json_encode($result['response'], JSON_PRETTY_PRINT) . "</pre>";
}

// Test 3: Check availability
echo "<h2>Test 3: Check Availability</h2>";
$availabilityUrl = $baseUrl . 'availability?room_id=1&check_in=2026-02-01&check_out=2026-02-03';
$result = testApi($availabilityUrl);
echo "<pre>HTTP Code: " . $result['code'] . "</pre>";
echo "<pre>" . json_encode($result['response'], JSON_PRETTY_PRINT) . "</pre>";

// Test 4: Create booking (if room is available)
echo "<h2>Test 4: Create Booking</h2>";
$bookingData = [
    'room_id' => 1,
    'guest_name' => 'API Test User',
    'guest_email' => 'test@example.com',
    'guest_phone' => '+265987654321',
    'guest_country' => 'Malawi',
    'guest_address' => 'Test Address',
    'number_of_guests' => 2,
    'check_in_date' => '2026-02-10',
    'check_out_date' => '2026-02-12',
    'special_requests' => 'API Test Booking'
];

$result = testApi($baseUrl . 'bookings', 'POST', $bookingData);
echo "<pre>HTTP Code: " . $result['code'] . "</pre>";
echo "<pre>" . json_encode($result['response'], JSON_PRETTY_PRINT) . "</pre>";

// Test 5: Get booking details (if booking was created)
if ($result['response']['success'] ?? false) {
    $bookingRef = $result['response']['data']['booking']['booking_reference'] ?? null;
    if ($bookingRef) {
        echo "<h2>Test 5: Get Booking Details</h2>";
        $detailsUrl = $baseUrl . 'bookings?id=' . $bookingRef;
        $result = testApi($detailsUrl);
        echo "<pre>HTTP Code: " . $result['code'] . "</pre>";
        echo "<pre>" . json_encode($result['response'], JSON_PRETTY_PRINT) . "</pre>";
    }
}

echo "<h2>API Test Complete</h2>";
echo "<p>Check the results above to verify API functionality.</p>";
echo "<p><strong>Note:</strong> This test script requires the API tables to be created in the database.</p>";
echo "<p>Run the SQL file: <code>Database/add-api-keys-table.sql</code></p>";