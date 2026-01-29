# Liwonde Sun Hotel - Client Integration Guide

This folder contains all implementation options for integrating Liwonde Sun Hotel's booking system into your website.

## Quick Start

Choose integration method that best fits your needs:

| Method | Difficulty | Best For | File |
|---------|-------------|-----------|-------|
| | **Iframe Embed** | ⭐ Very Easy | Quick setup, no coding | `iframe-embed.html` |
| | **JavaScript API** | ⭐⭐ Easy | Full control, custom UI | `javascript-api.html` |
| | **PHP Integration** | ⭐⭐ Medium | Server-side integration | `php-integration.php` |
| | **Popup Modal** | ⭐ Easy | Clean page, popup booking | `popup-modal.html` |

---

## Dynamic Site Settings

All integration files now support **dynamic site settings** fetched from live database. This means:

- **Hotel name** is pulled from `site_settings` table
- **Tagline** is pulled from `site_settings` table
- **Currency symbol** is pulled from `site_settings` table
- **Contact information** is pulled from `site_settings` table
- **No hardcoded values** - everything is dynamic

### How It Works

When the integration loads, it automatically calls the `/api/site-settings` endpoint to retrieve all configuration from the database. The settings are then used to:

- Update page titles and headings
- Display correct currency symbols
- Show accurate contact information
- Use correct booking URLs

### API Endpoint

```
GET /api/site-settings
Headers: X-API-Key: YOUR_API_KEY
```

### Response Structure

```json
{
  "success": true,
  "message": "Site settings retrieved successfully",
  "data": {
    "hotel": {
      "name": "Liwonde Sun Hotel",
      "tagline": "Where Luxury Meets Nature",
      "url": "https://liwondesunhotel.com",
      "logo": ""
    },
    "contact": {
      "phone_main": "+265 123 456 789",
      "phone_reservations": "+265 123 456 789",
      "email_main": "info@liwondesunhotel.com",
      "email_reservations": "reservations@liwondesunhotel.com",
      "address_line1": "Liwonde National Park",
      "address_line2": "",
      "address_country": "Malawi",
      "working_hours": "24/7 Available"
    },
    "booking": {
      "check_in_time": "2:00 PM",
      "check_out_time": "11:00 AM",
      "change_policy": ""
    },
    "currency": {
      "symbol": "MWK",
      "code": "MWK"
    },
    "social": {
      "facebook": "",
      "instagram": "",
      "twitter": "",
      "linkedin": ""
    },
    "legal": {
      "copyright_text": "2026 Liwonde Sun Hotel. All rights reserved."
    },
    "all_settings": { ... }
  }
}
```

---

## Configuration

Before implementing, update these values in code:

```javascript
const CONFIG = {
    baseUrl: 'https://liwondesunhotel.com/api/',  // Your production URL
    apiKey: 'YOUR_API_KEY_HERE',               // API key from hotel
    partnerName: 'YOUR_COMPANY_NAME'           // Your company name for tracking
};
```

**Note:** The `bookingUrl` is now automatically fetched from site settings, so you don't need to hardcode it.

---

## Getting Started

1. **Choose your integration method** from the options below
2. **Copy the code** to your website
3. **Update CONFIG** with your API key and URLs
4. **Test the integration** on your website
5. **Deploy to production**

---

## Support

For questions or issues:
- Contact: info@liwondesunhotel.com
- API Documentation: See `api/` folder
- Testing Guide: See `api/step-by-step-testing-guide.md`

---

## Security Notes

- **Never expose your API key** in client-side code for production
- **Use HTTPS** for all API calls in production
- **Validate all user input** before sending to the API
- **Handle errors gracefully** and show user-friendly messages

---

## License

This integration code is provided for use with Liwonde Sun Hotel's booking system only.
