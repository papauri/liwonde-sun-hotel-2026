# Tabbed Interface Design for admin/bookings.php

## Overview
Reorganize the bookings management page with clickable tabs to filter bookings by status, reducing clutter and improving usability.

## Current State Analysis
- Single table showing ALL bookings (lines 876-990)
- Statistics cards at top (lines 834-855)
- Conference inquiries section at bottom (lines 1000-1053)
- Action buttons vary by booking status (lines 947-985)

## Proposed Tab Structure

### Tab Navigation
Located below statistics cards, above the bookings table:

```
[All] [Pending] [Tentative] [Expiring Soon] [Confirmed] [Today's Check-ins] [Today's Check-outs] [Checked In] [Checked Out] [Cancelled] [Paid] [Unpaid]
```

### Tab Definitions

| Tab | Filter Criteria | Icon | Color |
|-----|----------------|------|-------|
| All Bookings | Show all bookings | `fa-list` | Navy |
| Pending | `status = 'pending'` | `fa-clock` | Yellow |
| Tentative | `status = 'tentative'` OR `is_tentative = 1` | `fa-hourglass-half` | Gold |
| Expiring Soon | Tentative bookings expiring within 24 hours | `fa-exclamation-triangle` | Orange |
| Confirmed | `status = 'confirmed'` | `fa-check-circle` | Green |
| Today's Check-ins | Confirmed bookings with check-in = today | `fa-calendar-day` | Blue |
| Today's Check-outs | Checked-in bookings with check-out = today | `fa-calendar-times` | Purple |
| Checked In | `status = 'checked-in'` | `fa-sign-in-alt` | Cyan |
| Checked Out | `status = 'checked-out'` | `fa-sign-out-alt` | Gray |
| Cancelled | `status = 'cancelled'` | `fa-times-circle` | Red |
| Paid | `payment_status = 'paid'` | `fa-dollar-sign` | Green |
| Unpaid | `payment_status != 'paid'` | `fa-exclamation-circle` | Red |

## Technical Implementation

### 1. HTML Structure (after line 864)

```html
<!-- Tab Navigation -->
<div class="tabs-container">
    <div class="tabs-header">
        <button class="tab-button active" data-tab="all" data-count="<?php echo $total_bookings; ?>">
            <i class="fas fa-list"></i>
            All
            <span class="tab-count"><?php echo $total_bookings; ?></span>
        </button>
        <button class="tab-button" data-tab="pending" data-count="<?php echo $pending; ?>">
            <i class="fas fa-clock"></i>
            Pending
            <span class="tab-count"><?php echo $pending; ?></span>
        </button>
        <button class="tab-button" data-tab="tentative" data-count="<?php echo $tentative; ?>">
            <i class="fas fa-hourglass-half"></i>
            Tentative
            <span class="tab-count"><?php echo $tentative; ?></span>
        </button>
        <button class="tab-button" data-tab="expiring-soon" data-count="<?php echo $expiring_soon; ?>">
            <i class="fas fa-exclamation-triangle"></i>
            Expiring Soon
            <span class="tab-count"><?php echo $expiring_soon; ?></span>
        </button>
        <button class="tab-button" data-tab="confirmed" data-count="<?php echo $confirmed; ?>">
            <i class="fas fa-check-circle"></i>
            Confirmed
            <span class="tab-count"><?php echo $confirmed; ?></span>
        </button>
        <button class="tab-button" data-tab="today-checkins" data-count="<?php echo $today_checkins; ?>">
            <i class="fas fa-calendar-day"></i>
            Today's Check-ins
            <span class="tab-count"><?php echo $today_checkins; ?></span>
        </button>
        <button class="tab-button" data-tab="today-checkouts" data-count="<?php echo $today_checkouts; ?>">
            <i class="fas fa-calendar-times"></i>
            Today's Check-outs
            <span class="tab-count"><?php echo $today_checkouts; ?></span>
        </button>
        <button class="tab-button" data-tab="checked-in" data-count="<?php echo $checked_in; ?>">
            <i class="fas fa-sign-in-alt"></i>
            Checked In
            <span class="tab-count"><?php echo $checked_in; ?></span>
        </button>
        <button class="tab-button" data-tab="checked-out" data-count="<?php echo $checked_out; ?>">
            <i class="fas fa-sign-out-alt"></i>
            Checked Out
            <span class="tab-count"><?php echo $checked_out; ?></span>
        </button>
        <button class="tab-button" data-tab="cancelled" data-count="<?php echo $cancelled; ?>">
            <i class="fas fa-times-circle"></i>
            Cancelled
            <span class="tab-count"><?php echo $cancelled; ?></span>
        </button>
        <button class="tab-button" data-tab="paid" data-count="<?php echo $paid; ?>">
            <i class="fas fa-dollar-sign"></i>
            Paid
            <span class="tab-count"><?php echo $paid; ?></span>
        </button>
        <button class="tab-button" data-tab="unpaid" data-count="<?php echo $unpaid; ?>">
            <i class="fas fa-exclamation-circle"></i>
            Unpaid
            <span class="tab-count"><?php echo $unpaid; ?></span>
        </button>
    </div>
</div>
```

