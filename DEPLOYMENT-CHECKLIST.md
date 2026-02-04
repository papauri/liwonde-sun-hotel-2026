# 🚀 LIVE SERVER DEPLOYMENT CHECKLIST
## Liwonde Sun Hotel Website - Production Ready

---

## ✅ CLEANUP COMPLETED

### Files Deleted (20 total):
- ❌ 5 Root MD files (temp documentation)
- ❌ 7 Root PHP test files (check-*.php, test-*.php)
- ❌ 4 Admin MD files (duplicate/unnecessary)
- ❌ 2 Admin PHP files (test/example)
- ❌ 2 Database migration MD files
- ❌ 1 Plans folder

### Directories Cleaned:
- ✅ `/cache` - All cache files cleared
- ✅ `/data/image-cache` - All cached images cleared
- ✅ `/logs` - All log files cleared
- ✅ `/sessions` - All session files cleared

### MD Files Remaining (2 total):
- ✅ `README.md` - Main project documentation
- ✅ `CACHE-MANAGEMENT-GUIDE.md` - Admin instructions

---

## 🔧 PRE-DEPLOYMENT CHECKS

### 1. Database Configuration
- [ ] **Update `config/database.php`** with production database credentials
- [ ] **Test database connection** on production server
- [ ] **Run database migrations** if needed:
  ```bash
  php Database/migrations/update_menu_feb_2026.php
  php Database/migrations/update_category_names_in_food_menu.php
  ```

### 2. Security Configuration
- [ ] **Review `config/security.php`** - CSP is already configured
- [ ] **Set proper file permissions** on server:
  - PHP files: 644 (readable)
  - Directories: 755 (executable)
  - `data/`: 777 (writable for cache/uploads)
  - `logs/`: 777 (writable)
  - `sessions/`: 777 (writable)
  - `cache/`: 777 (writable)
  - `invoices/`: 777 (writable)

### 3. Email Configuration
- [ ] **Configure `config/email.php`** for production SMTP
- [ ] **Update email credentials** in `PHPMailer`
- [ ] **Test email sending** functionality

### 4. API Keys & External Services
- [ ] **Update API keys** in `admin/api-keys.php`
- [ ] **Verify payment gateway** settings (if applicable)
- [ ] **Test external integrations**

### 5. PHP Configuration (Server-Side)
- [ ] **Ensure cURL is enabled** on production server
- [ ] **Check PHP version** (recommended: 8.0+)
- [ ] **Verify required extensions**: curl, mysqli, gd, json, mbstring
- [ ] **Set proper PHP limits** in php.ini:
  ```ini
  upload_max_filesize = 10M
  post_max_size = 10M
  max_execution_time = 300
  memory_limit = 256M
  ```

### 6. File Permissions
- [ ] **Set correct ownership** (www-data or web server user)
- [ ] **Protect sensitive files** (.env, config files)
- [ ] **Verify .htaccess** is working for URL rewriting

### 7. Testing Checklist
- [ ] **Test all pages** load correctly
- [ ] **Test booking system** end-to-end
- [ ] **Test admin panel** login and functionality
- [ ] **Test image uploads** and video playback
- [ ] **Test cache management** (admin/cache-management.php)
- [ ] **Test email notifications**
- [ ] **Test mobile responsiveness**
- [ ] **Test form submissions**

### 8. Performance Optimization
- [ ] **Enable caching** via admin panel
- [ ] **Set up scheduled cache clearing** (cron job):
  ```bash
  # Add to crontab
  0 2 * * * php /path/to/scripts/scheduled-cache-clear.php
  ```
- [ ] **Enable gzip compression** in .htaccess
- [ ] **Optimize images** (already done locally)

### 9. SEO & Analytics
- [ ] **Verify robots.txt** is correct
- [ ] **Update sitemap.xml** (run generate-sitemap.php)
- [ ] **Add Google Analytics** (if needed)
- [ ] **Update meta tags** in `includes/seo-meta.php`

### 10. Backup Strategy
- [ ] **Set up automated database backups**
- [ ] **Back up all uploaded images** (sync to cloud storage)
- [ ] **Test restore process**

---

## 📋 CRITICAL FILES TO REVIEW

### Database Credentials:
```
config/database.php
```

### Email Settings:
```
config/email.php
PHPMailer/src/
```

### Security Settings:
```
config/security.php
.htaccess
```

### API Keys:
```
admin/api-keys.php
```

---

## 🚀 DEPLOYMENT STEPS

### Step 1: Upload Files
```bash
# Upload all files via FTP/SFTP or Git
git push origin main
# Then pull on server:
git pull origin main
```

### Step 2: Set Permissions
```bash
# SSH into server
chmod -R 755 /path/to/website
chmod -R 777 /path/to/website/data
chmod -R 777 /path/to/website/logs
chmod -R 777 /path/to/website/sessions
chmod -R 777 /path/to/website/cache
chmod -R 777 /path/to/website/invoices
```

### Step 3: Import Database
```bash
# Import SQL file to production database
mysql -u username -p database_name < Database/p601229_hotels.sql
```

### Step 4: Run Migrations
```bash
php Database/migrations/update_menu_feb_2026.php
php Database/migrations/update_category_names_in_food_menu.php
```

### Step 5: Test Everything
- Visit homepage
- Test admin login
- Test booking form
- Test cache management

### Step 6: Set Up Cron Jobs
```bash
# Edit crontab
crontab -e

# Add scheduled cache clearing (2 AM daily)
0 2 * * * php /path/to/website/scripts/scheduled-cache-clear.php

# Add tentative booking checker (every hour)
0 * * * * php /path/to/website/scripts/check-tentative-bookings.php
```

---

## ⚠️ IMPORTANT REMINDERS

1. **NEVER upload local development files** (.env.local, php.ini.backup, etc.)
2. **ALWAYS backup production database** before making changes
3. **Test on staging environment first** if possible
4. **Monitor error logs** after deployment: `logs/error-*.log`
5. **Keep PHPMailer updated** for security
6. **Review cache settings** after first login to admin panel

---

## 🎯 POST-DEPLOYMENT VERIFICATION

### Immediate Checks:
- [ ] Homepage loads correctly
- [ ] All pages are accessible
- [ ] Admin panel works
- [ ] Booking system functions
- [ ] Images load properly
- [ ] Videos play without errors
- [ ] Forms submit successfully
- [ ] Emails send correctly

### Monitor for 24-48 Hours:
- [ ] Check error logs regularly
- [ ] Monitor server resources
- [ ] Test all user flows
- [ ] Verify email deliveries
- [ ] Check cache performance

---

## 📞 SUPPORT CONTACTS

Keep these handy:
- Hosting Provider: ___________
- Database Admin: ___________
- Email Provider: ___________

---

## ✅ DEPLOYMENT COMPLETE?

Once all checks pass, your site is LIVE! 🎉

Generate final sitemap:
```bash
php generate-sitemap.php
```

Access Admin Panel:
```
https://yourdomain.com/admin/
```

Cache Management:
```
https://yourdomain.com/admin/cache-management.php
```

---

**Last Updated:** 2026-02-05  
**Version:** 1.0  
**Status:** Ready for Production