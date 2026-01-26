<?php
require_once 'config/database.php';

try {
    // First, let's see what footer links currently exist
    $stmt = $pdo->query("SELECT * FROM footer_links ORDER BY column_name, display_order");
    $links = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Current Footer Links:\n";
    foreach ($links as $link) {
        echo "ID: " . $link['id'] . ", Column: " . $link['column_name'] . ", Text: " . $link['link_text'] . ", URL: " . $link['link_url'] . "\n";
    }
    
    // Check if About Us link already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM footer_links WHERE link_text = ? AND column_name = ?");
    $stmt->execute(["About Us", "Quick Links"]);
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        // Get the max display_order for Quick Links column
        $stmt = $pdo->prepare("SELECT MAX(display_order) FROM footer_links WHERE column_name = ?");
        $stmt->execute(["Quick Links"]);
        $max_order = $stmt->fetchColumn();
        $new_order = ($max_order !== false && $max_order !== null) ? $max_order + 1 : 1;
        
        // Insert the About Us link
        $stmt = $pdo->prepare("INSERT INTO footer_links (column_name, link_text, link_url, display_order, is_active) VALUES (?, ?, ?, ?, 1)");
        $stmt->execute(["Quick Links", "About Us", "index.php#about", $new_order]);
        
        echo "\nAdded About Us link to footer with display_order: " . $new_order . "\n";
    } else {
        echo "\nAbout Us link already exists in footer.\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>