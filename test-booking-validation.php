<?php
/**
 * Unit Tests for Booking Validation Functions
 * Liwonde Sun Hotel - Booking System Validation Tests
 * 
 * Run this file to test the validation functions
 * Usage: php test-booking-validation.php
 */

require_once 'config/database.php';

// Test tracking
$tests_run = 0;
$tests_passed = 0;
$tests_failed = 0;
$results = [];

function assertTest($test_name, $condition, $message = '') {
    global $tests_run, $tests_passed, $tests_failed, $results;
    $tests_run++;
    
    if ($condition) {
        $tests_passed++;
        $results[] = [
            'test' => $test_name,
            'status' => 'PASS',
            'message' => $message ?: 'Test passed'
        ];
        echo "✓ $test_name\n";
    } else {
        $tests_failed++;
        $results[] = [
            'test' => $test_name,
            'status' => 'FAIL',
            'message' => $message ?: 'Test failed'
        ];
        echo "✗ $test_name - $message\n";
    }
}

function printTestHeader($title) {
    echo "\n" . str_repeat('=', 60) . "\n";
    echo "  $title\n";
    echo str_repeat('=', 60) . "\n\n";
}

// ============================================================
// TEST SUITE: validateBookingData()
// ============================================================
printTestHeader('TEST SUITE: validateBookingData()');

// Test 1: Valid booking data
$valid_data = [
    'room_id' => 1,
    'guest_name' => 'John Doe',
    'guest_email' => 'john@example.com',
    'guest_phone' => '+265 999 123 456',
    'check_in_date' => date('Y-m-d', strtotime('+7 days')),
    'check_out_date' => date('Y-m-d', strtotime('+10 days')),
    'number_of_guests' => 2
];
$result = validateBookingData($valid_data);
assertTest(
    'Valid booking data should pass validation',
    $result['valid'],
    json_encode($result['errors'])
);

// Test 2: Missing required field (guest_name)
$missing_name = $valid_data;
unset($missing_name['guest_name']);
$result = validateBookingData($missing_name);
assertTest(
    'Missing guest_name should fail validation',
    !$result['valid'] && isset($result['errors']['guest_name']),
    $result['errors']['guest_name'] ?? 'Should have error message'
);

// Test 3: Invalid email format
$invalid_email = $valid_data;
$invalid_email['guest_email'] = 'invalid-email';
$result = validateBookingData($invalid_email);
assertTest(
    'Invalid email should fail validation',
    !$result['valid'] && isset($result['errors']['guest_email']),
    $result['errors']['guest_email']
);

// Test 4: Phone number too short
$short_phone = $valid_data;
$short_phone['guest_phone'] = '123';
$result = validateBookingData($short_phone);
assertTest(
    'Phone number too short should fail validation',
    !$result['valid'] && isset($result['errors']['guest_phone']),
    $result['errors']['guest_phone']
);

// Test 5: Check-in date in past
$past_date = $valid_data;
$past_date['check_in_date'] = date('Y-m-d', strtotime('-1 day'));
$result = validateBookingData($past_date);
assertTest(
    'Check-in date in past should fail validation',
    !$result['valid'] && isset($result['errors']['check_in_date']),
    $result['errors']['check_in_date']
);

// Test 6: Check-out before check-in
$invalid_dates = $valid_data;
$invalid_dates['check_in_date'] = date('Y-m-d', strtotime('+10 days'));
$invalid_dates['check_out_date'] = date('Y-m-d', strtotime('+7 days'));
$result = validateBookingData($invalid_dates);
assertTest(
    'Check-out before check-in should fail validation',
    !$result['valid'] && isset($result['errors']['check_out_date']),
    $result['errors']['check_out_date']
);

// Test 7: Maximum stay duration exceeded
$max_stay = $valid_data;
$max_stay['check_in_date'] = date('Y-m-d', strtotime('+1 day'));
$max_stay['check_out_date'] = date('Y-m-d', strtotime('+35 days'));
$result = validateBookingData($max_stay);
assertTest(
    'Stay duration > 30 days should fail validation',
    !$result['valid'] && isset($result['errors']['check_out_date']),
    $result['errors']['check_out_date']
);

// Test 8: Too many guests
$too_many_guests = $valid_data;
$too_many_guests['number_of_guests'] = 25;
$result = validateBookingData($too_many_guests);
assertTest(
    'Number of guests > 20 should fail validation',
    !$result['valid'] && isset($result['errors']['number_of_guests']),
    $result['errors']['number_of_guests']
);

