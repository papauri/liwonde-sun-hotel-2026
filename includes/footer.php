<?php require_once 'modal.php'; ?>
<!-- Footer -->
<footer class="footer" id="contact">
    <div class="container">
        <div class="footer-grid">
            <?php
            // Fetch policies from database
            $policies = [];
            try {
                $stmt = $pdo->query("SELECT slug, title, summary, content FROM policies WHERE is_active = 1 ORDER BY display_order ASC");
                $policies = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                // Fallback if table doesn't exist
            }
            
            // Fetch footer links from database
            $footer_links = [];
            try {
                $stmt = $pdo->query("SELECT column_name, link_text, link_url FROM footer_links WHERE is_active = 1 ORDER BY column_name, display_order ASC");
                $all_links = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($all_links as $link) {
                    $footer_links[$link['column_name']][] = $link;
                }
            } catch (PDOException $e) {
                // Fallback if table doesn't exist
            }
            
            // Fetch contact settings
            $contact = [
                'phone_main' => getSetting('phone_main'),
                'email_main' => getSetting('email_main'),
                'address_line1' => getSetting('address_line1'),
                'working_hours' => getSetting('working_hours')
            ];
            
            // Fetch social links
            $social = [
                'facebook_url' => getSetting('facebook_url'),
                'instagram_url' => getSetting('instagram_url'),
                'twitter_url' => getSetting('twitter_url'),
                'linkedin_url' => getSetting('linkedin_url')
            ];
            
            foreach ($footer_links as $column_name => $links): 
            ?>
            <div class="footer-column">
                <h4><?php echo htmlspecialchars($column_name); ?></h4>
                <ul class="footer-links">
                    <?php foreach ($links as $link): ?>
                    <li><a href="<?php echo htmlspecialchars($link['link_url']); ?>"><?php echo htmlspecialchars($link['link_text']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endforeach; ?>

            <div class="footer-column">
                <h4>Policies</h4>
                <ul class="footer-links">
                    <?php if (!empty($policies)): ?>
                        <?php foreach ($policies as $policy): ?>
                            <li><a href="#" class="policy-link" data-policy="<?php echo htmlspecialchars($policy['slug']); ?>"><?php echo htmlspecialchars($policy['title']); ?></a></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li><a href="#" class="policy-link" data-policy="booking-policy">Booking Policy</a></li>
                        <li><a href="#" class="policy-link" data-policy="cancellation-policy">Cancellation</a></li>
                        <li><a href="#" class="policy-link" data-policy="dining-policy">Dining Policy</a></li>
                        <li><a href="#" class="policy-link" data-policy="faqs">FAQs</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="footer-column">
                <h4>Contact Information</h4>
                <ul class="contact-info">
                    <li>
                        <i class="fas fa-phone"></i>
                        <a href="tel:<?php echo htmlspecialchars(preg_replace('/[^0-9+]/', '', $contact['phone_main'])); ?>"><?php echo htmlspecialchars($contact['phone_main']); ?></a>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <a href="mailto:<?php echo htmlspecialchars($contact['email_main']); ?>"><?php echo htmlspecialchars($contact['email_main']); ?></a>
                    </li>
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <a href="https://www.google.com/maps/search/<?php echo urlencode($contact['address_line1']); ?>" target="_blank"><?php echo htmlspecialchars($contact['address_line1']); ?></a>
                    </li>
                    <li>
                        <i class="fas fa-clock"></i>
                        <span><?php echo htmlspecialchars($contact['working_hours']); ?></span>
                    </li>
                </ul>
                
                <div class="social-links">
                    <?php if (!empty($social['facebook_url'])): ?>
                    <a href="<?php echo htmlspecialchars($social['facebook_url']); ?>" class="social-icon" target="_blank">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($social['instagram_url'])): ?>
                    <a href="<?php echo htmlspecialchars($social['instagram_url']); ?>" class="social-icon" target="_blank">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($social['twitter_url'])): ?>
                    <a href="<?php echo htmlspecialchars($social['twitter_url']); ?>" class="social-icon" target="_blank">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($social['linkedin_url'])): ?>
                    <a href="<?php echo htmlspecialchars($social['linkedin_url']); ?>" class="social-icon" target="_blank">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo htmlspecialchars(getSetting('copyright_text')); ?></p>
        </div>
    </div>
</footer>

<?php if (!empty($policies)): ?>
<div class="policy-overlay" data-policy-overlay></div>
<div class="policy-modals">
    <?php foreach ($policies as $policy): ?>
        <?php
        $policyContent = '';
        if (!empty($policy['summary'])) {
            $policyContent = '<p class="policy-summary" style="margin-bottom: 16px; color: #666; font-style: italic;">' . htmlspecialchars($policy['summary']) . '</p>';
        }
        $policyContent .= '<p>' . nl2br(htmlspecialchars($policy['content'])) . '</p>';
        
        renderModal(
            'policy-' . htmlspecialchars($policy['slug']),
            htmlspecialchars($policy['title']),
            $policyContent,
            [
                'size' => 'md',
                'show_close' => true
            ]
        );
        ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>
