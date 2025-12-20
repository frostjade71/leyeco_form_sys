<?php
/**
 * Service Request Form (Placeholder)
 * LEYECO III Forms Management System
 */

$page_title = 'Service Request Form';
require_once __DIR__ . '/../includes/header.php';
?>

<style>
    .placeholder-section { padding: var(--spacing-2xl) 0; text-align: center; }
    .placeholder-container { max-width: 600px; margin: 0 auto; }
    .placeholder-icon { font-size: 5rem; color: var(--primary-red); margin-bottom: var(--spacing-lg); }
    .placeholder-title { font-size: 2rem; color: var(--text-dark); margin-bottom: var(--spacing-md); }
    .placeholder-box { background: var(--light-gray); padding: var(--spacing-xl); border-radius: 12px; border-left: 4px solid var(--accent-yellow); margin-bottom: var(--spacing-lg); }
    .placeholder-info { font-size: 1.1rem; color: var(--text-gray); margin-bottom: var(--spacing-md); }
    .placeholder-desc { font-size: 0.95rem; color: var(--text-gray); }
    
    @media (max-width: 768px) {
        .placeholder-section { padding: var(--spacing-lg) 0; }
        .placeholder-icon { font-size: 3rem; margin-bottom: var(--spacing-md); }
        .placeholder-title { font-size: 1.5rem; }
        .placeholder-box { padding: var(--spacing-md); }
        .placeholder-info { font-size: 1rem; }
        .placeholder-desc { font-size: 0.85rem; }
    }
</style>

<section class="placeholder-section">
    <div class="container">
        <div class="placeholder-container">
            <div class="placeholder-icon">
                <i class="fas fa-file-text"></i>
            </div>
            
            <h1 class="placeholder-title">
                Service Request Form
            </h1>
            
            <div class="placeholder-box">
                <p class="placeholder-info">
                    <i class="fas fa-info-circle" style="color: var(--accent-yellow);"></i>
                    This form is currently under development.
                </p>

            </div>
            
            <a href="<?php echo BASE_URL; ?>/public/index.php" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-arrow-left"></i> Back to Homepage
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
