# Quick Start Guide - Liwonde Sun Hotel Integration

Get your booking widget live in 5 minutes!

---

## Step 1: Choose Your Integration Method

| Method | Best For | File | Difficulty |
|---------|-----------|-------|-----------|
| **Iframe** | Quick setup, no coding | `iframe-embed.html` | ⭐ Very Easy |
| **Popup Modal** | Clean page, popup booking | `popup-modal.html` | ⭐ Easy |
| **JavaScript API** | Full control, custom UI | `javascript-api.html` | ⭐⭐ Easy |
| **PHP Integration** | Server-side integration | `php-integration.php` | ⭐⭐ Medium |

---

## Step 2: Update Configuration

Open your chosen file and find the `CONFIG` section. Update these values:

```javascript
const CONFIG = {
    baseUrl: 'https://liwondesunhotel.com/api/',  // Your production URL
    apiKey: 'YOUR_API_KEY_HERE',               // API key from hotel
    bookingUrl: 'https://liwondesunhotel.com/booking.php',  // Booking page URL
    partnerName: 'YOUR_COMPANY_NAME'            // Your company name
};
```

### Getting Your API Key

Contact Liwonde Sun Hotel to get your API key:
- Email: info@liwondesunhotel.com
- Website: https://liwondesunhotel.com

---

## Step 3: Deploy to Your Website

### Option A: Simple Iframe (Recommended)

1. Open [`iframe-embed.html`](iframe-embed.html)
2. Replace `YOUR_PARTNER_NAME` with your company name
3. Replace `https://liwondesunhotel.com` with production URL
4. Copy the entire file content to your website
5. Done!

**That's it - just one file to upload!**

### Option B: Popup Modal

1. Open [`popup-modal.html`](popup-modal.html)
2. Update `CONFIG` values
3. Copy to your website
4. Add a "Book Now" button that calls `openBookingModal()`

### Option C: JavaScript API

1. Open [`javascript-api.html`](javascript-api.html)
2. Update `CONFIG` values
3. Copy to your website
4. Customize the UI as needed

### Option D: PHP Integration

1. Open [`php-integration.php`](php-integration.php)
2. Update `define()` statements at the top
3. Upload to your PHP server
4. Test the integration

---

## Step 4: Test Your Integration

1. Open your website in a browser
2. Click "Book Now" or view rooms
3. Try checking availability
4. Complete a test booking
5. Verify confirmation appears

---

## URL Parameters (Optional)

You can pre-fill data by adding parameters to the booking URL:

| Parameter | Description | Example |
|-----------|-------------|---------|
| `?partner=NAME` | Track which partner sent booking | `?partner=TravelMalawi` |
| `?room_id=X` | Pre-select a room | `?room_id=1` |
| `?check_in=YYYY-MM-DD` | Pre-fill check-in date | `?check_in=2026-03-01` |
| `?check_out=YYYY-MM-DD` | Pre-fill check-out date | `?check_out=2026-03-03` |

### Example with Pre-filled Data:

```html
<iframe 
    src="https://liwondesunhotel.com/booking.php?partner=TravelMalawi&room_id=2&check_in=2026-03-05&check_out=2026-03-07" 
    width="100%" 
    height="900px" 
    frameborder="0">
</iframe>
```

---

## Troubleshooting

### Issue: "Invalid API key"
**Solution:** Contact Liwonde Sun Hotel to get a valid API key

### Issue: "Room not available"
**Solution:** Try different dates further in the future

### Issue: "Network error"
**Solution:** Check that your website can reach `https://liwondesunhotel.com`

### Issue: Iframe not loading
**Solution:** 
- Check the URL is correct
- Verify your production URL is accessible
- Check browser console for errors

---

## Production Checklist

- [ ] Get API key from Liwonde Sun Hotel
- [ ] Update CONFIG with production URL
- [ ] Update CONFIG with API key
- [ ] Update CONFIG with partner name
- [ ] Upload files to your website
- [ ] Test booking flow
- [ ] Verify confirmation emails work
- [ ] Deploy to production

---

## Need Help?

- **Email:** info@liwondesunhotel.com
- **Website:** https://liwondesunhotel.com
- **Documentation:** See [`README.md`](README.md) for full details

---

## Quick Copy-Paste (Iframe Method)

Just replace `YOUR_PARTNER_NAME` and paste this on your website:

```html
<div class="liwonde-booking-widget">
    <h2>Book Your Stay</h2>
    <iframe 
        src="https://liwondesunhotel.com/booking.php?partner=YOUR_PARTNER_NAME" 
        width="100%" 
        height="900px" 
        frameborder="0" 
        style="border: none; border-radius: 8px;">
    </iframe>
</div>

<style>
    .liwonde-booking-widget {
        max-width: 800px;
        margin: 40px auto;
        text-align: center;
    }
    .liwonde-booking-widget h2 {
        color: #2c5aa0;
        margin-bottom: 20px;
    }
</style>
```

**That's all you need!** 🎉
