<?php
/**
 * Alert Component - Reusable centered alert messages
 * 
 * Usage:
 * 1. Include this file: <?php include 'includes/alert.php'; ?>
 * 2. Call showAlert() function to display an alert
 * 
 * @param string $message - Alert message (required)
 * @param string $type - Alert type: 'success', 'error', 'warning', 'info' (default: 'info')
 * @param array $options - Optional parameters:
 *   - 'dismissible': true/false (default: true)
 *   - 'icon': Custom icon class (default: auto based on type)
 *   - 'timeout': Auto-dismiss after milliseconds (default: 0 = no auto-dismiss)
 *   - 'position': 'top', 'bottom', 'top-left', 'top-right', 'bottom-left', 'bottom-right' (default: 'top')
 *   - 'id': Custom alert ID (optional)
 *   - 'class': Additional CSS classes (optional)
 */

if (!function_exists('showAlert')) {
    function showAlert($message, $type = 'info', $options = []) {
        // Default options
        $defaults = [
            'dismissible' => true,
            'icon' => null,
            'timeout' => 0,
            'position' => 'top',
            'id' => null,
            'class' => ''
        ];
        
        $opts = array_merge($defaults, $options);
        
        // Type configurations
        $typeConfig = [
            'success' => [
                'icon' => 'fa-check-circle',
                'bg' => 'linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%)',
                'border' => '#28a745',
                'color' => '#155724'
            ],
            'error' => [
                'icon' => 'fa-exclamation-circle',
                'bg' => 'linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%)',
                'border' => '#dc3545',
                'color' => '#721c24'
            ],
            'warning' => [
                'icon' => 'fa-exclamation-triangle',
                'bg' => 'linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%)',
                'border' => '#ffc107',
                'color' => '#856404'
            ],
            'info' => [
                'icon' => 'fa-info-circle',
                'bg' => 'linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%)',
                'border' => '#17a2b8',
                'color' => '#0c5460'
            ]
        ];
        
        $config = $typeConfig[$type] ?? $typeConfig['info'];
        $icon = $opts['icon'] ?? $config['icon'];
        $alertId = $opts['id'] ?? 'alert-' . uniqid();
        
        // Position classes
        $positionClasses = [
            'top' => 'alert-top',
            'bottom' => 'alert-bottom',
            'top-left' => 'alert-top-left',
            'top-right' => 'alert-top-right',
            'bottom-left' => 'alert-bottom-left',
            'bottom-right' => 'alert-bottom-right'
        ];
        $positionClass = $positionClasses[$opts['position']] ?? 'alert-top';
        ?>
        <!-- Alert: <?php echo htmlspecialchars($alertId); ?> -->
        <div class="alert-wrapper <?php echo $positionClass; ?>">
            <div class="alert alert-<?php echo htmlspecialchars($type); ?> <?php echo htmlspecialchars($opts['class']); ?>" 
                 id="<?php echo htmlspecialchars($alertId); ?>"
                 data-alert
                 data-alert-type="<?php echo htmlspecialchars($type); ?>"
                 data-alert-timeout="<?php echo (int)$opts['timeout']; ?>"
                 style="--alert-bg: <?php echo $config['bg']; ?>; --alert-border: <?php echo $config['border']; ?>; --alert-color: <?php echo $config['color']; ?>;">
                
                <div class="alert-icon">
                    <i class="fas <?php echo htmlspecialchars($icon); ?>"></i>
                </div>
                
                <div class="alert-content">
                    <?php echo $message; ?>
                </div>
                
                <?php if ($opts['dismissible']): ?>
                    <button class="alert-close" data-alert-close aria-label="Close alert">
                        <i class="fas fa-times"></i>
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}

/**
 * Show alert from session (for flash messages)
 * Usage: <?php showSessionAlert(); ?>
 */
if (!function_exists('showSessionAlert')) {
    function showSessionAlert() {
        if (isset($_SESSION['alert'])) {
            $alert = $_SESSION['alert'];
            unset($_SESSION['alert']);
            showAlert($alert['message'], $alert['type'], $alert['options'] ?? []);
        }
    }
}

/**
 * Set alert in session (for flash messages)
 * Usage: setSessionAlert('Success message', 'success');
 */
if (!function_exists('setSessionAlert')) {
    function setSessionAlert($message, $type = 'info', $options = []) {
        $_SESSION['alert'] = [
            'message' => $message,
            'type' => $type,
            'options' => $options
        ];
    }
}
?>

<!-- Alert CSS -->
<style>
    :root {
        --alert-border-radius: 12px;
        --alert-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        --alert-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        --alert-padding: 16px 20px;
        --alert-font-size: 14px;
    }

    /* Alert Wrapper - Centers alert on screen */
    .alert-wrapper {
        position: fixed;
        z-index: 10000;
        pointer-events: none;
        width: 100%;
        max-width: 600px;
        left: 50%;
        transform: translateX(-50%);
    }

    .alert-wrapper.alert-top {
        top: 20px;
    }

    .alert-wrapper.alert-bottom {
        bottom: 20px;
    }

    .alert-wrapper.alert-top-left {
        top: 20px;
        left: 20px;
        transform: none;
        max-width: 400px;
    }

    .alert-wrapper.alert-top-right {
        top: 20px;
        right: 20px;
        left: auto;
        transform: none;
        max-width: 400px;
    }

    .alert-wrapper.alert-bottom-left {
        bottom: 20px;
        left: 20px;
        transform: none;
        max-width: 400px;
    }

    .alert-wrapper.alert-bottom-right {
        bottom: 20px;
        right: 20px;
        left: auto;
        transform: none;
        max-width: 400px;
    }

    /* Alert Container */
    .alert {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: var(--alert-padding);
        border-radius: var(--alert-border-radius);
        background: var(--alert-bg);
        border-left: 4px solid var(--alert-border);
        color: var(--alert-color);
        box-shadow: var(--alert-shadow);
        pointer-events: auto;
        opacity: 0;
        transform: translateY(-20px);
        transition: var(--alert-transition);
        position: relative;
        overflow: hidden;
    }

    .alert.show {
        opacity: 1;
        transform: translateY(0);
    }

    .alert.hide {
        opacity: 0;
        transform: translateY(-20px);
    }

    /* Alert Icon */
    .alert-icon {
        flex-shrink: 0;
        font-size: 20px;
        margin-top: 2px;
    }

    /* Alert Content */
    .alert-content {
        flex: 1;
        font-size: var(--alert-font-size);
        line-height: 1.5;
        word-wrap: break-word;
    }

    .alert-content strong {
        font-weight: 600;
    }

    /* Alert Close Button */
    .alert-close {
        flex-shrink: 0;
        width: 24px;
        height: 24px;
        border: none;
        background: transparent;
        color: currentColor;
        opacity: 0.6;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.2s ease;
        padding: 0;
        margin-top: -4px;
    }

    .alert-close:hover {
        opacity: 1;
        background: rgba(0, 0, 0, 0.1);
    }

    .alert-close:focus {
        outline: 2px solid var(--alert-border);
        outline-offset: 2px;
    }

    /* Alert Type Specific Styles */
    .alert-success {
        --alert-bg: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        --alert-border: #28a745;
        --alert-color: #155724;
    }

    .alert-error {
        --alert-bg: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        --alert-border: #dc3545;
        --alert-color: #721c24;
    }

    .alert-warning {
        --alert-bg: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        --alert-border: #ffc107;
        --alert-color: #856404;
    }

    .alert-info {
        --alert-bg: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        --alert-border: #17a2b8;
        --alert-color: #0c5460;
    }

    /* Alert Animation */
    @keyframes alertSlideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes alertSlideOut {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-20px);
        }
    }

    .alert.show {
        animation: alertSlideIn 0.3s ease-out forwards;
    }

    .alert.hide {
        animation: alertSlideOut 0.3s ease-out forwards;
    }

    /* Multiple Alerts Stacking */
    .alert-wrapper .alert + .alert {
        margin-top: 12px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .alert-wrapper {
            left: 10px;
            right: 10px;
            width: auto;
            max-width: none;
            transform: none;
        }

        .alert-wrapper.alert-top-left,
        .alert-wrapper.alert-top-right,
        .alert-wrapper.alert-bottom-left,
        .alert-wrapper.alert-bottom-right {
            left: 10px;
            right: 10px;
            max-width: none;
        }

        .alert {
            padding: 14px 16px;
            font-size: 13px;
        }

        .alert-icon {
            font-size: 18px;
        }
    }
