<?php
/**
 * Reports Export API
 * Exports payment reports to CSV format
 */

require_once __DIR__ . '/../config/database.php';

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="payment-report-' . date('Y-m-d') . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Get parameters
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');
$report_type = $_GET['report_type'] ?? 'overview';

// Validate dates
if (!strtotime($start_date) || !strtotime($end_date)) {
    $start_date = date('Y-m-01');
    $end_date = date('Y-m-t');
}

// Get currency symbol
$currency_symbol = getSetting('currency_symbol', 'MK');

// Create output stream
$output = fopen('php://output', 'w');

// Build WHERE clause
$date_filter = "AND payment_date >= ? AND payment_date <= ?";

// ============================================
// EXPORT BASED ON REPORT TYPE
// ============================================

switch ($report_type) {
    case 'revenue':
        exportRevenueReport($output, $start_date, $end_date, $date_filter, $currency_symbol);
        break;
    
    case 'outstanding':
        exportOutstandingReport($output, $currency_symbol);
        break;
    
    case 'vat':
        exportVATReport($output, $start_date, $end_date, $date_filter, $currency_symbol);
        break;
    
    case 'overview':
    default:
        exportOverviewReport($output, $start_date, $end_date, $date_filter, $currency_symbol);
        break;
}

fclose($output);
exit;

/**
 * Export Overview Report
 */
function exportOverviewReport($output, $start_date, $end_date, $date_filter, $currency_symbol) {
    global $pdo;
    
    // Header
    fputcsv($output, ['Payment Overview Report']);
    fputcsv($output, ['Period:', $start_date . ' to ' . $end_date]);
    fputcsv($output, []);
    
    // Summary Statistics
    fputcsv($output, ['SUMMARY STATISTICS']);
    
    $summaryQuery = "
        SELECT 
            COUNT(*) as total_transactions,
            SUM(total_amount) as total_revenue,
            SUM(vat_amount) as total_vat
        FROM payments
        WHERE payment_status = 'completed'
        AND deleted_at IS NULL
        $date_filter
    ";
    $summaryStmt = $pdo->prepare($summaryQuery);
    $summaryStmt->execute([$start_date, $end_date]);
    $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);
    
    fputcsv($output, ['Total Transactions:', $summary['total_transactions']]);
    fputcsv($output, ['Total Revenue:', $currency_symbol . ' ' . number_format($summary['total_revenue'], 2)]);
    fputcsv($output, ['Total VAT Collected:', $currency_symbol . ' ' . number_format($summary['total_vat'], 2)]);
    fputcsv($output, []);
    
    // Payment Status Breakdown
    fputcsv($output, ['PAYMENT STATUS BREAKDOWN']);
    fputcsv($output, ['Status', 'Count', 'Total Amount']);
    
    $statusQuery = "
        SELECT 
            payment_status,
            COUNT(*) as count,
            SUM(total_amount) as total_amount
        FROM payments
        WHERE deleted_at IS NULL
        GROUP BY payment_status
    ";
    $statusStmt = $pdo->query($statusQuery);
    
    while ($row = $statusStmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            ucfirst($row['payment_status']),
            $row['count'],
            $currency_symbol . ' ' . number_format($row['total_amount'], 2)
        ]);
    }
    fputcsv($output, []);
    
    // Revenue by Booking Type
    fputcsv($output, ['REVENUE BY BOOKING TYPE']);
    fputcsv($output, ['Booking Type', 'Transactions', 'Revenue', 'VAT Amount']);
    
    $revenueQuery = "
        SELECT 
            booking_type,
            COUNT(*) as count,
            SUM(total_amount) as total_revenue,
            SUM(vat_amount) as total_vat
        FROM payments
        WHERE payment_status = 'completed'
        AND deleted_at IS NULL
        $date_filter
        GROUP BY booking_type
    ";
    $revenueStmt = $pdo->prepare($revenueQuery);
    $revenueStmt->execute([$start_date, $end_date]);
    
    while ($row = $revenueStmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            ucfirst($row['booking_type']),
            $row['count'],
            $currency_symbol . ' ' . number_format($row['total_revenue'], 2),
            $currency_symbol . ' ' . number_format($row['total_vat'], 2)
        ]);
    }
    fputcsv($output, []);
    
    // Payment Method Breakdown
    fputcsv($output, ['PAYMENT METHOD BREAKDOWN']);
    fputcsv($output, ['Payment Method', 'Transactions', 'Total Amount']);
    
    $methodsQuery = "
        SELECT 
            payment_method,
            COUNT(*) as count,
            SUM(total_amount) as total_amount
        FROM payments
        WHERE payment_status = 'completed'
        AND deleted_at IS NULL
        $date_filter
        GROUP BY payment_method
        ORDER BY total_amount DESC
    ";
    $methodsStmt = $pdo->prepare($methodsQuery);
    $methodsStmt->execute([$start_date, $end_date]);
    
    while ($row = $methodsStmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            ucfirst(str_replace('_', ' ', $row['payment_method'])),
            $row['count'],
            $currency_symbol . ' ' . number_format($row['total_amount'], 2)
        ]);
    }
}

