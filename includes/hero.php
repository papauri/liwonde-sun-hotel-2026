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

// Include video display helper
require_once __DIR__ . '/video-display.php';

// Get current page slug from the filename
$page_slug = strtolower(pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME));
$page_slug = str_replace('_', '-', $page_slug);

// Fetch hero data from database
$pageHero = getPageHero($page_slug);

// Only render if hero data exists
if ($pageHero):
?>
<!-- Editorial Hero Section -->
<section class="editorial-hero">
    <?php if (!empty($pageHero['hero_video_path'])): ?>
        <!-- Display video if available -->
        <div class="editorial-hero-bg editorial-hero-video">
            <?php echo renderVideoEmbed($pageHero['hero_video_path'], $pageHero['hero_video_type'], [
                'autoplay' => true,
                'muted' => true,
                'controls' => false,
                'loop' => true,
                'class' => 'editorial-hero-video-embed',
                'style' => 'position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;'
            ]); ?>
        </div>
    <?php else: ?>
        <!-- Display image background if no video -->
        <div class="editorial-hero-bg" style="background-image: url('<?php echo htmlspecialchars($pageHero['hero_image_path']); ?>');"></div>
    <?php endif; ?>
    
    <!-- Multi-layer overlay system for depth -->
    <div class="editorial-hero-overlay editorial-hero-overlay--base"></div>
    <div class="editorial-hero-overlay editorial-hero-overlay--vignette"></div>
    <div class="editorial-hero-overlay editorial-hero-overlay--gradient"></div>
    
    <!-- Decorative line at top -->
    <div class="editorial-hero-top-line"></div>
    
    <!-- Editorial content container -->
    <div class="editorial-hero-container">
        <div class="editorial-hero-content">
            <!-- Editorial label/tag -->
            <div class="editorial-hero-meta">
                <span class="editorial-hero-issue"><?php echo htmlspecialchars($pageHero['hero_subtitle']); ?></span>
                <span class="editorial-hero-divider"></span>
                <time class="editorial-hero-date"><?php echo date('F Y'); ?></time>
            </div>
            
            <!-- Main headline with superior typography -->
            <h1 class="editorial-hero-title"><?php echo htmlspecialchars($pageHero['hero_title']); ?></h1>
            
            <!-- Decorative divider -->
            <div class="editorial-hero-divider-line"></div>
            
            <!-- Lead paragraph with editorial styling -->
            <p class="editorial-hero-lead"><?php echo htmlspecialchars($pageHero['hero_description']); ?></p>
            
            <!-- Optional CTA section -->
            <?php if (!empty($pageHero['primary_cta_text']) && !empty($pageHero['primary_cta_link'])): ?>
            <div class="editorial-hero-cta">
                <a href="<?php echo htmlspecialchars($pageHero['primary_cta_link']); ?>" class="editorial-btn-primary">
                    <span><?php echo htmlspecialchars($pageHero['primary_cta_text']); ?></span>
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </a>
                <?php if (!empty($pageHero['secondary_cta_text']) && !empty($pageHero['secondary_cta_link'])): ?>
                <a href="<?php echo htmlspecialchars($pageHero['secondary_cta_link']); ?>" class="editorial-btn-secondary">
                    <span><?php echo htmlspecialchars($pageHero['secondary_cta_text']); ?></span>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Scroll indicator -->
    <div class="editorial-hero-scroll">
        <span class="scroll-text">Scroll</span>
        <span class="scroll-line"></span>
    </div>
</section>
<?php endif; ?>
