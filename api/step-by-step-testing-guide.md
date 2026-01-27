# Step-by-Step Booking API Testing Guide

This guide will walk you through testing the Liwonde Sun Hotel Booking API step by step.

## Prerequisites

Before testing, ensure you have:
1. ✅ Database tables created for the API
2. ✅ At least one active room in the database
3. ✅ A valid API key
4. ✅ PHP server running (or web server)

## Setup Phase

### Step 1: Verify API Tables Exist

Run this command to check if API tables are set up:

```bash
php setup-api-tables.php
```

Expected output:
- ✅ api_keys table created
- ✅ api_usage_logs table created
- ✅ Sample API key inserted: `test_key_12345`

### Step 2: Check API Key

Open your database and verify the API key exists:

```sql
SELECT * FROM api_keys WHERE api_key_hash LIKE '%test_key_12345%';
```

Expected result:
- One record with client_name = "Test Client"
- is_active = 1
- permissions includes: rooms.read, availability.check, bookings.create, bookings.read

### Step 3: Verify Rooms Exist

Check that you have rooms in the database:

```sql
SELECT id, name, price_per_night, is_active, rooms_available 
FROM rooms 
WHERE is_active = 1 
LIMIT 5;
```

Expected result:
- At least 1-3 active rooms listed
- Note the room IDs for testing

---

## Testing Phase

### Test 1: API Information Endpoint

**Endpoint:** `GET /api/`

**Purpose:** Verify the API is responding correctly

**Command:**
```bash
curl -H "X-API-Key: test_key_12345" http://localhost/api/
```

**Expected Response:**
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

**If this fails:**
- Check if API tables exist
- Verify API key is valid
- Check PHP error logs

---

### Test 2: Get Available Rooms

**Endpoint:** `GET /api/rooms`

**Purpose:** Retrieve list of available rooms

**Command:**
```bash
curl -H "X-API-Key: test_key_12345" http://localhost/api/rooms
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Rooms retrieved successfully",
  "data": {
    "rooms": [
      {
        "id": 1,
        "name": "Presidential Suite",
        "price_per_night": 50000.00,
        "max_guests": 4,
        "rooms_available": 5,
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

**If this fails:**
- Check if rooms table has data
- Verify rooms have is_active = 1
- Check database connection

---

### Test 3: Check Room Availability (Available)

**Endpoint:** `GET /api/availability`

**Purpose:** Check if a room is available for specific dates

**Command:**
```bash
curl -H "X-API-Key: test_key_12345" \
  "http://localhost/api/availability?room_id=1&check_in=2026-03-01&check_out=2026-03-03"
```

**Expected Response (Available):**
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

**If room is not available:**
- Try different dates (further in future)
- Check if room has existing bookings

---

### Test 4: Check Room Availability (Not Available)

**Purpose:** Test when room is already booked

**Command:**
```bash
curl -H "X-API-Key: test_key_12345" \
  "http://localhost/api/availability?room_id=1&check_in=2026-01-01&check_out=2026-01-03"
```

**Expected Response (Not Available):**
```json
{
  "success": true,
  "message": "Room not available",
  "data": {
    "available": false,
    "room": {
      "id": 1,
      "name": "Presidential Suite"
    },
    "dates": {
      "check_in": "2026-01-01",
      "check_out": "2026-01-03"
    },
    "conflicts": [],
    "message": "This room is not available for the selected dates..."
  }
}
```

---

### Test 5: Create a Booking

**Endpoint:** `POST /api/bookings`

**Purpose:** Create a new booking

**Command:**
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
    "special_requests": "API Test Booking - Please Ignore"
  }' \
  http://localhost/api/bookings
```

**Expected Response:**
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
      "special_requests": "API Test Booking - Please Ignore",
      "created_at": "2026-01-28 01:30:00"
    },
    "notifications": {
      "guest_email_sent": true,
      "admin_email_sent": true,
      "guest_email_message": "Email sent successfully via SMTP",
      "admin_email_message": "Email sent successfully via SMTP"
    },
    "next_steps": {
      "payment": "Payment will be made at the hotel upon arrival...",
      "confirmation": "Your booking is pending confirmation...",
      "contact": "If you have any questions, please contact us at..."
    }
  }
}
```

**Save the booking reference from the response** (e.g., `LSH2026005`) for the next test.

**If this fails:**
- Check if room is available for those dates
- Verify email configuration (can work without SMTP)
- Check required fields are provided

---

### Test 6: Get Booking Details

**Endpoint:** `GET /api/bookings?id={id}`

**Purpose:** Retrieve details of a specific booking

**Command:**
```bash
curl -H "X-API-Key: test_key_12345" \
  "http://localhost/api/bookings?id=LSH2026005"
