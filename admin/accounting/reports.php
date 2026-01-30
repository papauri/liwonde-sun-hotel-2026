<?php
/**
 * Payment Reports and Financial Analytics
 * Provides comprehensive payment status tracking and financial reporting
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../admin-header.php';

// Check permissions
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin-login.php');
    exit;
}

// Get date range from query parameters or default to current month
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

// Get VAT settings
$vatEnabled = getSetting('vat_enabled') === '1';
$vatRate = (float)getSetting('vat_rate', 0);

// Build WHERE clause for date filtering
$date_filter = "AND payment_date >= ? AND payment_date <= ?";

// ============================================
// REPORT DATA QUERIES
// ============================================

// 1. Payment Status Overview
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
$statusData = [];
$statusLabels = [
    'pending' => 'Pending',
    'partial' => 'Partial Payment',
    'completed' => 'Paid',
    'overdue' => 'Overdue',
    'refunded' => 'Refunded',
    'cancelled' => 'Cancelled'
];
while ($row = $statusStmt->fetch(PDO::FETCH_ASSOC)) {
    $statusData[$row['payment_status']] = $row;
}

// 2. Revenue by Booking Type
$revenueByTypeQuery = "
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
$revenueByTypeStmt = $pdo->prepare($revenueByTypeQuery);
$revenueByTypeStmt->execute([$start_date, $end_date]);
$revenueByType = $revenueByTypeStmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Payment Method Breakdown
$paymentMethodsQuery = "
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
$paymentMethodsStmt = $pdo->prepare($paymentMethodsQuery);
$paymentMethodsStmt->execute([$start_date, $end_date]);
$paymentMethods = $paymentMethodsStmt->fetchAll(PDO::FETCH_ASSOC);

// 4. Outstanding Payments (pending and overdue)
$outstandingQuery = "
    SELECT 
        p.*,
        CASE 
            WHEN p.booking_type = 'room' THEN CONCAT('BK-', b.booking_reference)
            WHEN p.booking_type = 'conference' THEN CONCAT('CONF-', ci.inquiry_reference)
        END as booking_reference,
        CASE 
            WHEN p.booking_type = 'room' THEN CONCAT(b.guest_name, ' (', b.guest_email, ')')
            WHEN p.booking_type = 'conference' THEN CONCAT(ci.company_name, ' - ', ci.contact_person)
        END as client_info
    FROM payments p
    LEFT JOIN bookings b ON p.booking_type = 'room' AND p.booking_id = b.id
    LEFT JOIN conference_inquiries ci ON p.booking_type = 'conference' AND p.booking_id = ci.id
    WHERE p.payment_status IN ('pending', 'partial', 'overdue')
    AND p.deleted_at IS NULL
    ORDER BY p.payment_date ASC
";
$outstandingStmt = $pdo->query($outstandingQuery);
$outstandingPayments = $outstandingStmt->fetchAll(PDO::FETCH_ASSOC);

// 5. Daily Revenue Trend
$dailyRevenueQuery = "
    SELECT 
        DATE(payment_date) as date,
        COUNT(*) as transaction_count,
        SUM(total_amount) as daily_revenue
    FROM payments
    WHERE payment_status = 'completed'
    AND deleted_at IS NULL
    $date_filter
    GROUP BY DATE(payment_date)
    ORDER BY date ASC
";
$dailyRevenueStmt = $pdo->prepare($dailyRevenueQuery);
$dailyRevenueStmt->execute([$start_date, $end_date]);
$dailyRevenue = $dailyRevenueStmt->fetchAll(PDO::FETCH_ASSOC);

// 6. VAT Collected Report
$vatCollectedQuery = "
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
$vatCollectedStmt = $pdo->prepare($vatCollectedQuery);
$vatCollectedStmt->execute([$start_date, $end_date]);
$vatCollected = $vatCollectedStmt->fetchAll(PDO::FETCH_ASSOC);

// 7. Top Clients by Revenue
$topClientsQuery = "
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
    LIMIT 10
";
$topClientsStmt = $pdo->prepare($topClientsQuery);
$topClientsStmt->execute([$start_date, $end_date]);
$topClients = $topClientsStmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate summary statistics
$totalRevenue = 0;
$totalVatCollected = 0;
$totalTransactions = 0;
$totalOutstanding = 0;

foreach ($revenueByType as $revenue) {
    $totalRevenue += $revenue['total_revenue'];
    $totalVatCollected += $revenue['total_vat'];
    $totalTransactions += $revenue['count'];
}

foreach ($outstandingPayments as $payment) {
    $totalOutstanding += $payment['total_amount'];
}

// Calculate percentages for status overview
$totalStatusCount = 0;
foreach ($statusData as $status) {
    $totalStatusCount += $status['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Reports - <?php echo htmlspecialchars(getSetting('site_name')); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .reports-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .reports-header {
            background: linear-gradient(135deg, #0A1929 0%, #1a3a5c 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .reports-header h1 {
            color: #D4AF37;
            margin-bottom: 10px;
        }

        .date-filter {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .date-filter form {
            display: flex;
            gap: 15px;
            align-items: end;
            flex-wrap: wrap;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }

        .form-group input,
        .form-group select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #D4AF37;
            color: #0A1929;
        }

        .btn-primary:hover {
            background: #c49f2f;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #D4AF37;
        }

        .summary-card h3 {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .summary-card .value {
            font-size: 28px;
            font-weight: bold;
            color: #0A1929;
        }

        .summary-card.revenue { border-left-color: #28a745; }
        .summary-card.outstanding { border-left-color: #dc3545; }
        .summary-card.transactions { border-left-color: #007bff; }
        .summary-card.vat { border-left-color: #6f42c1; }

        .report-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .report-section h2 {
            color: #0A1929;
            border-bottom: 2px solid #D4AF37;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .status-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .status-card {
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .status-card.pending { background: #fff3cd; border-left: 4px solid #ffc107; }
        .status-card.partial { background: #e2e3e5; border-left: 4px solid #6c757d; }
        .status-card.completed { background: #d4edda; border-left: 4px solid #28a745; }
        .status-card.overdue { background: #f8d7da; border-left: 4px solid #dc3545; }
        .status-card.refunded { background: #e2e3e5; border-left: 4px solid #6c757d; }

        .status-card .count {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .status-card .label {
            font-size: 14px;
            color: #666;
        }

        .status-card .amount {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th,
        table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #0A1929;
        }

        table tr:hover {
            background: #f8f9fa;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-room { background: #e7f3ff; color: #0d6efd; }
        .badge-conference { background: #fff3e0; color: #ff9800; }

        .chart-container {
            height: 300px;
            margin-top: 20px;
        }

        .progress-bar {
            width: 100%;
            height: 30px;
            background: #e9ecef;
            border-radius: 15px;
            overflow: hidden;
            display: flex;
        }

        .progress-segment {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 12px;
            transition: width 0.3s;
        }

        .progress-segment.pending { background: #ffc107; }
        .progress-segment.partial { background: #6c757d; }
        .progress-segment.completed { background: #28a745; }
        .progress-segment.overdue { background: #dc3545; }

        .export-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .summary-cards {
                grid-template-columns: 1fr;
            }

            .status-overview {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 12px;
            }

            .date-filter form {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="reports-container">
        <div class="reports-header">
            <h1><i class="fas fa-chart-line"></i> Payment Reports & Analytics</h1>
            <p>Comprehensive financial reporting and payment status tracking</p>
        </div>

        <!-- Date Filter -->
        <div class="date-filter">
            <form method="GET" action="">
                <div class="form-group">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>" required>
                </div>
                <div class="form-group">
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" required>
                </div>
                <div class="form-group">
                    <label for="report_type">Report Type:</label>
                    <select id="report_type" name="report_type">
                        <option value="overview" <?php echo $report_type === 'overview' ? 'selected' : ''; ?>>Overview</option>
                        <option value="revenue" <?php echo $report_type === 'revenue' ? 'selected' : ''; ?>>Revenue Analysis</option>
                        <option value="outstanding" <?php echo $report_type === 'outstanding' ? 'selected' : ''; ?>>Outstanding Payments</option>
                        <option value="vat" <?php echo $report_type === 'vat' ? 'selected' : ''; ?>>VAT Report</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Apply Filter
                </button>
                <button type="button" class="btn btn-secondary" onclick="exportToCSV()">
                    <i class="fas fa-download"></i> Export CSV
                </button>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card revenue">
                <h3>Total Revenue</h3>
                <div class="value"><?php echo $currency_symbol . ' ' . number_format($totalRevenue, 2); ?></div>
            </div>
            <div class="summary-card outstanding">
                <h3>Outstanding</h3>
                <div class="value"><?php echo $currency_symbol . ' ' . number_format($totalOutstanding, 2); ?></div>
            </div>
            <div class="summary-card transactions">
                <h3>Transactions</h3>
                <div class="value"><?php echo number_format($totalTransactions); ?></div>
            </div>
            <?php if ($vatEnabled): ?>
            <div class="summary-card vat">
                <h3>VAT Collected</h3>
                <div class="value"><?php echo $currency_symbol . ' ' . number_format($totalVatCollected, 2); ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Payment Status Overview -->
        <div class="report-section">
            <h2><i class="fas fa-tasks"></i> Payment Status Overview</h2>
            
            <div class="status-overview">
                <?php foreach ($statusLabels as $status => $label): ?>
                    <?php if (isset($statusData[$status])): ?>
                        <div class="status-card <?php echo $status; ?>">
                            <div class="count"><?php echo number_format($statusData[$status]['count']); ?></div>
                            <div class="label"><?php echo $label; ?></div>
                            <div class="amount"><?php echo $currency_symbol . ' ' . number_format($statusData[$status]['total_amount'], 2); ?></div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <?php if ($totalStatusCount > 0): ?>
            <div style="margin-top: 30px;">
                <h3>Payment Distribution</h3>
                <div class="progress-bar">
                    <?php foreach ($statusLabels as $status => $label): ?>
                        <?php if (isset($statusData[$status])): ?>
                            <?php $percentage = ($statusData[$status]['count'] / $totalStatusCount) * 100; ?>
                            <div class="progress-segment <?php echo $status; ?>" style="width: <?php echo $percentage; ?>%;" title="<?php echo $label; ?>: <?php echo number_format($percentage, 1); ?>%">
                                <?php echo number_format($percentage, 0); ?>%
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Revenue by Booking Type -->
        <div class="report-section">
            <h2><i class="fas fa-chart-pie"></i> Revenue by Booking Type</h2>
            <table>
                <thead>
                    <tr>
                        <th>Booking Type</th>
                        <th>Transactions</th>
                        <th>Revenue</th>
                        <?php if ($vatEnabled): ?>
                        <th>VAT Amount</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($revenueByType as $revenue): ?>
                        <tr>
                            <td>
                                <span class="badge badge-<?php echo $revenue['booking_type']; ?>">
                                    <?php echo ucfirst($revenue['booking_type']); ?>
                                </span>
                            </td>
                            <td><?php echo number_format($revenue['count']); ?></td>
                            <td><?php echo $currency_symbol . ' ' . number_format($revenue['total_revenue'], 2); ?></td>
                            <?php if ($vatEnabled): ?>
                            <td><?php echo $currency_symbol . ' ' . number_format($revenue['total_vat'], 2); ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Payment Method Breakdown -->
        <div class="report-section">
            <h2><i class="fas fa-credit-card"></i> Payment Method Breakdown</h2>
            <table>
                <thead>
                    <tr>
                        <th>Payment Method</th>
                        <th>Transactions</th>
                        <th>Total Amount</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($paymentMethods as $method): ?>
                        <tr>
                            <td><?php echo ucfirst(str_replace('_', ' ', $method['payment_method'])); ?></td>
                            <td><?php echo number_format($method['count']); ?></td>
                            <td><?php echo $currency_symbol . ' ' . number_format($method['total_amount'], 2); ?></td>
                            <td><?php echo number_format(($method['total_amount'] / $totalRevenue) * 100, 1); ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Outstanding Payments -->
        <div class="report-section">
            <h2><i class="fas fa-exclamation-triangle"></i> Outstanding Payments</h2>
            <?php if (empty($outstandingPayments)): ?>
                <p style="color: #28a745; font-weight: bold; text-align: center; padding: 20px;">
                    <i class="fas fa-check-circle"></i> No outstanding payments!
                </p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Payment Reference</th>
                            <th>Booking Reference</th>
                            <th>Client</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($outstandingPayments as $payment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($payment['payment_reference']); ?></td>
                                <td><?php echo htmlspecialchars($payment['booking_reference']); ?></td>
                                <td><?php echo htmlspecialchars($payment['client_info']); ?></td>
                                <td><?php echo $currency_symbol . ' ' . number_format($payment['total_amount'], 2); ?></td>
                                <td>
                                    <span class="badge" style="background: <?php echo $payment['payment_status'] === 'overdue' ? '#f8d7da' : '#fff3cd'; ?>; color: <?php echo $payment['payment_status'] === 'overdue' ? '#dc3545' : '#856404'; ?>;">
                                        <?php echo ucfirst($payment['payment_status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($payment['payment_date'])); ?></td>
                                <td>
                                    <a href="payment-add.php?id=<?php echo $payment['id']; ?>" class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;">
                                        <i class="fas fa-edit"></i> Update
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Top Clients -->
        <div class="report-section">
            <h2><i class="fas fa-trophy"></i> Top Clients by Revenue</h2>
            <table>
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Booking Type</th>
                        <th>Transactions</th>
                        <th>Total Spent</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topClients as $client): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($client['client_name']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $client['booking_type']; ?>">
                                    <?php echo ucfirst($client['booking_type']); ?>
                                </span>
                            </td>
                            <td><?php echo number_format($client['transaction_count']); ?></td>
                            <td><?php echo $currency_symbol . ' ' . number_format($client['total_spent'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Daily Revenue Trend -->
        <?php if (!empty($dailyRevenue)): ?>
        <div class="report-section">
            <h2><i class="fas fa-chart-line"></i> Daily Revenue Trend</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Transactions</th>
                        <th>Daily Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dailyRevenue as $day): ?>
                        <tr>
                            <td><?php echo date('M j, Y', strtotime($day['date'])); ?></td>
                            <td><?php echo number_format($day['transaction_count']); ?></td>
                            <td><?php echo $currency_symbol . ' ' . number_format($day['daily_revenue'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- VAT Report -->
        <?php if ($vatEnabled && !empty($vatCollected)): ?>
        <div class="report-section">
            <h2><i class="fas fa-percent"></i> VAT Collected Report</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Transactions</th>
                        <th>VAT Collected</th>
                        <th>Total Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vatCollected as $vat): ?>
                        <tr>
                            <td><?php echo date('M j, Y', strtotime($vat['date'])); ?></td>
                            <td><?php echo number_format($vat['transaction_count']); ?></td>
                            <td><?php echo $currency_symbol . ' ' . number_format($vat['vat_collected'], 2); ?></td>
                            <td><?php echo $currency_symbol . ' ' . number_format($vat['total_revenue'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="background: #f8f9fa; font-weight: bold;">
                        <td>Total</td>
                        <td><?php echo number_format($totalTransactions); ?></td>
                        <td><?php echo $currency_symbol . ' ' . number_format($totalVatCollected, 2); ?></td>
                        <td><?php echo $currency_symbol . ' ' . number_format($totalRevenue, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function exportToCSV() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const reportType = document.getElementById('report_type').value;
            
            // Create CSV export URL
            const url = `../../api/reports-export.php?start_date=${startDate}&end_date=${endDate}&report_type=${reportType}`;
            window.open(url, '_blank');
        }

        // Add quick date range buttons
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.date-filter form');
            const quickFilters = document.createElement('div');
            quickFilters.style.marginTop = '15px';
            quickFilters.innerHTML = `
                <strong>Quick Filters:</strong>
                <button type="button" class="btn btn-secondary" onclick="setDateRange('today')" style="margin-left: 10px; padding: 5px 10px;">Today</button>
                <button type="button" class="btn btn-secondary" onclick="setDateRange('week')" style="margin-left: 5px; padding: 5px 10px;">This Week</button>
                <button type="button" class="btn btn-secondary" onclick="setDateRange('month')" style="margin-left: 5px; padding: 5px 10px;">This Month</button>
                <button type="button" class="btn btn-secondary" onclick="setDateRange('quarter')" style="margin-left: 5px; padding: 5px 10px;">This Quarter</button>
                <button type="button" class="btn btn-secondary" onclick="setDateRange('year')" style="margin-left: 5px; padding: 5px 10px;">This Year</button>
            `;
            form.appendChild(quickFilters);
        });

        function setDateRange(range) {
            const today = new Date();
            let startDate, endDate;

            switch(range) {
                case 'today':
                    startDate = endDate = today.toISOString().split('T')[0];
                    break;
                case 'week':
                    const weekStart = new Date(today);
                    weekStart.setDate(today.getDate() - today.getDay());
                    startDate = weekStart.toISOString().split('T')[0];
                    endDate = today.toISOString().split('T')[0];
                    break;
                case 'month':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                    endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().split('T')[0];
                    break;
                case 'quarter':
                    const quarterStart = new Date(today.getFullYear(), Math.floor(today.getMonth() / 3) * 3, 1);
                    const quarterEnd = new Date(today.getFullYear(), Math.floor(today.getMonth() / 3) * 3 + 3, 0);
                    startDate = quarterStart.toISOString().split('T')[0];
                    endDate = quarterEnd.toISOString().split('T')[0];
                    break;
                case 'year':
                    startDate = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
                    endDate = new Date(today.getFullYear(), 11, 31).toISOString().split('T')[0];
                    break;
            }

            document.getElementById('start_date').value = startDate;
            document.getElementById('end_date').value = endDate;
            document.querySelector('.date-filter form').submit();
        }
    </script>
</body>
</html>
