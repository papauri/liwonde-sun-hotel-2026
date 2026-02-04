<?php
/**
 * Simple File-Based Caching System
 * Reduces database queries and remote connection overhead
 */

define('CACHE_DIR', __DIR__ . '/../cache');
define('CACHE_ENABLED', true);
define('CACHE_DEFAULT_TTL', 3600); // 1 hour default

/**
 * Get cached value with readable filename
 */
function getCache($key, $default = null) {
    if (!CACHE_ENABLED) {
        return $default;
    }
    
    // Generate readable cache filename with prefix
    $cacheFile = CACHE_DIR . '/' . getReadableCacheFilename($key);
    
    if (!file_exists($cacheFile)) {
        return $default;
    }
    
    $data = file_get_contents($cacheFile);
    if ($data === false) {
        return $default;
    }
    
    $cache = json_decode($data, true);
    if (!$cache || !isset($cache['data']) || !isset($cache['expiry'])) {
        return $default;
    }
    
    // Check if expired
    if (time() > $cache['expiry']) {
        @unlink($cacheFile);
        return $default;
    }
    
    return $cache['data'];
}

/**
 * Generate a human-readable cache filename with prefix
 */
function getReadableCacheFilename($key) {
    // Sanitize the key to make it filename-safe
    $sanitized = preg_replace('/[^a-zA-Z0-9_-]/', '_', $key);
    
    // Use the sanitized key as prefix, then add hash for uniqueness
    $hash = md5($key);
    $shortHash = substr($hash, 0, 8); // First 8 chars of hash
    
    return "{$sanitized}_{$shortHash}.cache";
}

/**
 * Set cached value with readable filename
 */
function setCache($key, $value, $ttl = CACHE_DEFAULT_TTL) {
    if (!CACHE_ENABLED) {
        return false;
    }
    
    // Create cache directory if it doesn't exist
    if (!file_exists(CACHE_DIR)) {
        @mkdir(CACHE_DIR, 0755, true);
    }
    
    $cacheFile = CACHE_DIR . '/' . getReadableCacheFilename($key);
    $cacheData = [
        'data' => $value,
        'expiry' => time() + $ttl,
        'created' => time(),
        'key' => $key // Store original key for reference
    ];
    
    $data = json_encode($cacheData);
    return file_put_contents($cacheFile, $data, LOCK_EX) !== false;
}

/**
 * Delete cached value with readable filename
 */
function deleteCache($key) {
    $cacheFile = CACHE_DIR . '/' . getReadableCacheFilename($key);
    if (file_exists($cacheFile)) {
        return @unlink($cacheFile);
    }
    return false;
}

/**
 * Clear all cache
 */
function clearCache() {
    $files = glob(CACHE_DIR . '/*.cache');
    if ($files) {
        foreach ($files as $file) {
            @unlink($file);
        }
    }
    return true;
}

/**
 * List all cache files with their details
 * Returns array of cache information
 */
function listCache() {
    $files = glob(CACHE_DIR . '/*.cache');
    $caches = [];
    
    if ($files) {
        foreach ($files as $file) {
            $data = file_get_contents($file);
            if ($data) {
                $cache = json_decode($data, true);
                if ($cache) {
                    $caches[] = [
                        'file' => basename($file),
                        'key' => $cache['key'] ?? 'unknown',
                        'size' => filesize($file),
                        'size_formatted' => formatBytes(filesize($file)),
                        'created' => $cache['created'] ?? null,
                        'created_formatted' => $cache['created'] ? date('Y-m-d H:i:s', $cache['created']) : 'unknown',
                        'expires' => $cache['expiry'] ?? null,
                        'expires_formatted' => $cache['expiry'] ? date('Y-m-d H:i:s', $cache['expiry']) : 'unknown',
                        'expired' => ($cache['expiry'] ?? 0) < time(),
                        'ttl' => ($cache['expiry'] ?? time()) - time()
                    ];
                }
            }
        }
    }
    
    // Sort by file name for easier reading
    usort($caches, function($a, $b) {
        return strcmp($a['file'], $b['file']);
    });
    
    return $caches;
}

/**
 * Clear cache by key pattern (supports wildcards)
 * Example: clearCacheByPattern('hero_*') clears all hero-related caches
 */
function clearCacheByPattern($pattern) {
    $files = glob(CACHE_DIR . '/*.cache');
    $cleared = 0;
    
    if ($files) {
        // Convert pattern to regex
        $regex = '/^' . str_replace('*', '.*', str_replace('?', '.', $pattern)) . '/';
        
        foreach ($files as $file) {
            $filename = basename($file);
            if (preg_match($regex, $filename)) {
                if (@unlink($file)) {
                    $cleared++;
                }
            }
        }
    }
    
    return $cleared;
}

/**
 * Get cache statistics
 */
function getCacheStats() {
    $files = glob(CACHE_DIR . '/*.cache');
    $stats = [
        'total_files' => 0,
        'total_size' => 0,
        'expired_files' => 0,
        'active_files' => 0,
        'oldest_file' => null,
        'newest_file' => null,
        'caches' => []
    ];
    
    if ($files) {
        $now = time();
        $oldest = PHP_INT_MAX;
        $newest = 0;
        
        foreach ($files as $file) {
            $data = file_get_contents($file);
            if ($data) {
                $cache = json_decode($data, true);
                if ($cache) {
                    $stats['total_files']++;
                    $stats['total_size'] += filesize($file);
                    
                    $created = $cache['created'] ?? 0;
                    if ($created < $oldest) {
                        $oldest = $created;
                        $stats['oldest_file'] = basename($file);
                    }
                    if ($created > $newest) {
                        $newest = $created;
                        $stats['newest_file'] = basename($file);
                    }
                    
                    if (($cache['expiry'] ?? 0) < $now) {
                        $stats['expired_files']++;
                    } else {
                        $stats['active_files']++;
                    }
                }
            }
        }
    }
    
    $stats['total_size_formatted'] = formatBytes($stats['total_size']);
    $stats['oldest_created'] = $oldest !== PHP_INT_MAX ? date('Y-m-d H:i:s', $oldest) : null;
    $stats['newest_created'] = $newest > 0 ? date('Y-m-d H:i:s', $newest) : null;
    
    return $stats;
}

/**
 * Format bytes to human readable format
 */
function formatBytes($size, $precision = 2) {
    if ($size == 0) return '0 B';
    
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $pow = floor(log($size, 1024));
    
    return round($size / pow(1024, $pow), $precision) . ' ' . $units[$pow];
}

/**
 * Clear specific cache by exact key
 * Alias for deleteCache() for consistency
 */
function clearCacheKey($key) {
    return deleteCache($key);
}
