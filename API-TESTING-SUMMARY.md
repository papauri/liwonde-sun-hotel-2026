# Liwonde Sun Hotel Booking API - Testing Summary

## Overview

This document provides a complete guide for testing the booking API step by step. The API allows external websites to integrate with the Liwonde Sun Hotel booking system.

## Quick Start

### 1. Setup API Tables and Key

```bash
# Run this command to set up the API tables and test key
php api/verify-api-key.php
```

This will:
- Create necessary database tables (api_keys, api_usage_logs)
- Insert/verify the test API key: `test_key_12345`
- Set up all required permissions

### 2. Run Automated Tests

```bash
# Run all API tests
php api/test-api-cli.php
```

This will test all endpoints automatically and show a pass/fail report.

### 3. Test with cURL Commands

Once setup is complete, you can test individual endpoints using cURL:

#### Test 1: API Information
```bash
curl -H "X-API-Key: test_key_12345" http://localhost/api/
```

#### Test 2: Get Available Rooms
```bash
curl -H "X-API-Key: test_key_12345" http://localhost/api/rooms
```

#### Test 3: Check Availability
```bash
curl -H "X-API-Key: test_key_12345" \
  "http://localhost/api/availability?room_id=1&check_in=2026-03-01&check_out=2026-03-03"
```

#### Test 4: Create Booking
```bash
curl -X POST \
  -H "Content-Type: application/json" \
  -H "X-API-Key: test_key_12345" \
  -d '{
    "room_id": 1,
    "guest_name": "John Test",
    "guest_email": "john.test@example.com",
    "guest_phone": "+265123456789",
    "guest_country": "Malawi",
    "guest_address": "123 Test Street",
    "number_of_guests": 2,
    "check_in_date": "2026-03-05",
    "check_out_date": "2026-03-07",
    "special_requests": "Test booking"
  }' \
  http://localhost/api/bookings
```

#### Test 5: Get Booking Details
```bash
curl -H "X-API-Key: test_key_12345" \
  "http://localhost/api/bookings?id=LSH2026005"
```

## API Endpoints

### 1. GET /api/
Get API information and available endpoints.

**Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": {
    "api": "Liwonde Sun Hotel Booking API",
    "version": "1.0.0",
    "endpoints": {
      "GET /api/rooms": "List available rooms",
      "GET /api/availability": "Check room availability",
      "POST /api/bookings": "Create a new booking",
      "GET /api/bookings?id={id}": "Get booking status"
    }
  }
}
```

### 2. GET /api/rooms
List all available rooms.

**Query Parameters (optional):**
- `active` (boolean): Filter by active status
- `featured` (boolean): Filter featured rooms only
- `limit` (integer): Number of rooms to return
- `offset` (integer): Pagination offset

**Response:**
```json
{
  "success": true,
  "message": "Rooms retrieved successfully",
  "data": {
    "rooms": [
      {
        "id": 1,
        "name": "Presidential Suite",
        "slug": "presidential-suite",
        "description": "Ultimate luxury...",
        "price_per_night": 50000.00,
        "price_per_night_formatted": "MWK 50,000",
        "size_sqm": 110,
        "max_guests": 4,
        "rooms_available": 5,
        "total_rooms": 5,
        "bed_type": "King Bed",
        "image_url": "images/rooms/presidential-suite.jpg",
        "badge": "Luxury",
        "amenities": ["King Bed", "Private Terrace", "Jacuzzi"],
        "is_featured": true,
        "is_active": true
      }
    ],
    "pagination": {
      "total": 6,
      "limit": 10,
      "offset": 0
    }
  }
}
```

### 3. GET /api/availability
Check if a room is available for specific dates.

**Required Parameters:**
- `room_id` (integer): Room ID to check
- `check_in` (string): Check-in date (YYYY-MM-DD)
- `check_out` (string): Check-out date (YYYY-MM-DD)

**Optional Parameters:**
- `number_of_guests` (integer): Number of guests

**Response (Available):**
```json
{
  "success": true,
  "message": "Room available",
  "data": {
    "available": true,
    "room": {
      "id": 1,
      "name": "Presidential Suite",
      "price_per_night": 50000.00,
      "max_guests": 4,
      "rooms_available": 5
    },
    "dates": {
      "check_in": "2026-03-01",
      "check_out": "2026-03-03",
      "nights": 2
    },
    "pricing": {
      "price_per_night": 50000.00,
      "total": 100000.00,
      "currency": "MWK"
    },
    "conflicts": [],
    "message": "Room is available for your selected dates"
  }
}
```

### 4. POST /api/bookings
Create a new booking.

**Required Fields:**
- `room_id` (integer): Room ID
- `guest_name` (string): Guest's full name
- `guest_email` (string): Guest's email address
- `guest_phone` (string): Guest's phone number
- `number_of_guests` (integer): Number of guests
- `check_in_date` (string): Check-in date (YYYY-MM-DD)
- `check_out_date` (string): Check-out date (YYYY-MM-DD)

**Optional Fields:**
- `guest_country` (string): Guest's country
- `guest_address` (string): Guest's address
- `special_requests` (string): Special requests or notes

**Response:**
```json
{
  "success": true,
  "message": "Booking created successfully",
  "data": {
    "booking": {
      "id": 5,
      "booking_reference": "LSH2026005",
      "status": "pending",
      "room": {
        "id": 1,
        "name": "Presidential Suite",
        "price_per_night": 50000.00,
        "max_guests": 4
      },
      "guest": {
        "name": "John Test",
        "email": "john.test@example.com",
        "phone": "+265123456789",
        "country": "Malawi",
        "address": "123 Test Street"
      },
      "dates": {
        "check_in": "2026-03-05",
        "check_out": "2026-03-07",
        "nights": 2
      },
      "pricing": {
        "total_amount": 100000.00,
        "currency": "MWK"
      },
      "special_requests": "Test booking",
      "created_at": "2026-01-28 01:30:00"
    },
    "notifications": {
      "guest_email_sent": true,
      "admin_email_sent": true,
      "guest_email_message": "Email sent successfully via SMTP",
      "admin_email_message": "Email sent successfully via SMTP"
    },
    "next_steps": {
      "booking_status": "Your booking has been created successfully and is now in the system.",
      "email_notification": "A confirmation email has been sent to guest@example.com OR Email notification pending - System will send confirmation once email service is configured",
      "payment": "Payment will be made at the hotel upon arrival. We accept cash only.",
      "confirmation": "Your booking reference is LSH2026005. Keep this reference for check-in.",
      "contact": "If you have any questions, please contact us at book@liwondesunhotel.com"
    }
  }
}
```

### 5. GET /api/bookings?id={id}
Retrieve booking details by booking ID or reference.

**Parameters:**
- `id` (required): Booking ID or reference number

**Response:**
```json
{
  "success": true,
  "message": "Booking retrieved successfully",
  "data": {
    "booking": {
      "id": 5,
      "booking_reference": "LSH2026005",
      "status": "pending",
      "payment_status": "unpaid",
      "room": {
        "id": 1,
        "name": "Presidential Suite",
        "price_per_night": 50000.00,
        "max_guests": 4
      },
      "guest": {
        "name": "John Test",
        "email": "john.test@example.com",
        "phone": "+265123456789",
        "country": "Malawi",
        "address": "123 Test Street"
      },
      "dates": {
        "check_in": "2026-03-05",
        "check_out": "2026-03-07",
        "nights": 2
      },
      "details": {
        "number_of_guests": 2,
        "special_requests": "Test booking"
      },
      "pricing": {
        "total_amount": 100000.00,
        "currency": "MWK"
      },
      "timestamps": {
        "created_at": "2026-01-28 01:30:00",
        "updated_at": "2026-01-28 01:30:00"
      }
    },
    "actions": {
      "can_cancel": true,
      "can_check_in": false,
      "can_check_out": false
    },
    "contact": {
      "hotel_name": "Liwonde Sun Hotel",
      "phone": "+265 123 456 789",
      "email": "book@liwondesunhotel.com",
      "address": "Liwonde National Park Road, Malawi"
    }
  }
}
```

## Authentication

All API requests require an API key sent in the `X-API-Key` header:

```http
X-API-Key: your_api_key_here
```

Alternatively, you can pass the API key as a query parameter:

```
https://yourdomain.com/api/rooms?api_key=your_api_key_here
```

## Error Responses

### Invalid API Key (401)
```json
{
  "success": false,
  "error": "Invalid API key",
  "code": 401,
  "timestamp": "2026-01-28T01:30:00+00:00"
}
```

### Validation Error (422)
```json
{
  "success": false,
  "error": "Validation failed",
  "details": {
    "guest_email": "Invalid email address",
    "check_in_date": "Check-in date cannot be in the past"
  },
  "code": 422,
  "timestamp": "2026-01-28T01:30:00+00:00"
}
```

### Room Not Available (409)
```json
{
  "success": false,
  "error": "This room is not available for the selected dates. Please choose different dates.",
  "code": 409,
  "timestamp": "2026-01-28T01:30:00+00:00"
}
```

## Testing Checklist

### Setup
- [ ] Run `php api/verify-api-key.php` to set up API
- [ ] Verify API key is working
- [ ] Check that rooms exist in database
- [ ] Ensure PHP/web server is running

### Functionality Tests
- [ ] Test API info endpoint (`GET /api/`)
- [ ] Test get rooms endpoint (`GET /api/rooms`)
- [ ] Test availability check (`GET /api/availability`)
- [ ] Test booking creation (`POST /api/bookings`)
- [ ] Test booking retrieval (`GET /api/bookings?id={id}`)

### Error Handling Tests
- [ ] Test with invalid API key (should return 401)
- [ ] Test without API key (should return 401)
- [ ] Test with invalid email (should return 422)
- [ ] Test with invalid dates (should return 422)
- [ ] Test unavailable room (should return 409)

## Files Created for Testing

1. **api/step-by-step-testing-guide.md** - Detailed testing guide with 10 test cases
2. **api/test-api-cli.php** - Automated CLI test script
3. **api/setup-api-key.php** - Script to set up API tables and key
4. **api/verify-api-key.php** - Script to verify and fix API key hash

## Common Issues and Solutions

### Issue: "Invalid API key"
**Solution:** Run `php api/verify-api-key.php` to fix the hash

### Issue: "Room not found"
**Solution:** Add rooms to the database using the admin panel

### Issue: "Room not available for dates"
**Solution:** Use dates further in the future (e.g., 30+ days ahead)

### Issue: Email sending fails
**Solution:** The booking will still work without email. Configure SMTP in admin panel if needed

### Issue: 500 Internal Server Error
**Solution:** Check PHP error logs in the `logs/` directory

## Integration Examples

### JavaScript Fetch Example
```javascript
const API_KEY = 'test_key_12345';
const API_BASE = 'http://localhost/api/';

