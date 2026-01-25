<?php
if (function_exists('getSetting')) {
    $siteName = getSetting('site_name');
}
?>
<!-- Fancy Page Loader -->
<div id="page-loader">
    <div class="loader-content">
        <div class="loader-spinner">
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
            <div class="spinner-center"></div>
        </div>
        <div class="loader-text"><?php echo htmlspecialchars($siteName); ?></div>
        <div class="loader-subtext">Loading Excellence...</div>
    </div>
</div>
