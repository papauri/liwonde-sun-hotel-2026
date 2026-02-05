</div>

<!-- Admin Footer -->
<footer class="admin-footer" style="background: var(--deep-navy); color: white; padding: 20px; margin-top: 40px; text-align: center;">
    <div class="container">
        <p style="margin: 0; opacity: 0.8;">
            &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(getSetting('site_name', 'Liwonde Sun Hotel')); ?>
            - Admin Panel
        </p>
        <p style="margin: 5px 0 0 0; opacity: 0.6; font-size: 13px;">
            Powered by ProManaged IT
        </p>
    </div>
</footer>

<!-- Scroll to Top -->
<?php if (file_exists(__DIR__ . '/../includes/scroll-to-top.php')): ?>
    <?php include __DIR__ . '/../includes/scroll-to-top.php'; ?>
<?php endif; ?>

</body>
</html>