/**
 * Export Revenue Report
 */
function exportRevenueReport($output, $start_date, $end_date, $date_filter, $currency_symbol) {
    global $pdo;
    
    // Header
    fputcsv($output, ['Revenue Analysis Report']);
    fputcsv($output, ['Period:', $start_date . ' to ' . $end_date]);
    fputcsv($output, []);
    
    // Daily Revenue
    fputcsv($output, ['DAILY REVENUE']);
    fputcsv($output, ['Date', 'Transactions', 'Revenue', 'VAT Amount']);
    
    $dailyQuery = "
        SELECT 
            DATE(payment_date) as date,
            COUNT(*) as transaction_count,
            SUM(total_amount) as daily_revenue,
            SUM(vat_amount) as daily_vat
        FROM payments
        WHERE payment_status = 'completed'
        AND deleted_at IS NULL
        $date_filter
        GROUP BY DATE(payment_date)
        ORDER BY date ASC
    ";
    $dailyStmt = $pdo->prepare($dailyQuery);
    $dailyStmt->execute([$start_date, $end_date]);
    
    while ($row = $dailyStmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            $row['date'],
            $row['transaction_count'],
            $currency_symbol . ' ' . number_format($row['daily_revenue'], 2),
            $currency_symbol . ' ' . number_format($row['daily_vat'], 2)
        ]);
    }
    fputcsv($output, []);
    
    // Top Clients
    fputcsv($output, ['TOP CLIENTS BY REVENUE']);
    fputcsv($output, ['Client', 'Booking Type', 'Transactions', 'Total Spent']);
    
    $clientsQuery = "
        SELECT 
            CASE 
                WHEN p.booking_type = 'room' THEN b.guest_name
                WHEN p.booking_type = 'conference' THEN ci.company_name
            END as client_name,
            p.booking_type,
            COUNT(*) as transaction_count,
            SUM(p.total_amount) as total_spent
        FROM payments p
        LEFT JOIN bookings b ON p.booking_type = 'room' AND p.booking_id = b.id
        LEFT JOIN conference_inquiries ci ON p.booking_type = 'conference' AND p.booking_id = ci.id
        WHERE p.payment_status = 'completed'
        AND p.deleted_at IS NULL
        $date_filter
        GROUP BY client_name, p.booking_type
        ORDER BY total_spent DESC
        LIMIT 20
    ";
    $clientsStmt = $pdo->prepare($clientsQuery);
    $clientsStmt->execute([$start_date, $end_date]);
    
    while ($row = $clientsStmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            $row['client_name'],
            ucfirst($row['booking_type']),
            $row['transaction_count'],
            $currency_symbol . ' ' . number_format($row['total_spent'], 2)
        ]);
    }
}

/**
 * Export Outstanding Payments Report
 */
