<?php
/**
 * LEYECO III Forms Management System
 * System Analytics Dashboard
 */

require_once __DIR__ . '/app/auth_middleware.php';

// Check if user is admin
if ($currentUser['role'] !== 'admin') {
    header('Location: ' . STAFF_URL . '/dashboard.php');
    exit;
}

// Page configuration
$pageTitle = 'System Analytics';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => STAFF_URL . '/dashboard.php'],
    ['label' => 'System Analytics']
];
$additionalCSS = [STAFF_URL . '/assets/css/analytics.css'];

// Fetch analytics data
try {
    // Get complaints by type
    $typeStmt = $conn->query("
        SELECT type, COUNT(*) as count 
        FROM complaints 
        GROUP BY type 
        ORDER BY count DESC
    ");
    $complaintsByType = $typeStmt->fetch_all(MYSQLI_ASSOC);
    
    // Get top municipalities
    $municipalityStmt = $conn->query("
        SELECT municipality, COUNT(*) as count 
        FROM complaints 
        GROUP BY municipality 
        ORDER BY count DESC 
        LIMIT 10
    ");
    $topMunicipalities = $municipalityStmt->fetch_all(MYSQLI_ASSOC);
    
    // Get status distribution
    $statusStmt = $conn->query("
        SELECT status, COUNT(*) as count 
        FROM complaints 
        GROUP BY status
    ");
    $statusDistribution = $statusStmt->fetch_all(MYSQLI_ASSOC);
    
    // Get monthly trends (last 6 months)
    $trendsStmt = $conn->query("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as count
        FROM complaints
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month ASC
    ");
    $monthlyTrends = $trendsStmt->fetch_all(MYSQLI_ASSOC);
    
} catch (Exception $e) {
    error_log("Analytics error: " . $e->getMessage());
    $complaintsByType = [];
    $topMunicipalities = [];
    $statusDistribution = [];
    $monthlyTrends = [];
}

include __DIR__ . '/includes/header.php';
?>

<!-- Analytics Overview Cards -->
<div class="analytics-overview">
    <div class="overview-card">
        <div class="overview-icon" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
            <i class="fas fa-chart-bar"></i>
        </div>
        <div class="overview-content">
            <h3>Total Reports</h3>
            <div class="overview-value"><?php echo number_format(array_sum(array_column($complaintsByType, 'count'))); ?></div>
        </div>
    </div>
    
    <div class="overview-card">
        <div class="overview-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
            <i class="fas fa-map-marker-alt"></i>
        </div>
        <div class="overview-content">
            <h3>Municipalities</h3>
            <div class="overview-value"><?php echo count($topMunicipalities); ?></div>
        </div>
    </div>
    
    <div class="overview-card">
        <div class="overview-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <i class="fas fa-tags"></i>
        </div>
        <div class="overview-content">
            <h3>Report Types</h3>
            <div class="overview-value"><?php echo count($complaintsByType); ?></div>
        </div>
    </div>
    
    <div class="overview-card">
        <div class="overview-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
            <i class="fas fa-clock"></i>
        </div>
        <div class="overview-content">
            <h3>This Month</h3>
            <div class="overview-value"><?php 
                $thisMonth = date('Y-m');
                $thisMonthCount = 0;
                foreach ($monthlyTrends as $trend) {
                    if ($trend['month'] === $thisMonth) {
                        $thisMonthCount = $trend['count'];
                        break;
                    }
                }
                echo number_format($thisMonthCount);
            ?></div>
        </div>
    </div>
</div>

<!-- Charts Grid -->
<div class="charts-grid">
    <!-- Reports by Type -->
    <div class="chart-card">
        <div class="chart-header">
            <h3><i class="fas fa-chart-pie"></i> Reports by Type</h3>
            <p>Distribution of complaints across different categories</p>
        </div>
        <div class="chart-container">
            <canvas id="typeChart"></canvas>
        </div>
    </div>
    
    <!-- Top Municipalities -->
    <div class="chart-card">
        <div class="chart-header">
            <h3><i class="fas fa-chart-bar"></i> Top Municipalities by Report Count</h3>
            <p>Top 10 municipalities with the most reports</p>
        </div>
        <div class="chart-container">
            <canvas id="municipalityChart"></canvas>
        </div>
    </div>
    
    <!-- Status Distribution -->
    <div class="chart-card">
        <div class="chart-header">
            <h3><i class="fas fa-tasks"></i> Status Distribution</h3>
            <p>Current status breakdown of all reports</p>
        </div>
        <div class="chart-container">
            <canvas id="statusChart"></canvas>
        </div>
    </div>
    
    <!-- Monthly Trends -->
    <div class="chart-card chart-card-wide">
        <div class="chart-header">
            <h3><i class="fas fa-chart-line"></i> Monthly Trends</h3>
            <p>Report submissions over the last 6 months</p>
        </div>
        <div class="chart-container">
            <canvas id="trendsChart"></canvas>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Prepare data
const typeData = <?php echo json_encode($complaintsByType); ?>;
const municipalityData = <?php echo json_encode($topMunicipalities); ?>;
const statusData = <?php echo json_encode($statusDistribution); ?>;
const trendsData = <?php echo json_encode($monthlyTrends); ?>;

// Color palette
const colors = {
    primary: '#dc2626',
    blue: '#3b82f6',
    green: '#10b981',
    yellow: '#f59e0b',
    purple: '#8b5cf6',
    pink: '#ec4899',
    orange: '#f97316',
    teal: '#14b8a6',
    indigo: '#6366f1',
    red: '#ef4444'
};

const colorArray = Object.values(colors);

// Chart.js default configuration
Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
Chart.defaults.color = getComputedStyle(document.documentElement).getPropertyValue('--text-primary').trim();

// Reports by Type - Doughnut Chart
const typeCtx = document.getElementById('typeChart').getContext('2d');
new Chart(typeCtx, {
    type: 'doughnut',
    data: {
        labels: typeData.map(item => item.type),
        datasets: [{
            data: typeData.map(item => item.count),
            backgroundColor: colorArray,
            borderWidth: 2,
            borderColor: getComputedStyle(document.documentElement).getPropertyValue('--bg-primary').trim()
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    usePointStyle: true,
                    font: { size: 12 }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// Top Municipalities - Horizontal Bar Chart
const municipalityCtx = document.getElementById('municipalityChart').getContext('2d');
new Chart(municipalityCtx, {
    type: 'bar',
    data: {
        labels: municipalityData.map(item => item.municipality),
        datasets: [{
            label: 'Number of Reports',
            data: municipalityData.map(item => item.count),
            backgroundColor: colors.blue,
            borderRadius: 6,
            barThickness: 30
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Reports: ' + context.parsed.x;
                    }
                }
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: { precision: 0 },
                grid: { display: true, color: 'rgba(0,0,0,0.05)' }
            },
            y: {
                grid: { display: false }
            }
        }
    }
});

// Status Distribution - Pie Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusColors = {
    'NEW': colors.blue,
    'INVESTIGATING': colors.yellow,
    'IN_PROGRESS': colors.purple,
    'RESOLVED': colors.green,
    'CLOSED': colors.red
};

new Chart(statusCtx, {
    type: 'pie',
    data: {
        labels: statusData.map(item => item.status),
        datasets: [{
            data: statusData.map(item => item.count),
            backgroundColor: statusData.map(item => statusColors[item.status] || colors.primary),
            borderWidth: 2,
            borderColor: getComputedStyle(document.documentElement).getPropertyValue('--bg-primary').trim()
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    usePointStyle: true,
                    font: { size: 12 }
                }
            }
        }
    }
});

// Monthly Trends - Line Chart
const trendsCtx = document.getElementById('trendsChart').getContext('2d');
new Chart(trendsCtx, {
    type: 'line',
    data: {
        labels: trendsData.map(item => {
            const date = new Date(item.month + '-01');
            return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        }),
        datasets: [{
            label: 'Reports Submitted',
            data: trendsData.map(item => item.count),
            borderColor: colors.primary,
            backgroundColor: colors.primary + '20',
            fill: true,
            tension: 0.4,
            borderWidth: 3,
            pointRadius: 5,
            pointBackgroundColor: colors.primary,
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                mode: 'index',
                intersect: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0 },
                grid: { color: 'rgba(0,0,0,0.05)' }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
