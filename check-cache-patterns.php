<?php
require_once 'config/cache.php';

$caches = listCache();
$patterns = [];

foreach($caches as $c) {
    $key = $c['key'];
    if(strpos($key, '_') !== false) {
        $parts = explode('_', $key);
        $pattern = $parts[0] . '_*';
        if(!in_array($pattern, $patterns)) {
            $patterns[] = $pattern;
        }
    }
}

echo "Found cache patterns:\n";
foreach($patterns as $p) {
    echo "- " . $p . "\n";
}

echo "\nAll cache keys:\n";
foreach($caches as $c) {
    echo "- " . $c['key'] . "\n";
}
?>