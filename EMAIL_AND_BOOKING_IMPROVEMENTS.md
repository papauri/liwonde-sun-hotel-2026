# Email and Booking System Improvements

## Overview
This document outlines the improvements made to the email and booking systems for Liwonde Sun Hotel.

---

## 1. Test Email System

### Purpose
A standalone test email interface that allows you to test email functionality without going through the booking process.

### File Created
- `test-email.php` - Test email interface

### How to Use
1. Navigate to `https://yourdomain.com/test-email.php` in your browser
2. Enter your email address
3. Select the type of email to test:
   - **Simple Test Email**: Basic test message to verify email system
   - **Booking Confirmation Email**: Tests the booking confirmation template
   - **Admin Notification Email**: Tests the admin notification template
4. Click "Send Test Email"
5. View the results showing success/failure status

### Features
- Clean, user-friendly interface
- Multiple email template testing options
- Displays detailed results and any errors
- Shows email configuration details
- Works in both development (preview mode) and production

### Development Mode
On localhost, the system creates email previews instead of sending real emails:
- Previews are saved to `logs/email-previews/` directory
- Each email has a timestamped HTML file
- You can open these files to view email templates

---

## 2. Advance Booking Restriction

### Purpose
Restrict bookings to a configurable number of days in advance (default: 30 days).

### Implementation Details

#### Server-Side Validation
Modified files:
- `config/database.php` - Added advance booking validation in `validateBookingData()` function
- `check-availability.php` - Added advance booking check in API

#### Client-Side Validation
Modified files:
- `booking.php` - Added `max` attribute to date inputs and helper text

#### Database Configuration
- `Database/add-advance-booking-setting.sql` - SQL script to add setting

### How It Works

1. **Configuration**: Admin sets maximum advance booking days via admin panel
2. **Form Validation**: Date pickers automatically restrict dates beyond the limit
3. **Server Validation**: Booking submissions are validated against the limit
4. **User Feedback**: Clear error messages if dates exceed the limit

### Default Behavior
- **Default Value**: 30 days (one month)
- **Minimum**: 1 day
- **Maximum**: 365 days (one year)
- **User Message**: "Bookings can only be made up to 30 days in advance. Please select an earlier check-in date."

---

## 3. Admin Configuration Interface

### File Created
- `admin/booking-settings.php` - Admin interface for booking configuration

### How to Use
1. Log in to admin panel
2. Navigate to `https://yourdomain.com/admin/booking-settings.php`
3. View current advance booking setting
4. Enter new value (1-365 days)
5. Click "Save Changes"

### Features
- Clean, intuitive interface
- Current setting display
- Input validation (1-365 days)
- Helpful information about how changes affect the site
- Success/error notifications

---

## Installation Instructions

### Step 1: Add Database Setting

Run the SQL script to add the configuration setting:

```bash
# Method 1: Direct SQL execution
mysql -u your_user -p your_database < Database/add-advance-booking-setting.sql

# Method 2: phpMyAdmin
# Open phpMyAdmin
# Select your database
# Go to SQL tab
# Paste the content of Database/add-advance-booking-setting.sql
# Click Go
```

**What this does:**
- Inserts a new setting `max_advance_booking_days` with value `30`
- Sets it as editable by admin
- Categorizes it under 'booking' group

### Step 2: Verify Installation

Check that the setting exists in the database:

```sql
SELECT * FROM site_settings WHERE setting_key = 'max_advance_booking_days';
```

Expected result:
- `setting_key`: max_advance_booking_days
- `setting_value`: 30
- `setting_group`: booking
- `display_name`: Maximum Advance Booking Days

### Step 3: Test Email System

1. Open `test-email.php` in your browser
2. Enter your email address
3. Send a test email
4. Verify you receive the email (or check preview on localhost)

### Step 4: Configure Advance Booking

1. Log in to admin panel
2. Go to `admin/booking-settings.php`
3. Adjust the advance booking days if needed
4. Test the booking form to verify the restriction works

---

## File Changes Summary

### New Files Created
- `test-email.php` - Email testing interface
- `admin/booking-settings.php` - Admin booking configuration
- `Database/add-advance-booking-setting.sql` - Database setup script
- `EMAIL_AND_BOOKING_IMPROVEMENTS.md` - This documentation

### Files Modified
- `config/database.php` - Added advance booking validation
- `check-availability.php` - Added advance booking check
- `booking.php` - Added max date attributes and helper text

---

## Testing Checklist

### Email System Testing
- [ ] Open test-email.php
- [ ] Send simple test email to your address
- [ ] Send booking confirmation test email
- [ ] Send admin notification test email
- [ ] Verify emails received (on production) or previews created (on localhost)

