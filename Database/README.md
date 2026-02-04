# Database Folder

This folder contains SQL migration files and database backups for the Liwonde Sun Hotel website.

## 📁 Files

### 1. **p601229_hotels.sql**
- **Purpose:** Complete database backup/restore file
- **Contents:** Full database schema with all tables, indexes, constraints, and sample data
- **Last Updated:** February 4, 2026
- **Size:** ~500KB
- **Use When:**
  - Setting up a new development environment
  - Restoring database after major changes
  - Complete system backup

### 2. **add-booking-time-buffer-setting.sql**
- **Purpose:** Migration to add booking time buffer setting
- **Adds:** `booking_time_buffer_minutes` setting to `site_settings` table
- **Status:** ✅ Applied (Default: 60 minutes)
- **Description:** Sets minimum advance time required for bookings

### 3. **add-gym-inquiries-table.sql**
- **Purpose:** Migration to create gym inquiries functionality
- **Adds:** `gym_inquiries` table with fields for membership bookings
- **Status:** ✅ Applied
- **Description:** Enables gym membership inquiry tracking

## 🗂️ Cleanup History

**Date:** February 4, 2026

**Removed Files (6 unnecessary setup scripts):**
- `setup-gym-inquiries.php` - Redundant PHP setup script
- `create-gym-table.php` - Duplicate table creation script
- `diagnose-gym-table.php` - One-time diagnostic tool
- `verify-gym-inquiries.php` - One-time verification tool
- `check-site-settings-structure.php` - One-time structure check
- `setup-booking-buffer.php` - Redundant PHP setup script

**Reason:** These were one-time setup scripts that are no longer needed since:
- All tables have been created
- All migrations have been applied
- The functionality is now integrated into the main application

## 📊 Database Structure

### Key Tables
- **Rooms:** `rooms`, `room_blocked_dates`, `gallery`
- **Bookings:** `bookings`, `conference_inquiries`, `tentative_booking_log`
- **Payments:** `payments`, `invoices`
- **Reviews:** `reviews`, `review_responses`
- **Events:** `events` (with video support)
- **Gym:** `gym_inquiries`, `gym_packages`, `gym_classes`
- **Content:** `site_settings`, `hero_slides`, `facilities`

### Recent Enhancements
- ✅ Video upload support for events (`video_path`, `video_type` columns)
- ✅ Video upload support for rooms (`video_path`, `video_type` columns)
- ✅ Booking time buffer feature
- ✅ Gym inquiries system
- ✅ Tentative booking management
- ✅ Comprehensive payment tracking

## 🚀 How to Use

### Restore Database from Backup
```bash
mysql -u username -p database_name < Database/p601229_hotels.sql
```

### Apply Pending Migrations
Run SQL files in order:
```bash
mysql -u username -p database_name < Database/add-gym-inquiries-table.sql
mysql -u username -p database_name < Database/add-booking-time-buffer-setting.sql
```

### Migration Log
Check `migration_log` table for applied migrations:
```sql
SELECT * FROM migration_log ORDER BY migration_date DESC;
```

## ⚠️ Important Notes

1. **Backup First:** Always backup before running migrations
2. **Test Locally:** Test migrations on development environment first
3. **Migration Tracking:** Use `migration_log` table to track applied migrations
4. **No Direct Edits:** Do not edit `p601229_hotels.sql` directly - create new migration files instead

## 📝 Migration File Naming Convention

Use this format for new migrations:
```
[YYYY-MM-DD]-[feature-name].sql
```

Example:
```
2026-02-04-add-video-support.sql
```

## 🔒 Security

- ⚠️ **DELETE** setup scripts after use (one-time execution only)
- ⚠️ **NEVER** commit production database dumps to public repositories
- ⚠️ **KEEP** this folder in .gitignore for production dumps

## 📞 Support

For database-related issues, contact the development team or refer to the main project documentation.

---

**Last Updated:** February 4, 2026  
**Database Version:** 1.0  
**Total Tables:** 50+