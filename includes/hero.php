<?php
/**
 * Reusable Hero Component
 * Displays hero content from the page_heroes database table
 * 
 * Usage: include 'includes/hero.php';
 * The component automatically detects the current page slug from the filename
 */

// Ensure database connection is available
if (!function_exists('getPageHero')) {
    require_once __DIR__ . '/../config/database.php';
}

// Get current page slug from the filename
$page_slug = strtolower(pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME));
$page_slug = str_replace('_', '-', $page_slug);

// Fetch hero data from database
$pageHero = getPageHero($page_slug);

// Only render if hero data exists
if ($pageHero):
?>
<!-- Hero Section -->
<section class="page-hero" style="background-image: url('<?php echo htmlspecialchars($pageHero['hero_image_path']); ?>');">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <span class="hero-subtitle"><?php echo htmlspecialchars($pageHero['hero_subtitle']); ?></span>
        <h1 class="hero-title"><?php echo htmlspecialchars($pageHero['hero_title']); ?></h1>
        <p class="hero-description"><?php echo htmlspecialchars($pageHero['hero_description']); ?></p>
    </div>
</section>
<?php endif; ?>
