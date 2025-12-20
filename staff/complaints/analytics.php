<?php
/**
 * LEYECO III Forms Management System
 * Complaints Analytics Dashboard
 */

require_once __DIR__ . '/../app/auth_middleware.php';
requireAdmin(); // Only admins can access

require_once __DIR__ . '/../../forms/complaints/app/ComplaintController.php';

// Page configuration
$pageTitle = 'Complaints Analytics';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => STAFF_URL . '/dashboard.php'],
    ['label' => 'Complaints', 'url' => STAFF_URL . '/complaints/dashboard.php'],
    ['label' => 'Analytics']
];
$additionalCSS = [
    STAFF_URL . '/assets/css/components.css?v=' . time(),
    STAFF_URL . '/assets/css/dashboard.css?v=' . time(),
    STAFF_URL . '/complaints/assets/css/analytics.css?v=' . time()
];

// Initialize controller
$controller = new ComplaintController();

// Get statistics and analytics
$stats = $controller->getStatistics();
$analytics = $controller->getAnalyticsData();
$staffPerformance = $controller->getStaffPerformance();

include __DIR__ . '/../includes/header.php';
?>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<!-- Statistics Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-card-title">Total Complaints</span>
            <div class="stat-card-icon total">
                <i class="fas fa-list"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($stats['total'] ?? 0); ?></div>
        <div class="stat-card-footer">All time</div>
    </div>

    <div class="stat-card resolved">
        <div class="stat-card-header">
            <span class="stat-card-title">Resolved</span>
            <div class="stat-card-icon resolved">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($stats['by_status']['RESOLVED'] ?? 0); ?></div>
        <div class="stat-card-footer">
            <?php 
            $resolvedRate = $stats['total'] > 0 ? round((($stats['by_status']['RESOLVED'] ?? 0) / $stats['total']) * 100, 1) : 0;
            echo $resolvedRate . '% resolution rate';
            ?>
        </div>
    </div>

    <div class="stat-card closed">
        <div class="stat-card-header">
            <span class="stat-card-title">Closed</span>
            <div class="stat-card-icon closed">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($stats['by_status']['CLOSED'] ?? 0); ?></div>
        <div class="stat-card-footer">Completed cases</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-card-title">Avg Resolution Time</span>
            <div class="stat-card-icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo $analytics['avg_resolution_days'] ?? 0; ?></div>
        <div class="stat-card-footer">days</div>
    </div>
</div>

<!-- Charts Row -->
<div class="charts-row">
    <!-- Top Municipalities Chart -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-map-marker-alt"></i> Top Municipalities by Complaints</h3>
        </div>
        <div class="chart-container">
            <canvas id="municipalitiesChart"></canvas>
        </div>
    </div>

    <!-- Top Complaint Types Chart -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-tags"></i> Top Complaint Types</h3>
        </div>
        <div class="chart-container">
            <canvas id="typesChart"></canvas>
        </div>
    </div>
</div>

<!-- Monthly Trends Chart -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-chart-line"></i> Monthly Trends (Last 6 Months)</h3>
    </div>
    <div class="chart-container">
        <canvas id="trendsChart"></canvas>
    </div>
</div>

<!-- Section Spacing -->
<div style="margin: 40px 0;"></div>

<!-- Staff Performance Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-trophy"></i> Top Performing Staff</h3>
        <p class="card-description" style="color: white; font-size: 14px; margin-top: 8px;">
            Based on status updates, dispatch updates, and comments
        </p>
    </div>
    <div class="chart-container">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Staff Member</th>
                        <th>Status Updates</th>
                        <th>Dispatch Updates</th>
                        <th>Comments</th>
                        <th>Total Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($staffPerformance)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-secondary);">
                                No staff activity found
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($staffPerformance as $staff): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($staff['full_name']); ?></strong></td>
                                <td><span class="badge badge-info"><?php echo $staff['status_updates']; ?></span></td>
                                <td><span class="badge badge-warning"><?php echo $staff['dispatch_updates']; ?></span></td>
                                <td><span class="badge badge-success"><?php echo $staff['comment_count']; ?></span></td>
                                <td><strong><?php echo $staff['total_actions']; ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Get current theme colors
