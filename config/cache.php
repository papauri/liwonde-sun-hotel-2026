<?php
/**
 * Simple File-Based Caching System
 * Reduces database queries and remote connection overhead
 */

define('CACHE_DIR', __DIR__ . '/../cache');
define('CACHE_ENABLED', true);
define('CACHE_DEFAULT_TTL', 3600); // 1 hour default

/**
 * Get cached value
 */
function getCache($key, $default = null) {
    if (!CACHE_ENABLED) {
        return $default;
    }
    
    $cacheFile = CACHE_DIR . '/' . md5($key) . '.cache';
    
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
 * Set cached value
 */
function setCache($key, $value, $ttl = CACHE_DEFAULT_TTL) {
    if (!CACHE_ENABLED) {
        return false;
    }
    
    // Create cache directory if it doesn't exist
    if (!file_exists(CACHE_DIR)) {
        @mkdir(CACHE_DIR, 0755, true);
    }
    
    $cacheFile = CACHE_DIR . '/' . md5($key) . '.cache';
    $cacheData = [
        'data' => $value,
        'expiry' => time() + $ttl,
        'created' => time()
    ];
    
    $data = json_encode($cacheData);
    return file_put_contents($cacheFile, $data, LOCK_EX) !== false;
}

/**
 * Delete cached value
 */
function deleteCache($key) {
    $cacheFile = CACHE_DIR . '/' . md5($key) . '.cache';
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