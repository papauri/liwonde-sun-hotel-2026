<?php
/**
 * Modal Component - Reusable centered modal/popup
 * 
 * Usage:
 * 1. Include this file: <?php include 'includes/modal.php'; ?>
 * 2. Call renderModal() function to display a modal
 * 
 * @param string $id - Unique modal ID (required)
 * @param string $title - Modal title (required)
 * @param string $content - Modal body content (required)
 * @param array $options - Optional parameters:
 *   - 'size': 'sm' (small), 'md' (medium, default), 'lg' (large), 'xl' (extra large)
 *   - 'show_close': true/false (default: true)
 *   - 'close_on_overlay': true/false (default: true)
 *   - 'close_on_escape': true/false (default: true)
 *   - 'footer': Footer content (optional)
 *   - 'class': Additional CSS classes (optional)
 *   - 'attributes': Additional HTML attributes (optional)
 */

if (!function_exists('renderModal')) {
    function renderModal($id, $title, $content, $options = []) {
        // Default options
        $defaults = [
            'size' => 'md',
            'show_close' => true,
            'close_on_overlay' => true,
            'close_on_escape' => true,
            'footer' => null,
            'class' => '',
            'attributes' => ''
        ];
        
        $opts = array_merge($defaults, $options);
        
        // Size classes
        $sizeClasses = [
            'sm' => 'modal-sm',
            'md' => 'modal-md',
            'lg' => 'modal-lg',
            'xl' => 'modal-xl'
        ];
        $sizeClass = $sizeClasses[$opts['size']] ?? 'modal-md';
        
        // Build attributes
        $dataAttrs = '';
        if ($opts['close_on_overlay']) {
            $dataAttrs .= ' data-close-on-overlay="true"';
        }
        if ($opts['close_on_escape']) {
            $dataAttrs .= ' data-close-on-escape="true"';
        }
        if (!empty($opts['attributes'])) {
            $dataAttrs .= ' ' . $opts['attributes'];
        }
        ?>
        <!-- Modal: <?php echo htmlspecialchars($id); ?> -->
        <div class="modal-overlay" id="<?php echo htmlspecialchars($id); ?>-overlay" data-modal-overlay></div>
        <div class="modal-wrapper <?php echo htmlspecialchars($sizeClass); ?> <?php echo htmlspecialchars($opts['class']); ?>" 
             id="<?php echo htmlspecialchars($id); ?>" 
             data-modal<?php echo $dataAttrs; ?>>
            <div class="modal-container">
                <?php if ($opts['show_close']): ?>
                    <button class="modal-close" data-modal-close aria-label="Close modal">
                        <i class="fas fa-times"></i>
                    </button>
                <?php endif; ?>
                
                <?php if (!empty($title)): ?>
                    <div class="modal-header">
                        <h3 class="modal-title"><?php echo $title; ?></h3>
                    </div>
                <?php endif; ?>
                
                <div class="modal-body">
                    <?php echo $content; ?>
                </div>
                
                <?php if (!empty($opts['footer'])): ?>
                    <div class="modal-footer">
                        <?php echo $opts['footer']; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}
?>

<!-- Modal CSS -->
<style>
    :root {
        --modal-overlay-bg: rgba(0, 0, 0, 0.7);
        --modal-bg: #ffffff;
        --modal-border-radius: 12px;
        --modal-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        --modal-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        --modal-header-bg: linear-gradient(135deg, var(--navy, #142841) 0%, var(--deep-navy, #0f1d2e) 100%);
        --modal-header-color: #ffffff;
        --modal-close-color: #ffffff;
        --modal-close-hover: var(--gold, #d4af37);
    }

    /* Modal Overlay */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: var(--modal-overlay-bg);
        z-index: 9998;
        opacity: 0;
        visibility: hidden;
        transition: var(--modal-transition);
        backdrop-filter: blur(4px);
    }

    .modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    /* Modal Wrapper - Centers modal on screen */
    .modal-wrapper {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.9);
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: var(--modal-transition);
        pointer-events: none;
    }

    .modal-wrapper.active {
        opacity: 1;
        visibility: visible;
        transform: translate(-50%, -50%) scale(1);
        pointer-events: auto;
    }

    /* Modal Container */
    .modal-container {
        background: var(--modal-bg);
        border-radius: var(--modal-border-radius);
        box-shadow: var(--modal-shadow);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        max-height: 90vh;
    }

    /* Modal Sizes */
    .modal-sm .modal-container {
        width: 90%;
        max-width: 400px;
    }

    .modal-md .modal-container {
        width: 90%;
        max-width: 500px;
    }

    .modal-lg .modal-container {
        width: 90%;
        max-width: 700px;
    }

    .modal-xl .modal-container {
        width: 90%;
        max-width: 900px;
    }

    /* Modal Close Button */
    .modal-close {
        position: absolute;
        top: 16px;
        right: 16px;
        width: 36px;
        height: 36px;
        border: none;
        background: rgba(255, 255, 255, 0.1);
        color: var(--modal-close-color);
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        transition: all 0.2s ease;
        z-index: 10;
    }

    .modal-close:hover {
        background: var(--modal-close-hover);
        color: var(--deep-navy, #0f1d2e);
        transform: rotate(90deg);
    }

    .modal-close:focus {
        outline: 2px solid var(--gold, #d4af37);
        outline-offset: 2px;
    }

    /* Modal Header */
    .modal-header {
        background: var(--modal-header-bg);
        color: var(--modal-header-color);
        padding: 20px 24px;
        position: relative;
    }

    .modal-title {
        margin: 0;
        font-family: 'Playfair Display', serif;
        font-size: 22px;
        font-weight: 600;
        color: var(--gold, #d4af37);
    }

    /* Modal Body */
    .modal-body {
        padding: 24px;
        overflow-y: auto;
        flex: 1;
    }

    /* Modal Footer */
    .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid #e0e0e0;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        background: #f8f9fa;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .modal-wrapper {
            width: 95%;
        }

        .modal-sm .modal-container,
        .modal-md .modal-container,
        .modal-lg .modal-container,
        .modal-xl .modal-container {
            width: 100%;
            max-width: none;
            max-height: 95vh;
        }

        .modal-header {
            padding: 16px 20px;
        }

        .modal-title {
            font-size: 18px;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            padding: 12px 20px;
            flex-direction: column;
        }

        .modal-footer button,
        .modal-footer a {
            width: 100%;
        }
    }

    /* Animation for modal content */
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-wrapper.active .modal-container {
        animation: modalSlideIn 0.3s ease-out;
    }

    /* Prevent body scroll when modal is open */
    body.modal-open {
        overflow: hidden;
    }
</style>

<!-- Modal JavaScript -->
<script>
(function() {
    'use strict';

    // Modal Controller
    window.Modal = {
        activeModals: [],

        // Open a modal by ID
        open: function(modalId) {
            const modal = document.getElementById(modalId);
            const overlay = document.getElementById(modalId + '-overlay');
            
            if (!modal) {
                console.error('Modal not found:', modalId);
                return;
            }

            // Add to active modals stack
            this.activeModals.push(modalId);

            // Show modal and overlay
            modal.classList.add('active');
            if (overlay) overlay.classList.add('active');

            // Prevent body scroll
            document.body.classList.add('modal-open');

            // Focus first focusable element
            setTimeout(() => {
                const focusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
                if (focusable) focusable.focus();
            }, 100);

            // Trigger custom event
            modal.dispatchEvent(new CustomEvent('modal:open', { detail: { modalId } }));
        },

        // Close a modal by ID
        close: function(modalId) {
            const modal = document.getElementById(modalId);
            const overlay = document.getElementById(modalId + '-overlay');
            
            if (!modal) return;

            // Remove from active modals
            this.activeModals = this.activeModals.filter(id => id !== modalId);

            // Hide modal and overlay
            modal.classList.remove('active');
            if (overlay) overlay.classList.remove('active');

            // Restore body scroll if no modals are open
            if (this.activeModals.length === 0) {
                document.body.classList.remove('modal-open');
            }

            // Trigger custom event
            modal.dispatchEvent(new CustomEvent('modal:close', { detail: { modalId } }));
        },

        // Close all open modals
        closeAll: function() {
            [...this.activeModals].forEach(id => this.close(id));
        },

        // Initialize modal event listeners
            init: function() {
                console.log('Modal.init() called');
            // Close button clicks
            document.querySelectorAll('[data-modal-close]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const modal = btn.closest('[data-modal]');
                    if (modal) this.close(modal.id);
                });
            });

            // Overlay clicks
            document.querySelectorAll('[data-modal-overlay]').forEach(overlay => {
                overlay.addEventListener('click', (e) => {
                    if (e.target === overlay) {
                        const modalId = overlay.id.replace('-overlay', '');
                        const modal = document.getElementById(modalId);
                        if (modal && modal.dataset.closeOnOverlay !== 'false') {
                            this.close(modalId);
                        }
                    }
                });
            });

            // Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.activeModals.length > 0) {
                    const topModalId = this.activeModals[this.activeModals.length - 1];
                    const modal = document.getElementById(topModalId);
                    if (modal && modal.dataset.closeOnEscape !== 'false') {
                        this.close(topModalId);
                    }
                }
            });

            // Open buttons
            document.querySelectorAll('[data-modal-open]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const modalId = btn.dataset.modalOpen;
                    this.open(modalId);
                });
            });
        }
    };

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => Modal.init());
    } else {
        Modal.init();
    }
    
    // Debug: Log when Modal is ready
    console.log('Modal component initialized:', typeof window.Modal);
    console.log('Modal.open function:', typeof window.Modal?.open);
    console.log('Modal.init function:', typeof window.Modal?.init);
})();
</script>