</style>

<!-- Alert JavaScript -->
<script>
(function() {
    'use strict';

    // Alert Controller
    const Alert = {
        alerts: [],

        // Show an alert
        show: function(message, type = 'info', options = {}) {
            const defaults = {
                dismissible: true,
                icon: null,
                timeout: 0,
                position: 'top',
                id: null,
                class: ''
            };
            
            const opts = { ...defaults, ...options };
            const alertId = opts.id || 'alert-' + Date.now();
            
            // Create alert element
            const alert = this.createAlert(message, type, opts, alertId);
            
            // Add to DOM
            this.addAlert(alert, opts.position);
            
            // Show with animation
            setTimeout(() => alert.classList.add('show'), 10);
            
            // Auto-dismiss if timeout is set
            if (opts.timeout > 0) {
                setTimeout(() => this.dismiss(alertId), opts.timeout);
            }
            
            return alertId;
        },

        // Create alert element
        createAlert: function(message, type, options, id) {
            const typeConfig = {
                success: { icon: 'fa-check-circle', bg: 'linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%)', border: '#28a745', color: '#155724' },
                error: { icon: 'fa-exclamation-circle', bg: 'linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%)', border: '#dc3545', color: '#721c24' },
                warning: { icon: 'fa-exclamation-triangle', bg: 'linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%)', border: '#ffc107', color: '#856404' },
                info: { icon: 'fa-info-circle', bg: 'linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%)', border: '#17a2b8', color: '#0c5460' }
            };
            
            const config = typeConfig[type] || typeConfig.info;
            const icon = options.icon || config.icon;
            
            const wrapper = document.createElement('div');
            wrapper.className = 'alert-wrapper alert-' + options.position;
            
            const alert = document.createElement('div');
            alert.className = 'alert alert-' + type + ' ' + options.class;
            alert.id = id;
            alert.dataset.alert = '';
            alert.dataset.alertType = type;
            alert.style.setProperty('--alert-bg', config.bg);
            alert.style.setProperty('--alert-border', config.border);
            alert.style.setProperty('--alert-color', config.color);
            
            alert.innerHTML = `
                <div class="alert-icon">
                    <i class="fas ${icon}"></i>
                </div>
                <div class="alert-content">${message}</div>
                ${options.dismissible ? '<button class="alert-close" data-alert-close aria-label="Close alert"><i class="fas fa-times"></i></button>' : ''}
            `;
            
            wrapper.appendChild(alert);
            return wrapper;
        },

        // Add alert to DOM
        addAlert: function(wrapper, position) {
            // Find or create wrapper for position
            let container = document.querySelector('.alert-container-' + position);
            if (!container) {
                container = document.createElement('div');
                container.className = 'alert-container-' + position;
                document.body.appendChild(container);
            }
            container.appendChild(wrapper);
            this.alerts.push(wrapper);
        },

        // Dismiss an alert
        dismiss: function(id) {
            const alert = document.getElementById(id);
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('hide');
                setTimeout(() => {
                    const wrapper = alert.closest('.alert-wrapper');
                    if (wrapper) {
                        wrapper.remove();
                        this.alerts = this.alerts.filter(a => a !== wrapper);
                    }
                }, 300);
            }
        },

        // Dismiss all alerts
        dismissAll: function() {
            [...this.alerts].forEach(wrapper => {
                const alert = wrapper.querySelector('.alert');
                if (alert) {
                    alert.classList.remove('show');
                    alert.classList.add('hide');
                }
            });
            setTimeout(() => {
                document.querySelectorAll('.alert-wrapper').forEach(w => w.remove());
                this.alerts = [];
            }, 300);
        },

        // Initialize alert event listeners
        init: function() {
            // Close button clicks
            document.addEventListener('click', (e) => {
                const closeBtn = e.target.closest('[data-alert-close]');
                if (closeBtn) {
                    const alert = closeBtn.closest('.alert');
                    if (alert) this.dismiss(alert.id);
                }
            });

            // Initialize existing alerts
            document.querySelectorAll('[data-alert]').forEach(alert => {
                const timeout = parseInt(alert.dataset.alertTimeout) || 0;
                setTimeout(() => alert.classList.add('show'), 10);
                
                if (timeout > 0) {
                    setTimeout(() => this.dismiss(alert.id), timeout);
                }
            });
        }
    };

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => Alert.init());
    } else {
        Alert.init();
    }

    // Expose to global scope
    window.Alert = Alert;
})();
</script>
