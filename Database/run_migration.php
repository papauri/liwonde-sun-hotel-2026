<?php
/**
 * Database Migration Runner
 * Executes SQL migration files to update database schema
 */

// Include database configuration
require_once __DIR__ . '/../config/database.php';

// Migration file to run
$migrationFile = __DIR__ . '/migration_room_blocked_dates.sql';

echo "========================================\n";
echo "Database Migration Runner\n";
echo "========================================\n\n";

// Check if migration file exists
if (!file_exists($migrationFile)) {
    die("Error: Migration file not found: {$migrationFile}\n");
}

// Read the migration SQL
$sql = file_get_contents($migrationFile);

if ($sql === false) {
    die("Error: Could not read migration file\n");
}

echo "Migration file: " . basename($migrationFile) . "\n";
echo "Database: " . DB_NAME . "\n\n";

// Split SQL into individual statements
$statements = [];
$currentStatement = '';
$inDelimiter = false;
$customDelimiter = ';';

$lines = explode("\n", $sql);

foreach ($lines as $line) {
    $trimmedLine = trim($line);
    
    // Check for delimiter changes
    if (preg_match('/DELIMITER\s+(\S+)/i', $trimmedLine, $matches)) {
        $customDelimiter = $matches[1];
        $inDelimiter = true;
        continue;
    }
    
    // Skip empty lines and comments
    if (empty($trimmedLine) || strpos($trimmedLine, '--') === 0 || strpos($trimmedLine, '#') === 0) {
        continue;
    }
    
    $currentStatement .= $line . "\n";
    
    // Check if statement is complete
    if ($inDelimiter) {
        if (preg_match('/' . preg_quote($customDelimiter, '/') . '\s*$/', $trimmedLine)) {
            $statements[] = rtrim($currentStatement, $customDelimiter . " \t\n\r\0\x0B");
            $currentStatement = '';
            $inDelimiter = false;
            $customDelimiter = ';';
        }
    } else {
        if (substr($trimmedLine, -1) === ';') {
            $statements[] = rtrim($currentStatement, ';');
            $currentStatement = '';
        }
    }
}

// Add any remaining statement
if (!empty(trim($currentStatement))) {
    $statements[] = trim($currentStatement);
}

echo "Found " . count($statements) . " SQL statement(s) to execute\n\n";

// Execute each statement
$successCount = 0;
$errorCount = 0;

try {
    // Start transaction
    $pdo->beginTransaction();
    
    foreach ($statements as $index => $statement) {
        if (empty(trim($statement))) {
            continue;
        }
        
        echo "[" . ($index + 1) . "] Executing...\n";
        
        try {
            $pdo->exec($statement);
            echo "    ✓ Success\n";
            $successCount++;
        } catch (PDOException $e) {
            // Check if it's a "table already exists" error (error 1050)
            if (strpos($e->getMessage(), 'already exists') !== false || 
                strpos($e->getMessage(), '1050') !== false) {
                echo "    ⚠ Table already exists (skipped)\n";
                $successCount++;
            } else {
                echo "    ✗ Error: " . $e->getMessage() . "\n";
                $errorCount++;
                
                // Show the statement that failed
                echo "    Statement: " . substr($statement, 0, 100) . "...\n";
            }
        }
    }
    
    // Commit transaction if all statements succeeded
    if ($errorCount === 0) {
        $pdo->commit();
        echo "\n========================================\n";
        echo "Migration completed successfully!\n";
        echo "========================================\n";
        echo "Statements executed: {$successCount}\n";
        echo "Errors: {$errorCount}\n";
    } else {
        // Rollback on errors
        $pdo->rollBack();
        echo "\n========================================\n";
        echo "Migration completed with errors!\n";
        echo "========================================\n";
        echo "Statements executed: {$successCount}\n";
        echo "Errors: {$errorCount}\n";
        echo "\nTransaction rolled back due to errors.\n";
    }
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "\nFatal Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n";