// Get rooms
async function getRooms() {
  const response = await fetch(`${API_BASE}rooms`, {
    headers: {
      'X-API-Key': API_KEY
    }
  });
  
  const data = await response.json();
  return data;
}

// Create booking
async function createBooking(bookingData) {
  const response = await fetch(`${API_BASE}bookings`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-API-Key': API_KEY
    },
    body: JSON.stringify(bookingData)
  });
  
  const data = await response.json();
  return data;
}

// Usage
const booking = {
  room_id: 1,
  guest_name: 'John Doe',
  guest_email: 'john@example.com',
  guest_phone: '+265123456789',
  number_of_guests: 2,
  check_in_date: '2026-03-05',
  check_out_date: '2026-03-07'
};

createBooking(booking)
  .then(result => console.log(result))
  .catch(error => console.error(error));
```

### PHP cURL Example
```php
<?php
$apiKey = 'test_key_12345';
$apiBase = 'http://localhost/api/';

// Get rooms
$ch = curl_init($apiBase . 'rooms');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-API-Key: ' . $apiKey
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$rooms = json_decode($response, true);

// Create booking
$bookingData = [
    'room_id' => 1,
    'guest_name' => 'John Doe',
    'guest_email' => 'john@example.com',
    'guest_phone' => '+265123456789',
    'number_of_guests' => 2,
    'check_in_date' => '2026-03-05',
    'check_out_date' => '2026-03-07'
];

$ch = curl_init($apiBase . 'bookings');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($bookingData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-API-Key: ' . $apiKey
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$result = json_decode($response, true);
?>
```

## Next Steps

Once testing is complete and all tests pass:

1. ✅ Create production API keys in the admin panel (`admin/api-keys.php`)
2. ✅ Set appropriate rate limits for each client
3. ✅ Configure email notifications for bookings
4. ✅ Provide API documentation to external partners
5. ✅ Monitor API usage logs regularly
6. ✅ Set up webhooks if needed for real-time notifications

## Support

For issues or questions:
- API Documentation: `api/README.md`
- Step-by-Step Guide: `api/step-by-step-testing-guide.md`
- Database Setup: `Database/add-api-keys-table.sql`
- Error Logs: `logs/` directory
- Admin Panel: `admin/api-keys.php`

## Test API Key

**API Key:** `test_key_12345`
**Client:** Test Client
**Permissions:** rooms.read, availability.check, bookings.create, bookings.read
**Rate Limit:** 1000 calls/hour
**Status:** Active

---

**Last Updated:** January 28, 2026