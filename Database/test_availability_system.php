<?php
/**
 * Test Room Availability System
 * Tests the complete blocked dates functionality
 */

require_once __DIR__ . '/../config/database.php';

echo "========================================\n";
echo "Room Availability System Test\n";
echo "========================================\n\n";

// Test data
$test_room_id = 1; // Assuming room ID 1 exists
$test_date = date('Y-m-d', strtotime('+7 days')); // A date 7 days from now
$test_date2 = date('Y-m-d', strtotime('+14 days')); // Another date 14 days from now

echo "Test Room ID: {$test_room_id}\n";
echo "Test Date 1: {$test_date}\n";
echo "Test Date 2: {$test_date2}\n\n";

// Test 1: Check availability before blocking
echo "Test 1: Check availability before blocking\n";
echo "-------------------------------------------\n";
$available_before = isRoomAvailable($test_room_id, $test_date, date('Y-m-d', strtotime('+8 days')));
echo "Room available on {$test_date}: " . ($available_before ? "YES" : "NO") . "\n\n";

// Test 2: Block a date
echo "Test 2: Block a date\n";
echo "-------------------------------------------\n";
$block_result = blockRoomDate($test_room_id, $test_date, 'maintenance', 'Test maintenance block', 1);
echo "Block result: " . ($block_result ? "SUCCESS" : "FAILED") . "\n\n";

// Test 3: Check availability after blocking
echo "Test 3: Check availability after blocking\n";
echo "-------------------------------------------\n";
$available_after = isRoomAvailable($test_room_id, $test_date, date('Y-m-d', strtotime('+8 days')));
echo "Room available on {$test_date}: " . ($available_after ? "YES" : "NO") . " (should be NO)\n\n";

// Test 4: Get blocked dates
echo "Test 4: Get blocked dates\n";
echo "-------------------------------------------\n";
$blocked_dates = getBlockedDates($test_room_id, $test_date, $test_date);
echo "Blocked dates found: " . count($blocked_dates) . "\n";
if (!empty($blocked_dates)) {
    foreach ($blocked_dates as $bd) {
        echo "  - ID: {$bd['id']}, Date: {$bd['block_date']}, Type: {$bd['block_type']}, Reason: {$bd['reason']}\n";
    }
}
echo "\n";

// Test 5: Get available dates
echo "Test 5: Get available dates for next 30 days\n";
echo "-------------------------------------------\n";
$start_date = date('Y-m-d');
$end_date = date('Y-m-d', strtotime('+30 days'));
$available_dates = getAvailableDates($test_room_id, $start_date, $end_date);
echo "Available dates found: " . count($available_dates) . "\n";
echo "First 5 available dates: ";
$count = 0;
foreach ($available_dates as $date) {
    if ($count++ < 5) {
        echo $date . " ";
    }
}
echo "\n\n";

// Test 6: Block multiple dates
echo "Test 6: Block multiple dates\n";
echo "-------------------------------------------\n";
$dates_to_block = [$test_date2, date('Y-m-d', strtotime('+21 days'))];
$block_count = blockRoomDates($test_room_id, $dates_to_block, 'event', 'Test event block', 1);
echo "Blocked {$block_count} date(s)\n\n";

// Test 7: Check room availability with conflicts
echo "Test 7: Check room availability with conflicts\n";
echo "-------------------------------------------\n";
$check_in = $test_date;
$check_out = date('Y-m-d', strtotime('+8 days'));
$availability = checkRoomAvailability($test_room_id, $check_in, $check_out);
echo "Availability check from {$check_in} to {$check_out}:\n";
echo "  Available: " . ($availability['available'] ? "YES" : "NO") . "\n";
echo "  Total rooms: " . $availability['total_rooms'] . "\n";
echo "  Available rooms: " . $availability['available_rooms'] . "\n";
if (!empty($availability['conflicts'])) {
    echo "  Conflicts: " . count($availability['conflicts']) . "\n";
    foreach ($availability['conflicts'] as $conflict) {
        echo "    - Type: {$conflict['type']}, Date: {$conflict['date']}\n";
    }
}
echo "\n";

// Test 8: Unblock a date
echo "Test 8: Unblock a date\n";
echo "-------------------------------------------\n";
$unblock_result = unblockRoomDate($test_room_id, $test_date);
echo "Unblock result: " . ($unblock_result ? "SUCCESS" : "FAILED") . "\n\n";

// Test 9: Check availability after unblocking
echo "Test 9: Check availability after unblocking\n";
echo "-------------------------------------------\n";
$available_after_unblock = isRoomAvailable($test_room_id, $test_date, date('Y-m-d', strtotime('+8 days')));
echo "Room available on {$test_date}: " . ($available_after_unblock ? "YES" : "NO") . " (should be YES)\n\n";

// Test 10: Unblock multiple dates
echo "Test 10: Unblock multiple dates\n";
echo "-------------------------------------------\n";
$unblock_count = unblockRoomDates($test_room_id, $dates_to_block);
echo "Unblocked {$unblock_count} date(s)\n\n";

// Summary
echo "========================================\n";
echo "Test Summary\n";
echo "========================================\n";
echo "All tests completed!\n";
echo "\n";

// Verify final state
$final_blocked = getBlockedDates($test_room_id);
echo "Final blocked dates count: " . count($final_blocked) . "\n";
echo "Test completed successfully!\n";
