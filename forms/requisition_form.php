<?php
/**
 * Reconnection Form (Placeholder)
 * LEYECO III Forms Management System
 */

$page_title = 'Requisition Form';
require_once __DIR__ . '/../includes/header.php';
?>

<section style="padding: var(--spacing-2xl) 0; text-align: center;">
    <div class="container">
        <div style="max-width: 600px; margin: 0 auto;">
            <div style="font-size: 5rem; color: var(--primary-red); margin-bottom: var(--spacing-lg);">
                <i class="fas fa-file-alt"></i>
            </div>
            
            <h1 style="font-size: 2rem; color: var(--text-dark); margin-bottom: var(--spacing-md);">
                Requisition Form
            </h1>
            
            <div style="background: var(--light-gray); padding: var(--spacing-xl); border-radius: 12px; border-left: 4px solid var(--accent-yellow); margin-bottom: var(--spacing-lg);">
                <p style="font-size: 1.1rem; color: var(--text-gray); margin-bottom: var(--spacing-md);">
                    <i class="fas fa-info-circle" style="color: var(--accent-yellow);"></i>
                    This form is currently under development.
                </p>
                <p style="font-size: 0.95rem; color: var(--text-gray);">
                    SOON
                </p>
            </div>
            
            <a href="<?php echo BASE_URL; ?>/public/index.php" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-arrow-left"></i> Back to Homepage
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
