<?php
require_once 'config/database.php';

try {
    // Update the existing About Us link in About Hotel column to point to #about
    $stmt = $pdo->prepare("UPDATE footer_links SET link_url = ? WHERE id = ?");
    $stmt->execute(["index.php#about", 1]);
    
    echo "Updated existing About Us link (ID: 1) to point to index.php#about\n";
    
    // Also update the Facilities link to point to #facilities (it already does, but just in case)
    $stmt = $pdo->prepare("UPDATE footer_links SET link_url = ? WHERE link_text = ? AND column_name = ?");
    $stmt->execute(["index.php#facilities", "Facilities", "Guest Services"]);
    
    echo "Updated Facilities link to point to index.php#facilities\n";
    
    // Verify the changes
    $stmt = $pdo->query("SELECT * FROM footer_links WHERE link_text LIKE '%About%' OR link_text = 'Facilities' ORDER BY id");
    $links = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nVerification:\n";
    foreach ($links as $link) {
        echo "ID: " . $link['id'] . ", Column: " . $link['column_name'] . ", Text: " . $link['link_text'] . ", URL: " . $link['link_url'] . "\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>