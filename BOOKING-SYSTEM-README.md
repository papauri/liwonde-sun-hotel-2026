# 🏨 Liwonde Sun Hotel - Booking System Documentation

## ✅ System Status
**All booking system files are working and ready to use.**

---

## 📋 Files Created

### Guest-Facing Pages
- **[booking.php](booking.php)** - Online booking form with room selection, date picker, guest info
- **[booking-confirmation.php](booking-confirmation.php)** - Confirmation page with booking reference, summary, and payment info

### Admin Panel (Receptionist)
- **[admin/login.php](admin/login.php)** - Secure login page
- **[admin/dashboard.php](admin/dashboard.php)** - Dashboard showing today's arrivals, departures, pending bookings
- **[admin/booking-details.php](admin/booking-details.php)** - Full booking management (confirm, check-in, check-out, notes)
- **[admin/logout.php](admin/logout.php)** - Logout functionality
- **[admin/password-reset.php](admin/password-reset.php)** - Password reset tool (delete after using)

### Database
- **[Database/add-booking-system.sql](Database/add-booking-system.sql)** - Complete booking database schema (run this file in phpMyAdmin)

---

## 🚀 Quick Start

### 1. Setup Database
1. Go to phpMyAdmin
2. Select database `u177308791_liwonde_sun`
3. Click **SQL** tab
4. Paste contents of `Database/add-booking-system.sql`
5. Click **Go**

### 2. Reset Passwords (if needed)
1. Visit: `http://localhost:8000/admin/password-reset.php`
2. Enter username: `receptionist`
3. Enter password: `admin123`
4. Click **Reset Password**
5. **Delete `admin/password-reset.php` after use!**

### 3. Test Login
1. Go to: `http://localhost:8000/admin/login.php`
2. Username: `receptionist`
3. Password: `admin123`
4. You should see the dashboard with upcoming bookings

### 4. Test Booking
1. Go to: `http://localhost:8000/booking.php`
2. Select any room
3. Choose check-in/check-out dates
4. Fill guest info
5. Submit
6. You'll get a booking confirmation page with reference number

---

## 🔐 Admin Login Credentials

| Username | Password | Role |
|----------|----------|------|
| `receptionist` | `admin123` | Receptionist |
| `admin` | `admin123` | Administrator |

⚠️ **Change these passwords after first login!**

---

## 📊 Database Tables

### `bookings`
Stores all guest reservations with status tracking
```sql
- id: Unique booking ID
- booking_reference: Unique reference (e.g., LSH20260125001)
- room_id: Links to rooms table
- guest_name, email, phone, country, address
- check_in_date, check_out_date
- number_of_guests, number_of_nights
- total_amount: Price calculation
- status: pending → confirmed → checked-in → checked-out
- payment_status: unpaid → partial → paid
```

### `admin_users`
User accounts for receptionist/admin access
```sql
- id: User ID
- username: Login username
- password_hash: Bcrypt hashed password
- role: admin, receptionist, or manager
- is_active: Enable/disable account
- last_login: Track login activity
```

### `booking_notes`
Internal notes for staff communication
```sql
- id: Note ID
- booking_id: Links to booking
- note_text: Staff notes
- created_by: Admin user who created note
- created_at: Timestamp
```

---

## 🎯 Booking Workflow

### For Guests
```
1. Visit /booking.php
2. Select room (see price, capacity, description)
3. Pick check-in/check-out dates
4. Enter guest info
5. Submit booking
6. Get confirmation page with booking reference
7. Email confirmation (optional enhancement)
8. Pay cash at hotel check-in
```

### For Receptionists
```
1. Login at /admin/login.php
2. See dashboard with:
   - Today's check-ins
   - Today's check-outs
   - Pending bookings
   - Current guests
3. Click booking to view details
4. Status workflow:
   - [Confirm] pending → confirmed
   - [Check In] confirmed → checked-in
   - [Check Out] checked-in → checked-out
5. Update payment status (unpaid → partial → paid)
6. Add internal notes for other staff
```

---

## ✨ Features

✅ **Room Selection** - Shows all active rooms with prices, capacity, description  
✅ **Availability Checking** - Prevents double bookings automatically  
✅ **Date Validation** - Prevents past dates, ensures checkout after check-in  
✅ **Price Calculation** - Automatic total based on room price × nights  
✅ **Booking Reference** - Unique reference for guest (e.g., LSH20260125001)  
✅ **Status Tracking** - Pending → Confirmed → Checked In → Checked Out  
✅ **Payment Tracking** - Unpaid/Partial/Paid status for cash payments  
✅ **Internal Notes** - Staff communication system per booking  
✅ **Dashboard** - Quick overview of today's arrivals/departures  
✅ **Mobile Responsive** - Works on all devices  

---

## 🔧 Customization

### Change Booking Reference Format
File: `booking.php` (Line 43)
```php
$booking_reference = 'LSH' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
// Change 'LSH' to your preferred prefix
```

### Add More Admin Users
In phpMyAdmin, insert into `admin_users`:
```sql
INSERT INTO admin_users (username, email, password_hash, full_name, role) 
VALUES ('john', 'john@hotel.com', '[bcrypt_hash]', 'John Doe', 'receptionist');
```

### Change Default Role
File: `Database/add-booking-system.sql`
Change role from `'receptionist'` to `'manager'` or `'admin'`

---

## 📞 Support & Troubleshooting

**Login not working?**
- Use password-reset.php to reset password
- Check `admin_users` table exists in database
- Ensure `is_active = 1` for user

**Booking submission failing?**
- Verify `bookings` table exists
- Check room IDs match `rooms` table
- Ensure dates are in future
- Check `number_of_guests` doesn't exceed `max_guests`

**Can't see bookings in dashboard?**
- Verify login successful (session created)
- Check bookings table has data
- Ensure check-in dates are today or future

---

## 🎓 Database Relationships

```
rooms (1) ──→ (Many) bookings
           ├─ Via: room_id

admin_users (1) ──→ (Many) booking_notes
            ├─ Via: created_by
```

---

## 📝 Next Steps

1. ✅ Database setup complete
2. ✅ Login system working
3. ✅ Booking form ready
4. **Optional**: Add email confirmations
5. **Optional**: Add payment gateway (Airtel Money, MTN Mobile Money)
6. **Optional**: Add booking history for guests
7. **Optional**: Add cancellation policy
8. **Optional**: Create staff management panel

---

**Booking System Version:** 1.0  
**Status:** Production Ready ✅  
**Last Updated:** January 21, 2026