function exportOutstandingReport($output, $currency_symbol) {
    global $pdo;
    
    // Header
    fputcsv($output, ['Outstanding Payments Report']);
    fputcsv($output, ['Generated:', date('Y-m-d H:i:s')]);
    fputcsv($output, []);
    
    fputcsv($output, [
        'Payment Reference',
        'Booking Reference',
        'Booking Type',
        'Client',
        'Amount Due',
        'VAT Amount',
        'Total Amount',
        'Status',
        'Due Date',
        'Days Overdue'
    ]);
    
    $query = "
        SELECT 
            p.*,
            CASE 
                WHEN p.booking_type = 'room' THEN b.booking_reference
                WHEN p.booking_type = 'conference' THEN ci.inquiry_reference
            END as booking_reference,
            CASE 
                WHEN p.booking_type = 'room' THEN CONCAT(b.guest_name, ' (', b.guest_email, ')')
                WHEN p.booking_type = 'conference' THEN CONCAT(ci.company_name, ' - ', ci.contact_person)
            END as client_info,
            DATEDIFF(CURDATE(), p.payment_date) as days_overdue
        FROM payments p
        LEFT JOIN bookings b ON p.booking_type = 'room' AND p.booking_id = b.id
        LEFT JOIN conference_inquiries ci ON p.booking_type = 'conference' AND p.booking_id = ci.id
        WHERE p.payment_status IN ('pending', 'partial', 'overdue')
        AND p.deleted_at IS NULL
        ORDER BY p.payment_date ASC
    ";
    $stmt = $pdo->query($query);
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            $row['payment_reference'],
            $row['booking_reference'],
            ucfirst($row['booking_type']),
            $row['client_info'],
            $currency_symbol . ' ' . number_format($row['payment_amount'], 2),
            $currency_symbol . ' ' . number_format($row['vat_amount'], 2),
            $currency_symbol . ' ' . number_format($row['total_amount'], 2),
            ucfirst($row['payment_status']),
            date('Y-m-d', strtotime($row['payment_date'])),
            $row['days_overdue'] > 0 ? $row['days_overdue'] : 0
        ]);
    }
    
    fputcsv($output, []);
    
    // Summary
    $summaryQuery = "
        SELECT 
            COUNT(*) as total_outstanding,
            SUM(total_amount) as total_amount
        FROM payments
        WHERE payment_status IN ('pending', 'partial', 'overdue')
        AND deleted_at IS NULL
    ";
    $summaryStmt = $pdo->query($summaryQuery);
    $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);
    
    fputcsv($output, ['SUMMARY']);
    fputcsv($output, ['Total Outstanding Payments:', $summary['total_outstanding']]);
    fputcsv($output, ['Total Outstanding Amount:', $currency_symbol . ' ' . number_format($summary['total_amount'], 2)]);
}

/**
 * Export VAT Report
 */
function exportVATReport($output, $start_date, $end_date, $date_filter, $currency_symbol) {
    global $pdo;
    
    // Header
    fputcsv($output, ['VAT Collection Report']);
    fputcsv($output, ['Period:', $start_date . ' to ' . $end_date]);
    fputcsv($output, ['VAT Rate:', getSetting('vat_rate') . '%']);
    fputcsv($output, ['VAT Number:', getSetting('vat_number')]);
    fputcsv($output, []);
    
    // Daily VAT Collection
    fputcsv($output, ['DAILY VAT COLLECTION']);
    fputcsv($output, ['Date', 'Transactions', 'VAT Collected', 'Total Revenue']);
    
    $dailyQuery = "
        SELECT 
            DATE(payment_date) as date,
            COUNT(*) as transaction_count,
            SUM(vat_amount) as vat_collected,
            SUM(total_amount) as total_revenue
        FROM payments
        WHERE payment_status = 'completed'
        AND deleted_at IS NULL
        $date_filter
        GROUP BY DATE(payment_date)
        ORDER BY date ASC
    ";
    $dailyStmt = $pdo->prepare($dailyQuery);
    $dailyStmt->execute([$start_date, $end_date]);
    
    $totalVat = 0;
    $totalRevenue = 0;
    
    while ($row = $dailyStmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            $row['date'],
            $row['transaction_count'],
            $currency_symbol . ' ' . number_format($row['vat_collected'], 2),
            $currency_symbol . ' ' . number_format($row['total_revenue'], 2)
        ]);
        
        $totalVat += $row['vat_collected'];
        $totalRevenue += $row['total_revenue'];
    }
    
    fputcsv($output, []);
    fputcsv($output, ['TOTALS']);
    fputcsv($output, ['Total VAT Collected:', $currency_symbol . ' ' . number_format($totalVat, 2)]);
    fputcsv($output, ['Total Revenue:', $currency_symbol . ' ' . number_format($totalRevenue, 2)]);
    fputcsv($output, []);
    
    // VAT by Booking Type
    fputcsv($output, ['VAT BY BOOKING TYPE']);
    fputcsv($output, ['Booking Type', 'Transactions', 'VAT Collected', 'Total Revenue']);
    
    $typeQuery = "
        SELECT 
            booking_type,
            COUNT(*) as count,
            SUM(vat_amount) as vat_collected,
            SUM(total_amount) as total_revenue
        FROM payments
        WHERE payment_status = 'completed'
        AND deleted_at IS NULL
        $date_filter
        GROUP BY booking_type
    ";
    $typeStmt = $pdo->prepare($typeQuery);
    $typeStmt->execute([$start_date, $end_date]);
    
    while ($row = $typeStmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            ucfirst($row['booking_type']),
            $row['count'],
            $currency_symbol . ' ' . number_format($row['vat_collected'], 2),
            $currency_symbol . ' ' . number_format($row['total_revenue'], 2)
        ]);
    }
}
