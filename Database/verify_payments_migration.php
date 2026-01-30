<?php
/**
 * Database Migration Verification Script
 * Verifies the payments_accounting migration was successful
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Payments Migration Verification Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; }
        .header h1 { font-size: 28px; margin-bottom: 10px; }
        .header p { opacity: 0.9; }
        .content { padding: 30px; }
        .section { margin-bottom: 30px; }
        .section h2 { font-size: 20px; color: #333; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #667eea; }
        .status { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
        .status.success { background: #10b981; color: white; }
        .status.error { background: #ef4444; color: white; }
        .status.warning { background: #f59e0b; color: white; }
        .status.info { background: #3b82f6; color: white; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f9fafb; font-weight: 600; color: #374151; }
        tr:hover { background: #f9fafb; }
        .check-item { display: flex; align-items: center; padding: 10px 0; border-bottom: 1px solid #e5e7eb; }
        .check-icon { width: 24px; height: 24px; margin-right: 12px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; }
        .check-icon.success { background: #10b981; color: white; }
        .check-icon.error { background: #ef4444; color: white; }
        .check-icon.warning { background: #f59e0b; color: white; }
        .check-label { flex: 1; }
        .check-detail { color: #6b7280; font-size: 14px; margin-top: 4px; }
        .summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0; }
        .summary-card { background: #f9fafb; padding: 20px; border-radius: 8px; text-align: center; }
        .summary-card .number { font-size: 36px; font-weight: 700; color: #667eea; }
        .summary-card .label { color: #6b7280; font-size: 14px; margin-top: 5px; }
        .code { background: #1f2937; color: #10b981; padding: 15px; border-radius: 6px; font-family: 'Courier New', monospace; font-size: 13px; overflow-x: auto; margin: 10px 0; }
        .recommendation { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 15px 0; border-radius: 4px; }
        .recommendation h4 { color: #92400e; margin-bottom: 8px; }
        .recommendation p { color: #78350f; font-size: 14px; }
        .footer { background: #f9fafb; padding: 20px 30px; text-align: center; color: #6b7280; font-size: 14px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🔍 Payments Accounting Migration Verification</h1>
            <p>Comprehensive database structure verification report</p>
        </div>
        <div class='content'>";

// Include database configuration
require_once __DIR__ . '/../config/database.php';

$verification_results = [
    'connection' => false,
    'migration_log' => false,
    'payments_table' => false,
    'bookings_columns' => false,
    'conference_columns' => false,
    'site_settings' => false
];

$issues = [];
$warnings = [];
$successes = [];

// ============================================================================
// STEP 1: Check Database Connection
// ============================================================================
echo "<div class='section'>
    <h2>1. Database Connection</h2>";

try {
    $stmt = $pdo->query("SELECT DATABASE() as current_db");
    $result = $stmt->fetch();
    $current_db = $result['current_db'];
    
    echo "<div class='check-item'>
        <div class='check-icon success'>✓</div>
        <div class='check-label'>
            <strong>Database Connection Successful</strong>
            <div class='check-detail'>Connected to: <code>{$current_db}</code></div>
        </div>
    </div>";
    
    $verification_results['connection'] = true;
    $successes[] = "Database connection established to {$current_db}";
    
    // Show connection details
    echo "<table>
        <tr><th>Setting</th><th>Value</th></tr>
        <tr><td>Host</td><td>" . DB_HOST . "</td></tr>
        <tr><td>Database</td><td>" . DB_NAME . "</td></tr>
        <tr><td>Charset</td><td>" . DB_CHARSET . "</td></tr>
    </table>";
    
} catch (PDOException $e) {
    echo "<div class='check-item'>
        <div class='check-icon error'>✗</div>
        <div class='check-label'>
            <strong>Database Connection Failed</strong>
            <div class='check-detail'>Error: " . htmlspecialchars($e->getMessage()) . "</div>
        </div>
    </div>";
    $issues[] = "Database connection failed: " . $e->getMessage();
}

// ============================================================================
// STEP 2: Verify migration_log Table
// ============================================================================
echo "<div class='section'>
    <h2>2. Migration Log Table</h2>";

try {
    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'migration_log'");
    $table_exists = $stmt->rowCount() > 0;
    
    if ($table_exists) {
        echo "<div class='check-item'>
            <div class='check-icon success'>✓</div>
            <div class='check-label'>
                <strong>migration_log table exists</strong>
            </div>
        </div>";
        
        // Check for payments_accounting_system entry
        $stmt = $pdo->prepare("SELECT * FROM migration_log WHERE migration_name = ?");
        $stmt->execute(['payments_accounting_system']);
        $migration_entry = $stmt->fetch();
        
        if ($migration_entry) {
            $status = $migration_entry['status'];
            $executed_at = $migration_entry['executed_at'];
            
            if ($status === 'completed') {
                echo "<div class='check-item'>
                    <div class='check-icon success'>✓</div>
                    <div class='check-label'>
                        <strong>Migration entry found: payments_accounting_system</strong>
                        <div class='check-detail'>Status: <span class='status success'>{$status}</span> | Executed: {$executed_at}</div>
                    </div>
                </div>";
                $verification_results['migration_log'] = true;
                $successes[] = "Migration log entry found with status 'completed'";
            } else {
                echo "<div class='check-item'>
                    <div class='check-icon warning'>⚠</div>
                    <div class='check-label'>
                        <strong>Migration entry found but status is: {$status}</strong>
                        <div class='check-detail'>Executed: {$executed_at}</div>
                    </div>
                </div>";
                $warnings[] = "Migration status is '{$status}' instead of 'completed'";
            }
        } else {
            echo "<div class='check-item'>
                <div class='check-icon error'>✗</div>
                <div class='check-label'>
                    <strong>Migration entry not found</strong>
                    <div class='check-detail'>No entry for 'payments_accounting_system' in migration_log table</div>
                </div>
            </div>";
            $issues[] = "Migration log entry for 'payments_accounting_system' not found";
        }
        
        // Show table structure
        $stmt = $pdo->query("DESCRIBE migration_log");
        $columns = $stmt->fetchAll();
        echo "<table>
            <tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>
                <td><code>{$col['Field']}</code></td>
                <td>{$col['Type']}</td>
                <td>{$col['Null']}</td>
                <td>{$col['Key']}</td>
            </tr>";
        }
        echo "</table>";
        
    } else {
        echo "<div class='check-item'>
            <div class='check-icon error'>✗</div>
            <div class='check-label'>
                <strong>migration_log table does not exist</strong>
            </div>
        </div>";
        $issues[] = "migration_log table not found";
    }
    
} catch (PDOException $e) {
    echo "<div class='check-item'>
        <div class='check-icon error'>✗</div>
        <div class='check-label'>
            <strong>Error checking migration_log</strong>
            <div class='check-detail'>" . htmlspecialchars($e->getMessage()) . "</div>
        </div>
    </div>";
    $issues[] = "Error checking migration_log: " . $e->getMessage();
}

// ============================================================================
// STEP 3: Verify payments Table
// ============================================================================
echo "<div class='section'>
    <h2>3. Payments Table</h2>";

$required_payment_columns = [
    'id', 'booking_id', 'conference_id', 'payment_type', 'amount', 'vat_amount',
    'total_amount', 'payment_method', 'payment_date', 'status', 'transaction_id',
    'invoice_number', 'notes', 'created_at', 'updated_at'
];

try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'payments'");
    $table_exists = $stmt->rowCount() > 0;
    
    if ($table_exists) {
        echo "<div class='check-item'>
            <div class='check-icon success'>✓</div>
            <div class='check-label'>
                <strong>payments table exists</strong>
            </div>
        </div>";
        
        // Check columns
        $stmt = $pdo->query("DESCRIBE payments");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $missing_columns = [];
        foreach ($required_payment_columns as $col) {
            if (!in_array($col, $columns)) {
                $missing_columns[] = $col;
            }
        }
        
        if (empty($missing_columns)) {
            echo "<div class='check-item'>
                <div class='check-icon success'>✓</div>
                <div class='check-label'>
                    <strong>All required columns present</strong>
                    <div class='check-detail'>" . count($required_payment_columns) . " columns verified</div>
                </div>
            </div>";
            $verification_results['payments_table'] = true;
            $successes[] = "payments table exists with all required columns";
        } else {
            echo "<div class='check-item'>
                <div class='check-icon error'>✗</div>
                <div class='check-label'>
                    <strong>Missing columns</strong>
                    <div class='check-detail'>" . implode(', ', $missing_columns) . "</div>
                </div>
            </div>";
            $issues[] = "payments table missing columns: " . implode(', ', $missing_columns);
        }
        
        // Show table structure
        $stmt = $pdo->query("DESCRIBE payments");
        $columns = $stmt->fetchAll();
        echo "<table>
            <tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>
                <td><code>{$col['Field']}</code></td>
                <td>{$col['Type']}</td>
                <td>{$col['Null']}</td>
                <td>{$col['Key']}</td>
                <td>" . ($col['Default'] ?? 'NULL') . "</td>
            </tr>";
        }
        echo "</table>";
        
        // Check row count
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM payments");
        $count = $stmt->fetch()['count'];
        echo "<div class='check-item'>
            <div class='check-icon info'>ℹ</div>
            <div class='check-label'>
                <strong>Current records</strong>
                <div class='check-detail'>{$count} payment record(s) in table</div>
            </div>
        </div>";
        
    } else {
        echo "<div class='check-item'>
            <div class='check-icon error'>✗</div>
            <div class='check-label'>
                <strong>payments table does not exist</strong>
            </div>
        </div>";
        $issues[] = "payments table not found";
    }
    
} catch (PDOException $e) {
    echo "<div class='check-item'>
        <div class='check-icon error'>✗</div>
        <div class='check-label'>
            <strong>Error checking payments table</strong>
            <div class='check-detail'>" . htmlspecialchars($e->getMessage()) . "</div>
        </div>
    </div>";
    $issues[] = "Error checking payments table: " . $e->getMessage();
}

// ============================================================================
// STEP 4: Verify bookings Table Modifications
// ============================================================================
echo "<div class='section'>
    <h2>4. Bookings Table Modifications</h2>";

$required_booking_columns = [
    'payment_status' => "ENUM('pending', 'partial', 'paid', 'refunded')",
    'payment_amount' => 'DECIMAL(10,2)',
    'payment_date' => 'DATE'
];

try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'bookings'");
    $table_exists = $stmt->rowCount() > 0;
    
    if ($table_exists) {
        echo "<div class='check-item'>
            <div class='check-icon success'>✓</div>
            <div class='check-label'>
                <strong>bookings table exists</strong>
            </div>
        </div>";
        
        // Check for new columns
        $stmt = $pdo->query("DESCRIBE bookings");
        $columns = $stmt->fetchAll();
        $column_map = [];
        foreach ($columns as $col) {
            $column_map[$col['Field']] = $col['Type'];
        }
        
        $missing_booking_columns = [];
        foreach ($required_booking_columns as $col => $expected_type) {
            if (!isset($column_map[$col])) {
                $missing_booking_columns[] = $col;
            }
        }
        
        if (empty($missing_booking_columns)) {
            echo "<div class='check-item'>
                <div class='check-icon success'>✓</div>
                <div class='check-label'>
                    <strong>All payment tracking columns present</strong>
                    <div class='check-detail'>payment_status, payment_amount, payment_date</div>
                </div>
            </div>";
            $verification_results['bookings_columns'] = true;
            $successes[] = "bookings table has all payment tracking columns";
        } else {
            echo "<div class='check-item'>
                <div class='check-icon error'>✗</div>
                <div class='check-label'>
                    <strong>Missing payment columns</strong>
                    <div class='check-detail'>" . implode(', ', $missing_booking_columns) . "</div>
                </div>
            </div>";
            $issues[] = "bookings table missing columns: " . implode(', ', $missing_booking_columns);
        }
        
        // Show payment-related columns
        echo "<table>
            <tr><th>Column</th><th>Type</th><th>Status</th></tr>";
        foreach ($required_booking_columns as $col => $expected_type) {
            $exists = isset($column_map[$col]);
            $actual_type = $exists ? $column_map[$col] : 'N/A';
            $status = $exists ? '<span class="status success">Present</span>' : '<span class="status error">Missing</span>';
            echo "<tr>
                <td><code>{$col}</code></td>
                <td>{$actual_type}</td>
                <td>{$status}</td>
            </tr>";
        }
        echo "</table>";
        
    } else {
        echo "<div class='check-item'>
            <div class='check-icon error'>✗</div>
            <div class='check-label'>
                <strong>bookings table does not exist</strong>
            </div>
        </div>";
        $issues[] = "bookings table not found";
    }
    
} catch (PDOException $e) {
    echo "<div class='check-item'>
        <div class='check-icon error'>✗</div>
        <div class='check-label'>
            <strong>Error checking bookings table</strong>
            <div class='check-detail'>" . htmlspecialchars($e->getMessage()) . "</div>
        </div>
    </div>";
    $issues[] = "Error checking bookings table: " . $e->getMessage();
}

// ============================================================================
// STEP 5: Verify conference_inquiries Table Modifications
// ============================================================================
echo "<div class='section'>
    <h2>5. Conference Inquiries Table Modifications</h2>";

$required_conference_columns = [
    'payment_status' => "ENUM('pending', 'deposit_paid', 'full_paid', 'refunded')",
    'deposit_amount' => 'DECIMAL(10,2)',
    'deposit_paid' => 'DECIMAL(10,2)',
    'total_paid' => 'DECIMAL(10,2)'
];

try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'conference_inquiries'");
    $table_exists = $stmt->rowCount() > 0;
    
    if ($table_exists) {
        echo "<div class='check-item'>
            <div class='check-icon success'>✓</div>
            <div class='check-label'>
                <strong>conference_inquiries table exists</strong>
            </div>
        </div>";
        
        // Check for new columns
        $stmt = $pdo->query("DESCRIBE conference_inquiries");
        $columns = $stmt->fetchAll();
        $column_map = [];
        foreach ($columns as $col) {
            $column_map[$col['Field']] = $col['Type'];
        }
        
        $missing_conference_columns = [];
        foreach ($required_conference_columns as $col => $expected_type) {
            if (!isset($column_map[$col])) {
                $missing_conference_columns[] = $col;
            }
        }
        
        if (empty($missing_conference_columns)) {
            echo "<div class='check-item'>
                <div class='check-icon success'>✓</div>
                <div class='check-label'>
                    <strong>All payment tracking columns present</strong>
                    <div class='check-detail'>payment_status, deposit_amount, deposit_paid, total_paid</div>
                </div>
            </div>";
            $verification_results['conference_columns'] = true;
            $successes[] = "conference_inquiries table has all payment tracking columns";
        } else {
            echo "<div class='check-item'>
                <div class='check-icon error'>✗</div>
                <div class='check-label'>
                    <strong>Missing payment columns</strong>
                    <div class='check-detail'>" . implode(', ', $missing_conference_columns) . "</div>
                </div>
            </div>";
            $issues[] = "conference_inquiries table missing columns: " . implode(', ', $missing_conference_columns);
        }
        
        // Show payment-related columns
        echo "<table>
            <tr><th>Column</th><th>Type</th><th>Status</th></tr>";
        foreach ($required_conference_columns as $col => $expected_type) {
            $exists = isset($column_map[$col]);
            $actual_type = $exists ? $column_map[$col] : 'N/A';
            $status = $exists ? '<span class="status success">Present</span>' : '<span class="status error">Missing</span>';
            echo "<tr>
                <td><code>{$col}</code></td>
                <td>{$actual_type}</td>
                <td>{$status}</td>
            </tr>";
        }
        echo "</table>";
        
    } else {
        echo "<div class='check-item'>
            <div class='check-icon error'>✗</div>
            <div class='check-label'>
                <strong>conference_inquiries table does not exist</strong>
            </div>
        </div>";
        $issues[] = "conference_inquiries table not found";
    }
    
} catch (PDOException $e) {
    echo "<div class='check-item'>
        <div class='check-icon error'>✗</div>
        <div class='check-label'>
            <strong>Error checking conference_inquiries table</strong>
            <div class='check-detail'>" . htmlspecialchars($e->getMessage()) . "</div>
        </div>
    </div>";
    $issues[] = "Error checking conference_inquiries table: " . $e->getMessage();
}

// ============================================================================
// STEP 6: Verify site_settings Entries
// ============================================================================
echo "<div class='section'>
    <h2>6. Site Settings (VAT & Payment Configuration)</h2>";

$required_settings = [
    'vat_enabled' => '1',
    'vat_rate' => '16.50',
    'vat_number' => '',
    'payment_terms' => 'Full payment required at booking',
    'invoice_prefix' => 'INV',
    'invoice_start_number' => '1000'
];

try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'site_settings'");
    $table_exists = $stmt->rowCount() > 0;
    
    if ($table_exists) {
        echo "<div class='check-item'>
            <div class='check-icon success'>✓</div>
            <div class='check-label'>
                <strong>site_settings table exists</strong>
            </div>
        </div>";
        
        // Check for required settings
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
        $settings = $stmt->fetchAll();
        $settings_map = [];
        foreach ($settings as $setting) {
            $settings_map[$setting['setting_key']] = $setting['setting_value'];
        }
        
        $missing_settings = [];
        foreach ($required_settings as $key => $default) {
            if (!isset($settings_map[$key])) {
                $missing_settings[] = $key;
            }
        }
        
        if (empty($missing_settings)) {
            echo "<div class='check-item'>
                <div class='check-icon success'>✓</div>
                <div class='check-label'>
                    <strong>All payment/VAT settings present</strong>
                    <div class='check-detail'>" . count($required_settings) . " settings verified</div>
                </div>
            </div>";
            $verification_results['site_settings'] = true;
            $successes[] = "All payment and VAT settings exist in site_settings";
        } else {
            echo "<div class='check-item'>
                <div class='check-icon warning'>⚠</div>
                <div class='check-label'>
                    <strong>Missing settings</strong>
                    <div class='check-detail'>" . implode(', ', $missing_settings) . "</div>
                </div>
            </div>";
            $warnings[] = "site_settings missing: " . implode(', ', $missing_settings);
        }
        
        // Show payment/VAT settings
        echo "<table>
            <tr><th>Setting Key</th><th>Current Value</th><th>Status</th></tr>";
        foreach ($required_settings as $key => $default) {
            $exists = isset($settings_map[$key]);
            $value = $exists ? $settings_map[$key] : 'Not set';
            $status = $exists ? '<span class="status success">Present</span>' : '<span class="status warning">Missing</span>';
            echo "<tr>
                <td><code>{$key}</code></td>
                <td>" . htmlspecialchars($value) . "</td>
                <td>{$status}</td>
            </tr>";
        }
        echo "</table>";
        
    } else {
        echo "<div class='check-item'>
            <div class='check-icon error'>✗</div>
            <div class='check-label'>
                <strong>site_settings table does not exist</strong>
            </div>
        </div>";
        $issues[] = "site_settings table not found";
    }
    
} catch (PDOException $e) {
    echo "<div class='check-item'>
        <div class='check-icon error'>✗</div>
        <div class='check-label'>
            <strong>Error checking site_settings</strong>
            <div class='check-detail'>" . htmlspecialchars($e->getMessage()) . "</div>
        </div>
    </div>";
    $issues[] = "Error checking site_settings: " . $e->getMessage();
}

// ============================================================================
// Summary Section
// ============================================================================
echo "<div class='section'>
    <h2>📊 Verification Summary</h2>";

$total_checks = count($verification_results);
$passed_checks = count(array_filter($verification_results));

echo "<div class='summary'>
    <div class='summary-card'>
        <div class='number'>{$passed_checks}/{$total_checks}</div>
        <div class='label'>Checks Passed</div>
    </div>
    <div class='summary-card'>
        <div class='number'>" . count($successes) . "</div>
        <div class='label'>Successes</div>
    </div>
    <div class='summary-card'>
        <div class='number'>" . count($warnings) . "</div>
        <div class='label'>Warnings</div>
    </div>
    <div class='summary-card'>
        <div class='number'>" . count($issues) . "</div>
        <div class='label'>Issues</div>
    </div>
</div>";

// Overall status
if ($passed_checks === $total_checks && empty($issues)) {
    echo "<div style='background: #d1fae5; border-left: 4px solid #10b981; padding: 20px; border-radius: 8px; margin: 20px 0;'>
        <h3 style='color: #065f46; margin-bottom: 10px;'>✅ Migration Verification: PASSED</h3>
        <p style='color: #047857;'>All database structures have been successfully created and verified. The payments accounting system migration is complete.</p>
    </div>";
} elseif ($passed_checks >= $total_checks / 2) {
    echo "<div style='background: #fef3c7; border-left: 4px solid #f59e0b; padding: 20px; border-radius: 8px; margin: 20px 0;'>
        <h3 style='color: #92400e; margin-bottom: 10px;'>⚠️ Migration Verification: PARTIAL</h3>
        <p style='color: #78350f;'>Some database structures are missing or incomplete. Please review the issues below and run the migration again if needed.</p>
    </div>";
} else {
    echo "<div style='background: #fee2e2; border-left: 4px solid #ef4444; padding: 20px; border-radius: 8px; margin: 20px 0;'>
        <h3 style='color: #991b1b; margin-bottom: 10px;'>❌ Migration Verification: FAILED</h3>
        <p style='color: #7f1d1d;'>Critical database structures are missing. The migration may not have been executed or may have failed.</p>
    </div>";
}

// Show issues if any
if (!empty($issues)) {
    echo "<h3 style='color: #dc2626; margin: 20px 0 10px;'>Issues Found</h3>";
    echo "<ul style='list-style: none; padding: 0;'>";
    foreach ($issues as $issue) {
        echo "<li style='background: #fee2e2; padding: 10px 15px; margin: 5px 0; border-radius: 4px; color: #991b1b;'>• {$issue}</li>";
    }
    echo "</ul>";
}

// Show warnings if any
if (!empty($warnings)) {
    echo "<h3 style='color: #d97706; margin: 20px 0 10px;'>Warnings</h3>";
    echo "<ul style='list-style: none; padding: 0;'>";
    foreach ($warnings as $warning) {
        echo "<li style='background: #fef3c7; padding: 10px 15px; margin: 5px 0; border-radius: 4px; color: #92400e;'>• {$warning}</li>";
    }
    echo "</ul>";
}

// Show successes
if (!empty($successes)) {
    echo "<h3 style='color: #059669; margin: 20px 0 10px;'>Successfully Verified</h3>";
    echo "<ul style='list-style: none; padding: 0;'>";
    foreach ($successes as $success) {
        echo "<li style='background: #d1fae5; padding: 10px 15px; margin: 5px 0; border-radius: 4px; color: #065f46;'>• {$success}</li>";
    }
    echo "</ul>";
}

// ============================================================================
// Recommendations
// ============================================================================
if (!empty($issues) || !empty($warnings)) {
    echo "<div class='section'>
        <h2>💡 Recommendations</h2>";
    
    if (!empty($issues)) {
        echo "<div class='recommendation'>
            <h4>Run Migration Again</h4>
            <p>If critical structures are missing, run the migration script:</p>
            <div class='code'>php Database/run_migration.php</div>
        </div>";
    }
    
    if (in_array('Migration log entry for \'payments_accounting_system\' not found', $issues)) {
        echo "<div class='recommendation'>
            <h4>Manual Migration Log Entry</h4>
            <p>If tables exist but migration log is missing, add it manually:</p>
            <div class='code'>
INSERT INTO migration_log (migration_name, status, executed_at)
VALUES ('payments_accounting_system', 'completed', NOW());
            </div>
        </div>";
    }
    
    if (in_array('site_settings missing', $issues) || in_array('site_settings missing', $warnings)) {
        echo "<div class='recommendation'>
            <h4>Add Missing Settings</h4>
            <p>Run the following SQL to add missing payment/VAT settings:</p>
            <div class='code'>
INSERT INTO site_settings (setting_key, setting_value, setting_group) VALUES
('vat_enabled', '1', 'payment'),
('vat_rate', '16.50', 'payment'),
('vat_number', '', 'payment'),
('payment_terms', 'Full payment required at booking', 'payment'),
('invoice_prefix', 'INV', 'payment'),
('invoice_start_number', '1000', 'payment')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
            </div>
        </div>";
    }
    
    echo "</div>";
}

// ============================================================================
// Footer
// ============================================================================
echo "
        </div>
        <div class='footer'>
            Generated on " . date('Y-m-d H:i:s') . " | Liwonde Sun Hotel Database Verification
        </div>
    </div>
</body>
</html>";