```

**Expected Response:**
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
        "special_requests": "API Test Booking - Please Ignore"
      },
      "pricing": {
        "total_amount": 100000.00,
        "currency": "MWK"
      }
    },
    "actions": {
      "can_cancel": true,
      "can_check_in": false,
      "can_check_out": false
    }
  }
}
```

---

### Test 7: Error Handling - Invalid API Key

**Purpose:** Test authentication error

**Command:**
```bash
curl -H "X-API-Key: invalid_key_12345" http://localhost/api/rooms
```

**Expected Response:**
```json
{
  "success": false,
  "error": "Invalid API key",
  "code": 401,
  "timestamp": "2026-01-28T01:30:00+00:00"
}
```

---

### Test 8: Error Handling - Missing API Key

**Purpose:** Test missing authentication

**Command:**
```bash
curl http://localhost/api/rooms
```

**Expected Response:**
```json
{
  "success": false,
  "error": "API key is required",
  "code": 401,
  "timestamp": "2026-01-28T01:30:00+00:00"
}
```

---

### Test 9: Error Handling - Validation Error

**Purpose:** Test validation when creating booking with invalid data

**Command:**
```bash
curl -X POST \
  -H "Content-Type: application/json" \
  -H "X-API-Key: test_key_12345" \
  -d '{
    "room_id": 1,
    "guest_name": "Test",
    "guest_email": "invalid-email",
    "guest_phone": "+265123456789",
    "number_of_guests": 2,
    "check_in_date": "2026-03-05",
    "check_out_date": "2026-03-03"
  }' \
  http://localhost/api/bookings
```

**Expected Response:**
```json
{
  "success": false,
  "error": "Validation failed",
  "details": {
    "guest_email": "Invalid email address",
    "check_out_date": "Check-out date must be after check-in date"
  },
  "code": 422,
  "timestamp": "2026-01-28T01:30:00+00:00"
}
```

---

### Test 10: Error Handling - Room Not Available

**Purpose:** Test booking unavailable room

**Command:**
```bash
curl -X POST \
  -H "Content-Type: application/json" \
  -H "X-API-Key: test_key_12345" \
  -d '{
    "room_id": 1,
    "guest_name": "Test User",
    "guest_email": "test@example.com",
    "guest_phone": "+265123456789",
    "number_of_guests": 2,
    "check_in_date": "2026-03-05",
    "check_out_date": "2026-03-07"
  }' \
  http://localhost/api/bookings
```

**Expected Response:**
```json
{
  "success": false,
  "error": "This room is not available for the selected dates. Please choose different dates.",
  "code": 409,
  "timestamp": "2026-01-28T01:30:00+00:00"
}
```

---

## Using the Web Test Interface

For easier testing, you can use the web-based test script:

1. Open in browser: `http://localhost/api/test-api.php`
2. The script will automatically run all tests
3. Review the results on the page

---

## Testing Checklist

Use this checklist to track your testing progress:

### Setup
- [ ] API tables created successfully
- [ ] API key verified in database
- [ ] At least one active room exists
- [ ] PHP/web server is running

### Functionality Tests
- [ ] Test 1: API info endpoint works
- [ ] Test 2: Get rooms returns data
- [ ] Test 3: Check availability (available)
- [ ] Test 4: Check availability (not available)
- [ ] Test 5: Create booking successful
- [ ] Test 6: Get booking details works

### Error Handling Tests
- [ ] Test 7: Invalid API key rejected
- [ ] Test 8: Missing API key rejected
- [ ] Test 9: Validation errors work
- [ ] Test 10: Unavailable room rejected

---

## Common Issues and Solutions

### Issue: "Invalid API key"
**Solution:** Run `setup-api-tables.php` to create tables and insert test key

### Issue: "Room not found"
**Solution:** Add rooms to the database or use existing room IDs

### Issue: "Room not available for dates"
**Solution:** Use dates further in the future that aren't booked

### Issue: Email sending fails
**Solution:** Configure SMTP settings in admin or the booking will still work without email

### Issue: 500 Internal Server Error
**Solution:** Check PHP error logs in `logs/` directory

---

## Next Steps After Testing

Once all tests pass:

1. ✅ Create production API keys in admin panel
2. ✅ Set appropriate rate limits for each key
3. ✅ Configure email notifications
4. ✅ Provide API documentation to external partners
5. ✅ Monitor API usage logs regularly

---

## Quick Reference

**Base URL:** `http://localhost/api/`

**API Key:** `test_key_12345`

**Available Endpoints:**
- `GET /api/` - API info
- `GET /api/rooms` - List rooms
- `GET /api/availability` - Check availability
- `POST /api/bookings` - Create booking
- `GET /api/bookings?id={id}` - Get booking details

**Test Script:** `api/test-api.php`

---

## Support

For issues or questions:
- Check the API documentation: `api/README.md`
- Review database setup: `Database/add-api-keys-table.sql`
- View error logs: `logs/` directory