### 2. CSS Styles (add to existing `<style>` section)

```css
/* Tab Navigation Styles */
.tabs-container {
    background: white;
    border-radius: 12px 12px 0 0;
    padding: 0;
    margin-bottom: 0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.tabs-header {
    display: flex;
    flex-wrap: wrap;
    gap: 0;
    border-bottom: 2px solid #e0e0e0;
    overflow-x: auto;
}

.tab-button {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 16px 20px;
    background: white;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    font-weight: 500;
    color: #666;
    transition: all 0.3s ease;
    white-space: nowrap;
    position: relative;
}

.tab-button:hover {
    background: #f8f9fa;
    color: var(--navy);
}

.tab-button.active {
    color: var(--navy);
    border-bottom-color: var(--gold);
    background: linear-gradient(to bottom, #fff8e1 0%, white 100%);
}

.tab-button i {
    font-size: 16px;
}

.tab-count {
    background: #f0f0f0;
    color: #666;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    min-width: 20px;
    text-align: center;
}

.tab-button.active .tab-count {
    background: var(--gold);
    color: var(--deep-navy);
}

/* Tab-specific active colors */
.tab-button[data-tab="pending"].active .tab-count {
    background: #ffc107;
    color: #212529;
}

.tab-button[data-tab="tentative"].active .tab-count {
    background: linear-gradient(135deg, var(--gold) 0%, #c49b2e 100%);
    color: var(--deep-navy);
}

.tab-button[data-tab="expiring-soon"].active .tab-count {
    background: #ff6b35;
    color: white;
    animation: pulse 2s infinite;
}

.tab-button[data-tab="confirmed"].active .tab-count {
    background: #28a745;
    color: white;
}

.tab-button[data-tab="today-checkins"].active .tab-count {
    background: #007bff;
    color: white;
}

.tab-button[data-tab="today-checkouts"].active .tab-count {
    background: #6f42c1;
    color: white;
}

.tab-button[data-tab="checked-in"].active .tab-count {
    background: #17a2b8;
    color: white;
}

.tab-button[data-tab="checked-out"].active .tab-count {
    background: #6c757d;
    color: white;
}

.tab-button[data-tab="cancelled"].active .tab-count {
    background: #dc3545;
    color: white;
}

.tab-button[data-tab="paid"].active .tab-count {
    background: #28a745;
    color: white;
}

.tab-button[data-tab="unpaid"].active .tab-count {
    background: #dc3545;
    color: white;
}

/* Adjust bookings section to connect with tabs */
.bookings-section {
    border-radius: 0 0 12px 12px !important;
    margin-top: -1px !important;
}

/* Responsive tabs */
@media (max-width: 1024px) {
    .tabs-header {
        justify-content: flex-start;
    }
    
    .tab-button {
        padding: 12px 16px;
        font-size: 13px;
    }
    
    .tab-count {
        font-size: 11px;
        padding: 2px 6px;
    }
}

@media (max-width: 768px) {
    .tabs-header {
        gap: 0;
    }
    
    .tab-button {
        padding: 10px 12px;
        font-size: 12px;
        flex: 0 0 auto;
    }
    
    .tab-button span:not(.tab-count) {
        display: none;
    }
    
    .tab-button i {
        font-size: 18px;
    }
}
```

### 3. JavaScript Implementation (replace existing script section)

