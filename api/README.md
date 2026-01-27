# Liwonde Sun Hotel Booking API

RESTful API for external websites to integrate with the Liwonde Sun Hotel booking system.

## Base URL
```
https://yourdomain.com/api/
```

## Authentication
All API requests require an API key sent in the `X-API-Key` header.

```http
X-API-Key: your_api_key_here
```

Alternatively, you can pass the API key as a query parameter:
```
https://yourdomain.com/api/rooms?api_key=your_api_key_here
```

## Rate Limiting
- Default: 100 requests per hour per API key
- Can be increased for high-traffic clients
- 429 status code returned when limit exceeded

## Endpoints

### 1. Get Available Rooms
```http
GET /api/rooms
```

**Query Parameters:**
- `active` (boolean): Filter by active status (default: true)
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
        "description": "Ultimate luxury with private terrace...",
        "short_description": "Ultimate luxury with private terrace...",
        "price_per_night": 50000.00,
        "price_per_night_formatted": "MWK 50,000",
        "size_sqm": 110,
        "max_guests": 4,
        "rooms_available": 5,
        "total_rooms": 5,
        "bed_type": "King Bed",
        "image_url": "images/rooms/presidential-suite.jpg",
        "badge": "Luxury",
        "amenities": ["King Bed", "Private Terrace", "Jacuzzi", ...],
        "is_featured": true,
        "is_active": true,
        "display_order": 1,
        "currency": "MWK",
        "gallery": [...]
      }
    ],
    "pagination": {
      "total": 6,
      "limit": 10,
      "offset": 0,
      "has_more": false
    },
    "metadata": {
      "currency": "MWK",
      "currency_code": "MWK",
      "site_name": "Liwonde Sun Hotel"
    }
  }
}
```

### 2. Check Room Availability
```http
GET /api/availability
```

**Query Parameters:**
- `room_id` (required): Room ID to check
- `check_in` (required): Check-in date (YYYY-MM-DD)
- `check_out` (required): Check-out date (YYYY-MM-DD)
- `number_of_guests` (optional): Number of guests

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
      "check_in": "2026-02-01",
      "check_out": "2026-02-03",
      "nights": 2
    },
    "pricing": {
      "price_per_night": 50000.00,
      "total": 100000.00,
      "currency": "MWK",
      "currency_code": "MWK"
    },
    "conflicts": [],
    "message": "Room is available for your selected dates"
  }
}
```

**Response (Not Available):**
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
      "check_in": "2026-02-01",
      "check_out": "2026-02-03"
    },
    "conflicts": [
      {
        "booking_reference": "LSH2026001",
        "guest_name": "John Banda",
        "check_in_date": "2026-02-01",
        "check_out_date": "2026-02-03",
        "status": "confirmed"
      }
    ],
    "message": "This room is not available for the selected dates..."
  }
}
```

### 3. Create Booking
```http
POST /api/bookings
Content-Type: application/json
```

**Request Body:**
```json
{
  "room_id": 1,
  "guest_name": "John Doe",
  "guest_email": "john@example.com",
  "guest_phone": "+265123456789",
  "guest_country": "Malawi",
  "guest_address": "123 Street, City",
  "number_of_guests": 2,
  "check_in_date": "2026-02-01",
  "check_out_date": "2026-02-03",
  "special_requests": "Early check-in please"
}
```

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
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "+265123456789",
        "country": "Malawi",
        "address": "123 Street, City"
      },
      "dates": {
        "check_in": "2026-02-01",
        "check_out": "2026-02-03",
        "nights": 2
      },
      "pricing": {
        "total_amount": 100000.00,
        "currency": "MWK",
        "currency_code": "MWK"
      },
      "special_requests": "Early check-in please",
      "created_at": "2026-01-28 00:25:00"
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
      "contact": "If you have any questions, please contact us at book@liwondesunhotel.com"
    }
  }
}
```

### 4. Get Booking Details
```http
GET /api/bookings?id={id}
```

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
        "max_guests": 4,
        "image_url": "images/rooms/presidential-suite.jpg",
        "amenities": ["King Bed", "Private Terrace", "Jacuzzi", ...]
      },
      "guest": {
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "+265123456789",
        "country": "Malawi",
        "address": "123 Street, City"
      },
      "dates": {
        "check_in": "2026-02-01",
        "check_out": "2026-02-03",
        "nights": 2
      },
      "details": {
        "number_of_guests": 2,
        "special_requests": "Early check-in please"
      },
      "pricing": {
        "total_amount": 100000.00,
        "currency": "MWK",
        "currency_code": "MWK"
      },
      "timestamps": {
        "created_at": "2026-01-28 00:25:00",
        "updated_at": "2026-01-28 00:25:00"
      }
    },
    "actions": {
      "can_cancel": true,
      "can_check_in": false,
      "can_check_out": false,
      "cancellation_policy": "Cancellations up to 48 hours before arrival are free of charge."
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

## Error Responses

### Authentication Error (401)
```json
{
  "success": false,
  "error": "Invalid API key",
  "code": 401,
  "timestamp": "2026-01-28T00:25:00+00:00"
}
```

### Permission Error (403)
```json
{
  "success": false,
  "error": "Permission denied: bookings.create",
  "code": 403,
  "timestamp": "2026-01-28T00:25:00+00:00"
}
```

### Rate Limit Error (429)
```json
{
  "success": false,
  "error": "Rate limit exceeded. Please try again later.",
  "code": 429,
  "timestamp": "2026-01-28T00:25:00+00:00"
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
  "timestamp": "2026-01-28T00:25:00+00:00"
}
```

### Not Found Error (404)
```json
{
  "success": false,
  "error": "Booking not found",
  "code": 404,
  "timestamp": "2026-01-28T00:25:00+00:00"
}
```

## Integration Examples

### JavaScript Fetch Example
```javascript
const API_KEY = 'your_api_key_here';
const API_BASE = 'https://yourdomain.com/api/';

async function getRooms() {
  const response = await fetch(`${API_BASE}rooms`, {
    headers: {
      'X-API-Key': API_KEY
    }
  });
  
  const data = await response.json();
  return data;
}

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
```

### PHP cURL Example
```php
<?php
$apiKey = 'your_api_key_here';
$apiBase = 'https://yourdomain.com/api/';

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
    // ... other fields
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

## Webhook Support (Optional)
Contact admin to set up webhooks for:
- Booking confirmation notifications
- Booking status updates
- Payment status changes

## Support
For API key requests, rate limit increases, or technical support:
- Email: admin@liwondesunhotel.com
- Phone: +265 123 456 789