function getThemeColors() {
    const styles = getComputedStyle(document.documentElement);
    return {
        textPrimary: styles.getPropertyValue('--text-primary').trim(),
        textSecondary: styles.getPropertyValue('--text-secondary').trim(),
        borderColor: styles.getPropertyValue('--border-color').trim(),
        isDark: document.documentElement.getAttribute('data-theme') === 'dark'
    };
}

// Chart.js configuration with dynamic theme support
Chart.defaults.font.family = "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif";

// Function to get chart options with theme colors
function getChartOptions(type, customOptions = {}) {
    const colors = getThemeColors();
    
    const baseOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    color: colors.textPrimary,
                    font: {
                        size: 12
                    }
                }
            }
        }
    };
    
    // Add scales for bar and line charts
    if (type === 'bar' || type === 'line') {
        baseOptions.scales = {
            x: {
                ticks: {
                    color: colors.textSecondary
                },
                grid: {
                    color: colors.borderColor,
                    borderColor: colors.borderColor
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    color: colors.textSecondary,
                    stepSize: 1
                },
                grid: {
                    color: colors.borderColor,
                    borderColor: colors.borderColor
                }
            }
        };
    }
    
    // Merge with custom options
    return Object.assign({}, baseOptions, customOptions);
}

// Store chart instances
let municipalitiesChart, typesChart, trendsChart;

// Function to create/update charts
function createCharts() {
    const colors = getThemeColors();
    
    // Top Municipalities Chart
    const municipalitiesData = <?php echo json_encode($analytics['top_municipalities'] ?? []); ?>;
    if (municipalitiesData.length > 0) {
        const ctx1 = document.getElementById('municipalitiesChart');
        if (municipalitiesChart) {
            municipalitiesChart.destroy();
        }
        municipalitiesChart = new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: municipalitiesData.map(item => item.municipality),
                datasets: [{
                    label: 'Complaints',
                    data: municipalitiesData.map(item => item.count),
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: getChartOptions('bar', {
                plugins: {
                    legend: {
                        display: false
                    }
                }
            })
        });
    }
    
    // Top Complaint Types Chart
    const typesData = <?php echo json_encode($analytics['top_types'] ?? []); ?>;
    if (typesData.length > 0) {
        const ctx2 = document.getElementById('typesChart');
        if (typesChart) {
            typesChart.destroy();
        }
        typesChart = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: typesData.map(item => item.type),
                datasets: [{
                    data: typesData.map(item => item.count),
                    backgroundColor: [
                        'rgba(220, 38, 38, 0.8)',
                        'rgba(251, 146, 60, 0.8)',
                        'rgba(250, 204, 21, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(148, 163, 184, 0.8)',
                        'rgba(99, 102, 241, 0.8)',
                        'rgba(14, 165, 233, 0.8)'
                    ],
                    borderWidth: 2,
                    borderColor: colors.isDark ? '#1f2937' : '#ffffff'
                }]
            },
            options: getChartOptions('doughnut', {
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: colors.textPrimary
                        }
                    }
                }
            })
        });
    }
    
    // Monthly Trends Chart
    const trendsData = <?php echo json_encode(array_reverse($analytics['monthly_trends'] ?? [])); ?>;
    if (trendsData.length > 0) {
        const ctx3 = document.getElementById('trendsChart');
        if (trendsChart) {
            trendsChart.destroy();
        }
        trendsChart = new Chart(ctx3, {
            type: 'line',
            data: {
                labels: trendsData.map(item => {
                    const date = new Date(item.month + '-01');
                    return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
                }),
                datasets: [
                    {
                        label: 'Total Complaints',
                        data: trendsData.map(item => item.total),
                        borderColor: 'rgba(220, 38, 38, 1)',
                        backgroundColor: 'rgba(220, 38, 38, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Resolved',
                        data: trendsData.map(item => item.resolved),
                        borderColor: 'rgba(34, 197, 94, 1)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Closed',
                        data: trendsData.map(item => item.closed),
                        borderColor: 'rgba(148, 163, 184, 1)',
                        backgroundColor: 'rgba(148, 163, 184, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: getChartOptions('line', {
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: colors.textPrimary
                        }
                    }
                }
            })
        });
    }
}

// Initial chart creation
createCharts();

// Listen for theme changes
const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
        if (mutation.type === 'attributes' && mutation.attributeName === 'data-theme') {
            createCharts();
        }
    });
});

observer.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['data-theme']
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