```javascript
// Tab switching functionality
let currentTab = 'all';

function switchTab(tabName) {
    currentTab = tabName;
    
    // Update active tab button
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
        if (btn.dataset.tab === tabName) {
            btn.classList.add('active');
        }
    });
    
    // Filter table rows
    filterBookingsTable(tabName);
    
    // Update section title
    updateSectionTitle(tabName);
}

function filterBookingsTable(tabName) {
    const table = document.querySelector('.booking-table tbody');
    if (!table) return;
    
    const rows = table.querySelectorAll('tr');
    let visibleCount = 0;
    
    // Get today's date for comparison
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const todayStr = today.toISOString().split('T')[0];
    
    rows.forEach(row => {
        const statusCell = row.querySelector('td:nth-child(9)'); // Status column
        const paymentCell = row.querySelector('td:nth-child(10)'); // Payment column
        const checkInCell = row.querySelector('td:nth-child(4)'); // Check-in date column
        const checkOutCell = row.querySelector('td:nth-child(5)'); // Check-out date column
        
        if (!statusCell || !paymentCell) return;
        
        const statusBadge = statusCell.querySelector('.badge');
        const paymentBadge = paymentCell.querySelector('.badge');
        
        if (!statusBadge || !paymentBadge) return;
        
        const status = statusBadge.textContent.trim().toLowerCase().replace(' ', '-');
        const payment = paymentBadge.textContent.trim().toLowerCase();
        
        // Parse dates from table cells
        const checkInDate = checkInCell ? new Date(checkInCell.textContent.trim()) : null;
        const checkOutDate = checkOutCell ? new Date(checkOutCell.textContent.trim()) : null;
        
        // Check if tentative booking is expiring soon (within 24 hours)
        const isExpiringSoon = row.innerHTML.includes('Expires soon') ||
                              (status === 'tentative' && row.querySelector('.expires-soon'));
        
        // Check if check-in/check-out is today
        const isTodayCheckIn = checkInDate &&
                              checkInDate.toISOString().split('T')[0] === todayStr &&
                              status === 'confirmed';
        const isTodayCheckOut = checkOutDate &&
                               checkOutDate.toISOString().split('T')[0] === todayStr &&
                               status === 'checked-in';
        
        let isVisible = false;
        
        switch(tabName) {
            case 'all':
                isVisible = true;
                break;
            case 'pending':
                isVisible = status === 'pending';
                break;
            case 'tentative':
                isVisible = status === 'tentative' || row.innerHTML.includes('Tentative');
                break;
            case 'expiring-soon':
                isVisible = isExpiringSoon;
                break;
            case 'confirmed':
                isVisible = status === 'confirmed';
                break;
            case 'today-checkins':
                isVisible = isTodayCheckIn;
                break;
            case 'today-checkouts':
                isVisible = isTodayCheckOut;
                break;
            case 'checked-in':
                isVisible = status === 'checked-in';
                break;
            case 'checked-out':
                isVisible = status === 'checked-out';
                break;
            case 'cancelled':
                isVisible = status === 'cancelled';
                break;
            case 'paid':
                isVisible = payment === 'paid';
                break;
            case 'unpaid':
                isVisible = payment !== 'paid';
                break;
        }
        
        if (isVisible) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update count in section title
    const countSpan = document.querySelector('.section-title span');
    if (countSpan) {
        countSpan.textContent = `(${visibleCount} shown)`;
    }
}

function updateSectionTitle(tabName) {
    const titleElement = document.querySelector('.section-title');
    if (!titleElement) return;
    
    const tabTitles = {
        'all': 'All Room Bookings',
        'pending': 'Pending Bookings',
        'tentative': 'Tentative Bookings',
        'expiring-soon': 'Expiring Soon (Urgent)',
        'confirmed': 'Confirmed Bookings',
        'today-checkins': "Today's Check-ins",
        'today-checkouts': "Today's Check-outs",
        'checked-in': 'Checked In Guests',
        'checked-out': 'Checked Out Bookings',
        'cancelled': 'Cancelled Bookings',
        'paid': 'Paid Bookings',
        'unpaid': 'Unpaid Bookings'
    };
    
    const icon = titleElement.querySelector('i');
    const countSpan = titleElement.querySelector('span');
    
    let newTitle = tabTitles[tabName] || 'Room Bookings';
    let newIcon = 'fa-bed';
    
    if (tabName === 'pending') newIcon = 'fa-clock';
    if (tabName === 'tentative') newIcon = 'fa-hourglass-half';
    if (tabName === 'expiring-soon') newIcon = 'fa-exclamation-triangle';
    if (tabName === 'confirmed') newIcon = 'fa-check-circle';
    if (tabName === 'today-checkins') newIcon = 'fa-calendar-day';
    if (tabName === 'today-checkouts') newIcon = 'fa-calendar-times';
    if (tabName === 'checked-in') newIcon = 'fa-sign-in-alt';
    if (tabName === 'checked-out') newIcon = 'fa-sign-out-alt';
    if (tabName === 'cancelled') newIcon = 'fa-times-circle';
    if (tabName === 'paid') newIcon = 'fa-dollar-sign';
    if (tabName === 'unpaid') newIcon = 'fa-exclamation-circle';
    
    titleElement.innerHTML = `<i class="fas ${newIcon}"></i> ${newTitle} `;
    if (countSpan) {
        titleElement.appendChild(countSpan);
    }
}

// Initialize tab click handlers
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            switchTab(tabName);
        });
    });
    
    // Initial filter
    switchTab('all');
});

// Keep all existing functions (makeTentative, convertTentativeBooking, etc.)
```

