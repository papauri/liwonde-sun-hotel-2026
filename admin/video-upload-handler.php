<?php
/**
 * Video Upload Handler
 * Provides reusable functions for handling video uploads across the admin panel
 */

if (!defined('ADMIN_ACCESS')) {
    die('Direct access not permitted');
}

/**
 * Upload a video file
 * 
 * @param array $fileInput The $_FILES array element for the video file
 * @param string $category Category subdirectory (e.g., 'events', 'rooms', 'hero')
 * @param int $maxSize Maximum file size in bytes (default: 100MB)
 * @return array|null Returns array with 'path' and 'type' on success, null on failure
 */
function uploadVideo($fileInput, $category = 'general', $maxSize = 104857600) {
    // Validate file input
    if (!$fileInput || !isset($fileInput['tmp_name']) || $fileInput['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    // Check file size
    if ($fileInput['size'] > $maxSize) {
        error_log("Video upload failed: File size exceeds maximum of " . ($maxSize / 1048576) . "MB");
        return null;
    }

    // Allowed video MIME types
    $allowedTypes = [
        'video/mp4',
        'video/webm',
        'video/ogg',
        'video/quicktime', // .mov files
        'video/x-msvideo', // .avi files
        'video/x-matroska' // .mkv files
    ];

    // Get file info
    $fileType = mime_content_type($fileInput['tmp_name']);
    
    // Validate MIME type
    if (!in_array($fileType, $allowedTypes)) {
        error_log("Video upload failed: Invalid file type '$fileType'");
        return null;
    }

    // Create upload directory
    $uploadDir = __DIR__ . '/../videos/' . $category . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generate unique filename
    $extension = pathinfo($fileInput['name'], PATHINFO_EXTENSION);
    if (empty($extension)) {
        // Fallback to extension from MIME type
        $mimeToExt = [
            'video/mp4' => 'mp4',
            'video/webm' => 'webm',
            'video/ogg' => 'ogv',
            'video/quicktime' => 'mov',
            'video/x-msvideo' => 'avi',
            'video/x-matroska' => 'mkv'
        ];
        $extension = $mimeToExt[$fileType] ?? 'mp4';
    }
    
    $filename = 'video_' . time() . '_' . random_int(1000, 9999) . '.' . strtolower($extension);
    $relativePath = 'videos/' . $category . '/' . $filename;
    $destination = $uploadDir . $filename;

    // Move uploaded file
    if (move_uploaded_file($fileInput['tmp_name'], $destination)) {
        return [
            'path' => $relativePath,
            'type' => $fileType,
            'size' => $fileInput['size'],
            'original_name' => $fileInput['name']
        ];
    }

    error_log("Video upload failed: Could not move file to '$destination'");
    return null;
}

/**
 * Delete a video file
 * 
 * @param string $videoPath The relative path to the video file
 * @return bool True on success, false on failure
 */
function deleteVideo($videoPath) {
    if (empty($videoPath)) {
        return false;
    }

    $fullPath = __DIR__ . '/../' . $videoPath;
    
    if (file_exists($fullPath)) {
        return unlink($fullPath);
    }
    
    return false;
}

/**
 * Get video information
 * 
 * @param string $videoPath The relative path to the video file
 * @return array|false Video information or false on failure
 */
function getVideoInfo($videoPath) {
    if (empty($videoPath)) {
        return false;
    }

    $fullPath = __DIR__ . '/../' . $videoPath;
    
    if (!file_exists($fullPath)) {
        return false;
    }

    $fileInfo = [
        'path' => $videoPath,
        'size' => filesize($fullPath),
        'size_formatted' => formatBytes(filesize($fullPath)),
        'type' => mime_content_type($fullPath),
        'url' => '../' . $videoPath
    ];

    return $fileInfo;
}

/**
 * Format bytes to human readable format
 *
 * @param int $bytes File size in bytes
 * @param int $precision Decimal precision
 * @return string Formatted size string
 */
if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

/**
 * Render video upload HTML field
 * 
 * @param string $fieldName Form field name
 * @param string|null $currentVideoPath Current video path (if any)
 * @param string $label Field label
 * @param array $options Additional options
 * @return string HTML for video upload field
 */
function renderVideoUploadField($fieldName, $currentVideoPath = null, $label = 'Video', $options = []) {
    $defaults = [
        'accept' => 'video/*',
        'max_size' => '100MB',
        'required' => false,
        'help_text' => 'Upload a video file (MP4, WebM, OGG - Max 100MB)',
        'show_preview' => true
    ];
    
    $opts = array_merge($defaults, $options);
    $required = $opts['required'] ? 'required' : '';
    $videoInfo = $currentVideoPath ? getVideoInfo($currentVideoPath) : null;
    
    ob_start();
    ?>
    <div class="form-group video-upload-group">
        <label><?php echo htmlspecialchars($label); ?></label>
        
        <?php if ($videoInfo && $opts['show_preview']): ?>
            <div class="current-video-preview" style="margin-bottom: 16px;">
                <div style="background: #f8f9fa; padding: 16px; border-radius: 8px; border: 1px solid #ddd;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                        <i class="fas fa-video" style="font-size: 24px; color: var(--gold);"></i>
                        <div>
                            <strong style="display: block;">Current Video</strong>
                            <small style="color: #666;"><?php echo htmlspecialchars($videoInfo['size_formatted']); ?></small>
                        </div>
                    </div>
                    <video 
                        controls 
                        style="width: 100%; max-width: 400px; border-radius: 8px;"
                        preload="metadata">
                        <source src="<?php echo htmlspecialchars($videoInfo['url']); ?>" type="<?php echo htmlspecialchars($videoInfo['type']); ?>">
                        Your browser does not support video playback.
                    </video>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="video-upload-area" style="border: 2px dashed #ddd; border-radius: 8px; padding: 24px; text-align: center; cursor: pointer; transition: all 0.3s ease; background: #f8f9fa;" onclick="document.getElementById('<?php echo $fieldName; ?>').click()">
            <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: var(--gold); margin-bottom: 12px; display: block;"></i>
            <div style="font-weight: 600; margin-bottom: 4px;">Click to Upload Video</div>
            <div style="font-size: 12px; color: #999;">
                <?php echo htmlspecialchars($opts['help_text']); ?>
            </div>
        </div>
        
        <input 
            type="file" 
            name="<?php echo $fieldName; ?>" 
            id="<?php echo $fieldName; ?>" 
            accept="<?php echo htmlspecialchars($opts['accept']); ?>" 
            style="display: none;"
            onchange="previewVideoFile(this, '<?php echo $fieldName; ?>')"
            <?php echo $required; ?>>
        
        <div id="<?php echo $fieldName; ?>Preview" style="margin-top: 16px; display: none;">
            <div style="background: #e8f5e9; padding: 12px; border-radius: 8px; border: 1px solid #4caf50;">
                <i class="fas fa-check-circle" style="color: #4caf50;"></i>
                <span id="<?php echo $fieldName; ?>FileName" style="font-size: 14px; color: #2e7d32;"></span>
            </div>
        </div>
    </div>
    
    <script>
    function previewVideoFile(input, fieldName) {
        const previewContainer = document.getElementById(fieldName + 'Preview');
        const fileNameSpan = document.getElementById(fieldName + 'FileName');
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            fileNameSpan.textContent = file.name + ' (' + formatBytes(file.size) + ')';
            previewContainer.style.display = 'block';
        } else {
            previewContainer.style.display = 'none';
        }
    }
    
    function formatBytes(bytes) {
        const units = ['B', 'KB', 'MB', 'GB'];
        let size = bytes;
        let unitIndex = 0;
        
        while (size >= 1024 && unitIndex < units.length - 1) {
            size /= 1024;
            unitIndex++;
        }
        
        return size.toFixed(2) + ' ' + units[unitIndex];
    }
    </script>
    <?php
    return ob_get_clean();
}

/**
 * Render video embed for frontend display
 * 
 * @param string|null $videoPath Video path
 * @param string|null $videoType Video MIME type
 * @param array $options Display options
 * @return string HTML for video embed
 */
function renderVideoEmbed($videoPath, $videoType = null, $options = []) {
    if (empty($videoPath)) {
        return '';
    }

    $defaults = [
        'autoplay' => false,
        'muted' => true,
        'controls' => true,
        'loop' => false,
        'class' => 'video-embed',
        'style' => 'width: 100%; height: auto; border-radius: 8px;'
    ];
    
    $opts = array_merge($defaults, $options);
    
    $videoUrl = $videoPath;
    // Add ../ prefix if path doesn't already have it
    if (strpos($videoPath, 'http') !== 0 && strpos($videoPath, '../') !== 0) {
        $videoUrl = '../' . $videoPath;
    }
    
    // Determine video type if not provided
    if (empty($videoType)) {
        $fullPath = __DIR__ . '/../' . $videoPath;
        if (file_exists($fullPath)) {
            $videoType = mime_content_type($fullPath);
        }
    }
    
    if (empty($videoType)) {
        $videoType = 'video/mp4'; // Default fallback
    }
    
    ob_start();
    ?>
    <video 
        class="<?php echo htmlspecialchars($opts['class']); ?>"
        style="<?php echo htmlspecialchars($opts['style']); ?>"
        <?php echo $opts['autoplay'] ? 'autoplay' : ''; ?>
        <?php echo $opts['muted'] ? 'muted' : ''; ?>
        <?php echo $opts['controls'] ? 'controls' : ''; ?>
        <?php echo $opts['loop'] ? 'loop' : ''; ?>
        <?php echo $opts['muted'] ? 'playsinline' : ''; ?>>
        <source src="<?php echo htmlspecialchars($videoUrl); ?>" type="<?php echo htmlspecialchars($videoType); ?>">
        <p>Your browser does not support video playback.</p>
    </video>
    <?php
    return ob_get_clean();
}