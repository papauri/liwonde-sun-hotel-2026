# Conference & Events Features - Setup Guide

## 🎉 New Pages Added

### 1. Conference Facilities Page (conference.php)
Professional conference room booking system with 3 different venues.

**Features:**
- ✅ 3 Conference rooms with detailed information
- ✅ Capacity, size, amenities, and pricing display
- ✅ Hourly and daily rate pricing
- ✅ Full booking inquiry form
- ✅ Modal-based inquiry system
- ✅ Automated inquiry reference generation
- ✅ Email and phone validation
- ✅ Catering and AV equipment requests
- ✅ Special requirements field
- ✅ Mobile responsive design

**Conference Rooms:**
1. **Executive Boardroom** - 12 people, ideal for board meetings
2. **Grand Conference Hall** - 150 people, for seminars and large events
3. **Lakeside Meeting Room** - 30 people, with lake views

### 2. Upcoming Events Page (events.php)
Dynamic events listing showing all upcoming hotel events.

**Features:**
- ✅ Grid layout with modern card design
- ✅ Event date badges
- ✅ Featured event highlighting
- ✅ Event details (date, time, location, capacity)
- ✅ Pricing display (free or paid events)
- ✅ "Book Your Spot" CTA buttons
- ✅ Empty state for no events
- ✅ Mobile responsive design

**Sample Events Included:**
1. New Year Gala Dinner (Dec 31)
2. Wine Tasting Evening (Feb 14)
3. Business Networking Breakfast (Feb 28)
4. Easter Sunday Brunch (Apr 5)
5. Lake Festival Cultural Night (May 15)

---

## 📊 Database Setup

### Run This SQL File:
```bash
database/add-conference-events-tables.sql
```

### Tables Created:
1. **conference_rooms** - Store conference venue details
2. **conference_inquiries** - Track booking inquiries
3. **events** - Manage upcoming events

### Sample Data:
- ✅ 3 conference rooms with full details
- ✅ 5 upcoming events with descriptions
- ✅ Proper indexing for performance

---

## 🖼️ Image Requirements

### Conference Images Needed:
Place these images in `images/conference/`:
- `conference-hero.jpg` (1920x600px) - Hero banner
- `executive-boardroom.jpg` (800x500px) - Boardroom photo
- `grand-hall.jpg` (800x500px) - Large hall photo
- `lakeside-room.jpg` (800x500px) - Meeting room with view
- `placeholder-conference.jpg` (800x500px) - Fallback image

### Events Images Needed:
Place these images in `images/events/`:
- `events-hero.jpg` (1920x600px) - Hero banner
- `gala-dinner.jpg` (600x400px) - Gala event photo
- `wine-tasting.jpg` (600x400px) - Wine tasting photo
- `business-breakfast.jpg` (600x400px) - Breakfast meeting photo
- `easter-brunch.jpg` (600x400px) - Brunch buffet photo
- `cultural-night.jpg` (600x400px) - Cultural event photo
- `placeholder-event.jpg` (600x400px) - Fallback image

---

## 🧭 Navigation Updated

The following pages now include Conference & Events in their navigation:
- ✅ index.php (Homepage)
- ✅ rooms-showcase.php
- ✅ restaurant.php
- ✅ conference.php (new)
- ✅ events.php (new)

**New Menu Structure:**
1. Home
2. Rooms
3. Restaurant
4. Conference (NEW)
5. Events (NEW)
6. Contact
7. Book Now (CTA button)

---

## 🎨 Design Features

### Conference Page:
- Luxury gradient backgrounds
- Pricing cards with hourly/daily rates
- Amenities displayed as tags
- Modal inquiry form
- Success/error messages
- Gold accent colors
- Hover animations

### Events Page:
- Modern card grid layout
- Date badges with day/month
- Featured event highlighting
- Price display (free or paid)
- Time and location metadata
- Hover lift effects
- Empty state design

---

## 📧 Conference Inquiry Workflow

1. User clicks "Send Inquiry" on conference room
2. Modal opens with pre-selected room
3. User fills company details, event info
4. System validates all fields
5. Generates unique inquiry reference (CONF-2026-XXXXX)
6. Calculates cost based on hours × hourly rate
7. Saves to `conference_inquiries` table
8. Shows success message with reference number
9. Status defaults to "pending"

**Inquiry Statuses:**
- Pending (default)
- Confirmed
- Cancelled
- Completed

---

## 🚀 Quick Start

### 1. Run Database Migration:
```sql
source database/add-conference-events-tables.sql;
```

### 2. Add Images:
- Download/create conference room photos
- Download/create event photos
- Place in respective folders

### 3. Test Pages:
- Visit `/conference.php` to see conference rooms
- Visit `/events.php` to see upcoming events
- Test inquiry form submission
- Check mobile responsiveness

### 4. Customize Content:
- Update conference room details in database
- Add/edit events in `events` table
- Adjust pricing as needed
- Upload real photos

---

## 🔧 Admin Management

### Adding New Conference Room:
```sql
INSERT INTO conference_rooms (name, description, capacity, size_sqm, hourly_rate, daily_rate, amenities, image_path, display_order)
VALUES ('Your Room Name', 'Description...', 20, 50.00, 10000, 70000, 'WiFi, Projector, Whiteboard', 'images/conference/your-room.jpg', 4);
```

### Adding New Event:
```sql
INSERT INTO events (title, description, event_date, start_time, end_time, location, image_path, ticket_price, capacity, is_featured)
VALUES ('Event Name', 'Description...', '2026-03-15', '18:00:00', '21:00:00', 'Grand Hall', 'images/events/event.jpg', 20000, 100, 1);
```

### Managing Inquiries:
Check `conference_inquiries` table for new booking requests:
```sql
SELECT * FROM conference_inquiries WHERE status = 'pending' ORDER BY created_at DESC;
```

---

## 📱 Mobile Optimization

Both pages are fully responsive with:
- Stacked layouts on mobile
- Touch-friendly buttons
- Readable text sizes
- Optimized images
- Smooth scrolling
- Mobile menu integration

---

## ✨ Key Features Summary

**Conference Page:**
- Professional business focus
- Clear pricing structure
- Easy inquiry process
- Detailed room information
- Amenities highlighting

**Events Page:**
- Engaging event cards
- Clear date/time display
- Featured event badges
- Price transparency
- Simple booking CTA

**Both Pages:**
- Luxury gold/navy theme
- Smooth animations
- Modern design
- Full mobile support
- Database-driven content

---

## 🎯 Next Steps

1. **Add Real Images**: Replace placeholder images with professional photos
2. **Test Booking Flow**: Submit test inquiries to verify system
3. **Customize Content**: Update room descriptions and event details
4. **Admin Panel**: Create admin interface for managing inquiries (future enhancement)
5. **Email Notifications**: Add email confirmations for inquiries (future enhancement)

---

**Both conference and events systems are now fully functional and integrated into your hotel website!**