### Advance Booking Testing
- [ ] Run SQL script to add database setting
- [ ] Verify setting appears in database
- [ ] Open booking form
- [ ] Check that date picker has max date set
- [ ] Try selecting a date beyond the limit
- [ ] Verify helper text shows current limit
- [ ] Attempt to submit booking with invalid date
- [ ] Verify error message appears
- [ ] Submit valid booking within limit
- [ ] Confirm booking succeeds

### Admin Configuration Testing
- [ ] Log in to admin panel
- [ ] Open booking-settings.php
- [ ] View current setting (should be 30)
- [ ] Change to a different value (e.g., 60)
- [ ] Save changes
- [ ] Verify success message
- [ ] Go back to booking form
- [ ] Verify new limit is applied
- [ ] Change back to 30 days
- [ ] Save changes

---

## Troubleshooting

### Email Not Sending

**Problem**: Emails not being sent on localhost

**Solution**: This is expected behavior. On localhost, emails create preview files instead.
- Check `logs/email-previews/` directory
- Open HTML files to view email previews
- To test real emails, use production server

**Problem**: Emails failing on production

**Solution**: Check the following:
1. Verify SMTP password in `config/email-password.php` is correct
2. Check `config/email.php` SMTP settings
3. Review error logs: `logs/email-log.txt`
4. Ensure firewall allows SMTP connections

### Advance Booking Not Working

**Problem**: Date picker doesn't restrict dates

**Solution**: 
1. Verify database setting exists: `SELECT * FROM site_settings WHERE setting_key = 'max_advance_booking_days'`
2. If missing, run the SQL script again
3. Check PHP error logs for any errors
4. Clear browser cache and reload booking form

**Problem**: Can still book beyond the limit

**Solution**:
1. Check that `check-availability.php` was updated
2. Verify `getSetting('max_advance_booking_days', 30)` returns correct value
3. Ensure JavaScript is enabled in browser
4. Check browser console for JavaScript errors

### Admin Settings Not Saving

**Problem**: Changes not saving in admin panel

**Solution**:
1. Verify user is logged in as admin
2. Check database connection
3. Ensure `site_settings` table is writable
4. Check PHP error logs
5. Verify form is being submitted (check network tab in browser dev tools)

---

## Advanced Configuration

### Change Email Templates

Email templates are in `templates/emails/` directory:
- `booking-confirmation.php` - Guest booking confirmation
- `admin-new-booking.php` - Admin notification
- `booking-status-update.php` - Status change notification
- `checkin-reminder.php` - Check-in reminder

Modify these files to customize email content and design.

### SMTP Configuration

Update SMTP settings in `config/email.php`:
```php
$smtp_host = 'mail.promanaged-it.com';
$smtp_port = 465;
$smtp_username = 'info@promanaged-it.com';
$smtp_secure = 'ssl';
```

Update password in `config/email-password.php`:
```php
$email_smtp_password = 'your_actual_password';
```

### Customize Advance Booking Message

Change the error message in `config/database.php`:
```php
$errors['check_in_date'] = "Your custom message here with {$max_advance_days} days";
```

---

## Security Notes

### Test Email Page
- **Production**: Should be protected or removed in production
- **Recommendation**: Add IP whitelist or authentication
- **Alternative**: Delete test-email.php after testing

### Admin Panel
- Requires admin authentication
- Settings can only be changed by logged-in admin
- Input validation prevents invalid values

### Booking Validation
- Server-side validation is always enforced
- Client-side validation improves UX but cannot be bypassed
- Date range limits prevent booking manipulation

---

## Performance Considerations

### Email System
- Caching: Settings are cached in `$_SITE_SETTINGS`
- Performance: Minimal impact on booking process
- Async: Emails are sent synchronously but don't block booking

### Advance Booking
- Database: Single query to fetch setting
- Caching: Setting cached after first load
- Performance: Negligible impact on booking form

---

## Future Enhancements

### Potential Improvements
1. Add email queue system for bulk sending
2. Implement email template editor in admin panel
3. Add advance booking calendar view for admin
4. Allow different advance limits per room type
5. Add email statistics (sent, opened, clicked)
6. Implement email retry logic for failed sends

### Integration Points
- SMS notifications for urgent updates
- Push notifications via websockets
- Calendar export (iCal, Google Calendar)
- Third-party booking platforms integration

---

## Support

For issues or questions:
1. Check error logs: `logs/email-log.txt`
2. Review PHP error logs
3. Verify database settings
4. Test with browser developer tools
5. Check network requests in dev tools

---

## Version History

### Version 1.0 (Current)
- Added test email interface
- Implemented advance booking restriction
- Created admin configuration panel
- Added database setting for configurable limits
- Updated booking form validation
- Enhanced availability checking API

---

## License
This code is part of the Liwonde Sun Hotel booking system.