### 4. PHP Backend Changes

Add additional counts for tabs (after line 395):

```php
// Count statistics for all tabs
$checked_out = count(array_filter($bookings, fn($b) => $b['status'] === 'checked-out'));
$cancelled = count(array_filter($bookings, fn($b) => $b['status'] === 'cancelled'));
$paid = count(array_filter($bookings, fn($b) => $b['payment_status'] === 'paid'));
$unpaid = count(array_filter($bookings, fn($b) => $b['payment_status'] !== 'paid'));

// Count expiring soon (tentative bookings expiring within 24 hours)
$now = new DateTime();
$expiring_soon = 0;
foreach ($bookings as $booking) {
    if (($booking['status'] === 'tentative' || $booking['is_tentative'] == 1) && $booking['tentative_expires_at']) {
        $expires_at = new DateTime($booking['tentative_expires_at']);
        $hours_until_expiry = ($expires_at->getTimestamp() - $now->getTimestamp()) / 3600;
        if ($hours_until_expiry <= 24 && $hours_until_expiry > 0) {
            $expiring_soon++;
        }
    }
}

// Count today's check-ins (confirmed bookings with check-in today)
$today = new DateTime();
$today_str = $today->format('Y-m-d');
$today_checkins = count(array_filter($bookings, fn($b) =>
    $b['status'] === 'confirmed' && $b['check_in_date'] === $today_str
));

// Count today's check-outs (checked-in bookings with check-out today)
$today_checkouts = count(array_filter($bookings, fn($b) =>
    $b['status'] === 'checked-in' && $b['check_out_date'] === $today_str
));
```

## User Flow

1. **Default View**: Page loads with "All Bookings" tab active
2. **Tab Click**: User clicks a tab (e.g., "Confirmed")
3. **Visual Feedback**: 
   - Tab button becomes active (highlighted with gold border)
   - Table filters to show only matching bookings
   - Section title updates to "Confirmed Bookings"
   - Count updates to show visible bookings
4. **Actions**: All existing action buttons (Confirm, Check In, Paid, etc.) work normally
5. **Persistence**: Tab selection resets to "All" on page reload (simple approach)

## Benefits

1. **Reduced Clutter**: Each tab shows only relevant bookings
2. **Faster Navigation**: Quick access to specific booking types
3. **Better Focus**: Staff can focus on one status at a time
4. **Visual Counts**: Each tab shows count of bookings in that category
5. **Mobile Friendly**: Tabs collapse to icons on small screens
6. **No Backend Changes**: Pure client-side filtering, no API changes needed

## Future Enhancements (Optional)

1. **URL Parameter**: `?tab=pending` to share filtered views
2. **Remember Tab**: Store last tab in localStorage
3. **Search Within Tab**: Filter bookings in current tab by name/reference
4. **Bulk Actions**: Select multiple bookings in a tab for bulk operations
5. **Auto-refresh**: Tentative tab auto-refreshes every 5 minutes
