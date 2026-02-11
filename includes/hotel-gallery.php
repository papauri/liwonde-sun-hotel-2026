<?php
/**
 * Hotel Gallery Section - Reusable Include
 * Displays a carousel of hotel gallery images with navigation controls
 * 
 * Required Variables:
 * - $gallery_images: Array of gallery image data with keys: image_url, title, description, category
 * - $site_name: Site name for context (optional)
 */

// If gallery_images is not available, try to fetch it
if (!isset($gallery_images) || empty($gallery_images)) {
    try {
        require_once __DIR__ . '/../config/database.php';
        
        // Check if getCachedGalleryImages function exists
        if (function_exists('getCachedGalleryImages')) {
            $gallery_images = getCachedGalleryImages();
        } else {
            // Fallback to direct query
            $stmt = $pdo->query("
                SELECT image_url, title, description, category, video_path, video_type 
                FROM hotel_gallery 
                WHERE is_active = 1 
                ORDER BY display_order ASC
            ");
            $gallery_images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        $gallery_images = [];
        error_log("Error fetching gallery images: " . $e->getMessage());
    }
}

// Helper function to resolve image URLs
if (!function_exists('resolveImageUrl')) {
    function resolveImageUrl($path) {
        if (!$path) return '';
        $trimmed = trim($path);
        if (stripos($trimmed, 'http://') === 0 || stripos($trimmed, 'https://') === 0) {
            return $trimmed;
        }
        return $trimmed;
    }
}
?>


<!-- Passalacqua-Inspired Editorial Gallery Section: Masonry/Grid, Large Images, Minimal Overlay -->
<?php if (!empty($gallery_images)): ?>
<section class="editorial-gallery-section" id="gallery">
    <div class="container">
        <?php 
            $gallery_description = isset($site_name) 
                ? 'Immerse yourself in the beauty and luxury of ' . htmlspecialchars($site_name) 
                : 'Immerse yourself in the beauty and luxury of our hotel';
            renderSectionHeader('hotel_gallery', 'index', [
                'label' => 'Visual Journey',
                'title' => 'Explore Our Hotel',
                'description' => $gallery_description
            ]); 
        ?>
        <div class="editorial-gallery-grid">
            <?php require_once __DIR__ . '/video-display.php'; ?>
            <?php foreach ($gallery_images as $index => $image): ?>
            <div class="editorial-gallery-item">
                <?php if (!empty($image['video_path'])): ?>
                <div class="editorial-gallery-video">
                    <?php echo renderVideoEmbed($image['video_path'], $image['video_type'], [
                        'autoplay' => true,
                        'muted' => true,
                        'controls' => false,
                        'loop' => true,
                        'class' => 'gallery-video-embed',
                        'style' => 'width: 100%; height: 100%; object-fit: cover; border-radius: 0;'
                    ]); ?>
                </div>
                <?php else: ?>
                <img src="<?php echo htmlspecialchars(resolveImageUrl($image['image_url'])); ?>" 
                         alt="<?php echo htmlspecialchars($image['title']); ?>" 
                         loading="lazy">
                <?php endif; ?>
                <div class="editorial-gallery-overlay">
                    <div class="editorial-gallery-content">
                        <h4><?php echo htmlspecialchars($image['title']); ?></h4>
                        <?php if (!empty($image['description'])): ?>
                        <p><?php echo htmlspecialchars($image['description']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($image['category'])): ?>
                        <span class="editorial-gallery-category">
                            <i class="fas fa-tag"></i> <?php echo htmlspecialchars($image['category']); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>