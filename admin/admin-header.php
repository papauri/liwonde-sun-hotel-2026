<?php
/**
 * Admin Header HTML Output
 * Shared header and navbar for admin pages
 * 
 * NOTE: This file outputs HTML. Include admin-init.php FIRST
 * before including this file to ensure proper initialization.
 * 
 * Usage:
 * 1. require_once 'admin-init.php';  // BEFORE <head>
 * 2. ... <head> with CSS links ...
 * 3. require_once 'admin-header.php';  // AFTER <head>
 */
?>
<div class="admin-header">
    <h1><i class="fas fa-hotel"></i> <?php echo htmlspecialchars($site_name); ?></h1>
    <div class="user-info">
        <div>
            <div class="user-name"><?php echo htmlspecialchars($user['full_name']); ?></div>
            <div class="user-role"><?php echo htmlspecialchars($user['role']); ?></div>
        </div>
        <a href="logout.php" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>
<nav class="admin-nav">
    <ul>
        <li><a href="dashboard.php" class="<?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="bookings.php" class="<?php echo $current_page === 'bookings.php' ? 'active' : ''; ?>"><i class="fas fa-calendar-check"></i> Bookings</a></li>
        <li><a href="calendar.php" class="<?php echo $current_page === 'calendar.php' ? 'active' : ''; ?>"><i class="fas fa-calendar"></i> Calendar</a></li>
        <li><a href="blocked-dates.php" class="<?php echo $current_page === 'blocked-dates.php' ? 'active' : ''; ?>"><i class="fas fa-ban"></i> Blocked Dates</a></li>
        <li><a href="room-management.php" class="<?php echo $current_page === 'room-management.php' ? 'active' : ''; ?>"><i class="fas fa-bed"></i> Rooms</a></li>
        <li><a href="conference-management.php" class="<?php echo $current_page === 'conference-management.php' ? 'active' : ''; ?>"><i class="fas fa-briefcase"></i> Conference Rooms</a></li>
        <li><a href="menu-management.php" class="<?php echo $current_page === 'menu-management.php' ? 'active' : ''; ?>"><i class="fas fa-utensils"></i> Menu</a></li>
        <li><a href="events-management.php" class="<?php echo $current_page === 'events-management.php' ? 'active' : ''; ?>"><i class="fas fa-calendar-alt"></i> Events</a></li>
        <li><a href="reviews.php" class="<?php echo $current_page === 'reviews.php' ? 'active' : ''; ?>"><i class="fas fa-star"></i> Reviews</a></li>
        <li><a href="accounting-dashboard.php" class="<?php echo $current_page === 'accounting-dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-calculator"></i> Accounting</a></li>
        <li><a href="payments.php" class="<?php echo $current_page === 'payments.php' ? 'active' : ''; ?>"><i class="fas fa-money-bill-wave"></i> Payments</a></li>
        <li><a href="invoices.php" class="<?php echo $current_page === 'invoices.php' ? 'active' : ''; ?>"><i class="fas fa-file-invoice-dollar"></i> Invoices</a></li>
        <li><a href="payment-add.php" class="<?php echo $current_page === 'payment-add.php' ? 'active' : ''; ?>"><i class="fas fa-plus-circle"></i> Add Payment</a></li>
        <li><a href="reports.php" class="<?php echo $current_page === 'reports.php' ? 'active' : ''; ?>"><i class="fas fa-chart-bar"></i> Reports</a></li>
        <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Website</a></li>
        <li><a href="booking-settings.php" class="<?php echo $current_page === 'booking-settings.php' ? 'active' : ''; ?>"><i class="fas fa-cog"></i> Booking Settings</a></li>
    </ul>
</nav>