// Test 9: Zero guests
$zero_guests = $valid_data;
$zero_guests['number_of_guests'] = 1;
$result = validateBookingData($zero_guests);
assertTest(
    'Number of guests >= 1 should pass validation',
    $result['valid'],
    json_encode($result['errors'])
);

// ============================================================
// TEST SUITE: checkRoomAvailability()
// ============================================================
printTestHeader('TEST SUITE: checkRoomAvailability()');

// Test 10: Check availability for valid room and dates
$future_check_in = date('Y-m-d', strtotime('+14 days'));
$future_check_out = date('Y-m-d', strtotime('+17 days'));
$result = checkRoomAvailability(1, $future_check_in, $future_check_out);
assertTest(
    'Valid future dates should show room available',
    $result['available'] ?? false,
    $result['error'] ?? 'Should not have error'
);

// Test 11: Check non-existent room
$result = checkRoomAvailability(999, $future_check_in, $future_check_out);
assertTest(
    'Non-existent room should fail availability check',
    !$result['available'] && !$result['room_exists'],
    $result['error']
);

// Test 12: Check with conflicting dates (simulated overlap)
// Note: This test depends on actual booking data in database
$result = checkRoomAvailability(1, '2026-01-25', '2026-01-28');
assertTest(
    'Availability check should return room details',
    isset($result['room']) && isset($result['room']['name']),
    'Should have room details'
);

// Test 13: Calculate nights correctly
$result = checkRoomAvailability(1, '2026-02-01', '2026-02-05');
assertTest(
    'Should calculate 4 nights correctly',
    $result['nights'] === 4,
    "Calculated {$result['nights']} nights instead of 4"
);

// ============================================================
// TEST SUITE: validateBookingWithAvailability()
// ============================================================
printTestHeader('TEST SUITE: validateBookingWithAvailability()');

// Test 14: Combined validation and availability check
$valid_booking = [
    'room_id' => 1,
    'guest_name' => 'Jane Smith',
    'guest_email' => 'jane@example.com',
    'guest_phone' => '+265 888 234 567',
    'check_in_date' => date('Y-m-d', strtotime('+20 days')),
    'check_out_date' => date('Y-m-d', strtotime('+25 days')),
    'number_of_guests' => 2
];
$result = validateBookingWithAvailability($valid_booking);
assertTest(
    'Valid booking with availability check should pass',
    $result['valid'] && isset($result['availability']),
    json_encode($result)
);

// Test 15: Booking with validation errors
$invalid_booking = $valid_booking;
$invalid_booking['guest_email'] = 'invalid-email';
$result = validateBookingWithAvailability($invalid_booking);
assertTest(
    'Invalid email should cause validation failure',
    !$result['valid'] && $result['type'] === 'validation',
    json_encode($result['errors'])
);

// ============================================================
// TEST SUITE: isRoomAvailable() (Legacy function)
// ============================================================
printTestHeader('TEST SUITE: isRoomAvailable() - Legacy Function');

// Test 16: Legacy availability check
$test_dates_in = date('Y-m-d', strtotime('+30 days'));
$test_dates_out = date('Y-m-d', strtotime('+35 days'));
$available = isRoomAvailable(1, $test_dates_in, $test_dates_out);
assertTest(
    'Legacy function should return true for available dates',
    $available === true,
    'Should return true'
);

// ============================================================
// TEST SUMMARY
// ============================================================
printTestHeader('TEST SUMMARY');

$percentage = $tests_run > 0 ? round(($tests_passed / $tests_run) * 100, 2) : 0;

echo "Total Tests Run: $tests_run\n";
echo "Tests Passed: $tests_passed\n";
echo "Tests Failed: $tests_failed\n";
echo "Success Rate: $percentage%\n";

if ($tests_failed > 0) {
    echo "\n" . str_repeat('=', 60) . "\n";
    echo "FAILED TESTS DETAILS:\n";
    echo str_repeat('=', 60) . "\n\n";
    
    foreach ($results as $result) {
        if ($result['status'] === 'FAIL') {
            echo "Test: {$result['test']}\n";
            echo "Status: {$result['status']}\n";
            echo "Message: {$result['message']}\n\n";
        }
    }
}

echo "\n";
if ($tests_failed === 0) {
    echo "🎉 ALL TESTS PASSED! 🎉\n";
} else {
    echo "⚠️  $tests_failed TEST(S) FAILED ⚠️\n";
}
echo "\n";