# 🔗 Booking System Integration Guide

## Quick Links to Add

Add these links to your navigation and room listings to direct guests to the booking system.

---

## 1. Main Navigation

Add to your header/navbar (typically in `includes/header.php` or `index.php`):

```html
<a href="booking.php" class="btn btn-primary">
  <i class="fas fa-calendar-check"></i> Book Now
</a>
```

---

## 2. Admin Panel Link

Add to your admin/staff area:

```html
<a href="admin/login.php" class="btn btn-secondary">
  <i class="fas fa-lock"></i> Admin Login
</a>
```

---

## 3. Room Listing Integration

In **[rooms-showcase.php](rooms-showcase.php)**, add booking button to each room card:

```php
<?php foreach ($rooms as $room): ?>
<div class="room-card">
    <h3><?php echo $room['name']; ?></h3>
    <p><?php echo $room['short_description']; ?></p>
    <p class="price">K<?php echo number_format($room['price_per_night'], 0); ?>/night</p>
    
    <!-- Add this button -->
    <a href="booking.php?room=<?php echo $room['id']; ?>" class="btn btn-gold">
        Reserve This Room
    </a>
</div>
<?php endforeach; ?>
```

---

## 4. Direct Room Booking

Link directly to specific rooms by passing room ID:

```html
<!-- Presidential Suite -->
<a href="booking.php?room=1">Book Presidential Suite</a>

<!-- Executive Suite -->
<a href="booking.php?room=2">Book Executive Suite</a>

<!-- Family Suite -->
<a href="booking.php?room=3">Book Family Suite</a>

<!-- Deluxe Suite -->
<a href="booking.php?room=4">Book Deluxe Suite</a>

<!-- Superior Room -->
<a href="booking.php?room=5">Book Superior Room</a>

<!-- Standard Room -->
<a href="booking.php?room=6">Book Standard Room</a>
```

---

## 5. Enhance Booking Form

Optional: Auto-select room if passed via URL in `booking.php`:

```javascript
// After your form loads, add this to auto-select room from URL
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const roomId = urlParams.get('room');
    
    if (roomId) {
        const roomRadio = document.querySelector(`input[value="${roomId}"]`);
        if (roomRadio) {
            roomRadio.click();
            roomRadio.closest('.room-option').classList.add('selected');
            updateSummary(); // Trigger summary update
        }
    }
});
```

---

## 6. Sidebar/Footer CTA

Add prominent call-to-action:

```html
<div class="booking-cta">
    <h3>Ready to Book?</h3>
    <p>Reserve your luxury stay at Liwonde Sun Hotel</p>
    <a href="booking.php" class="btn btn-gold btn-lg">
        Book Your Room Now
    </a>
</div>
```

---

## 7. Email/Marketing

Include booking link in emails:

```
Book now: https://liwondesunhotel.com/booking.php
Presidential Suite: https://liwondesunhotel.com/booking.php?room=1
```

---

## Current Integration Status

| Page | Integration | Status |
|------|-------------|--------|
| booking.php | Guest form | ✅ Ready |
| booking-confirmation.php | Confirmation | ✅ Ready |
| admin/login.php | Staff login | ✅ Ready |
| admin/dashboard.php | Dashboard | ✅ Ready |
| admin/booking-details.php | Manage bookings | ✅ Ready |

---

## Testing Checklist

- [ ] Can view booking.php without errors
- [ ] Can select a room in the form
- [ ] Can pick valid dates
- [ ] Can enter guest info
- [ ] Can submit booking
- [ ] Get booking reference on confirmation page
- [ ] Can login as receptionist
- [ ] Can see dashboard with bookings
- [ ] Can click "View" on a booking
- [ ] Can update booking status
- [ ] Can add notes to booking

---

## Troubleshooting

**Booking form shows no rooms?**
- Check `rooms` table has `is_active = 1` records
- Verify database connection working

**Can't login?**
- Use password-reset.php to reset credentials
- Check `admin_users` table has data

**Booking not creating?**
- Check browser console for JavaScript errors
- Verify `bookings` table exists and is empty
- Check file permissions on booking.php

---

Ready to go live! 🚀
