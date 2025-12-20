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
    
    // Page Visits Analytics
    
    // Total Page Views (all time)
    $totalPageViewsStmt = $conn->query("
        SELECT COUNT(*) as count 
        FROM page_visits 
        WHERE page = 'public/index.php'
    ");
    $totalPageViews = $totalPageViewsStmt ? $totalPageViewsStmt->fetch_assoc()['count'] : 0;
    
    // Total Page Views (Daily)
    $dailyPageViewsStmt = $conn->query("
        SELECT COUNT(*) as count 
        FROM page_visits 
        WHERE page = 'public/index.php' 
        AND DATE(visited_at) = CURDATE()
    ");
    $dailyPageViews = $dailyPageViewsStmt ? $dailyPageViewsStmt->fetch_assoc()['count'] : 0;
    
    // Total Page Views (Weekly)
    $weeklyPageViewsStmt = $conn->query("
        SELECT COUNT(*) as count 
        FROM page_visits 
        WHERE page = 'public/index.php' 
        AND visited_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ");
    $weeklyPageViews = $weeklyPageViewsStmt ? $weeklyPageViewsStmt->fetch_assoc()['count'] : 0;
    
    // Daily Page Visits for Last 7 Days (for chart)
    $dailyVisitsStmt = $conn->query("
        SELECT 
            DATE(visited_at) as visit_date,
            COUNT(*) as count
        FROM page_visits
        WHERE page = 'public/index.php'
        AND visited_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
        GROUP BY DATE(visited_at)
        ORDER BY visit_date ASC
    ");
    $dailyVisitsData = $dailyVisitsStmt ? $dailyVisitsStmt->fetch_all(MYSQLI_ASSOC) : [];
    
    // Fill in missing days with 0 counts
    $dailyVisits = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $found = false;
        foreach ($dailyVisitsData as $row) {
            if ($row['visit_date'] === $date) {
                $dailyVisits[] = ['date' => $date, 'count' => (int)$row['count']];
                $found = true;
                break;
            }
        }
        if (!$found) {
            $dailyVisits[] = ['date' => $date, 'count' => 0];
        }
    }
    
    // Staff Analytics - Using existing tables

    
    // Active Staff Today (staff who created a session today)
    $activeStaffTodayStmt = $conn->query("
        SELECT COUNT(DISTINCT user_id) as count 
        FROM sessions 
        WHERE DATE(created_at) = CURDATE()
    ");
    $activeStaffToday = $activeStaffTodayStmt ? $activeStaffTodayStmt->fetch_assoc()['count'] : 0;
    
    // Active Staff This Week (staff who created a session in the last 7 days)
    $activeStaffWeekStmt = $conn->query("
        SELECT COUNT(DISTINCT user_id) as count 
        FROM sessions 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ");
    $activeStaffWeek = $activeStaffWeekStmt ? $activeStaffWeekStmt->fetch_assoc()['count'] : 0;
    
    // New Staff Accounts (created in the last 30 days)
    $newStaffStmt = $conn->query("
        SELECT COUNT(*) as count 
        FROM users 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ");
    $newStaffAccounts = $newStaffStmt ? $newStaffStmt->fetch_assoc()['count'] : 0;
    
    // Daily Staff Activity for Last 7 Days (for chart)
    $dailyStaffActivityStmt = $conn->query("
        SELECT 
            DATE(created_at) as activity_date,
            COUNT(DISTINCT user_id) as count
        FROM sessions
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
        GROUP BY DATE(created_at)
        ORDER BY activity_date ASC
    ");
    $dailyStaffActivityData = $dailyStaffActivityStmt ? $dailyStaffActivityStmt->fetch_all(MYSQLI_ASSOC) : [];
    
    // Fill in missing days with 0 counts
    $dailyStaffActivity = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $found = false;
        foreach ($dailyStaffActivityData as $row) {
            if ($row['activity_date'] === $date) {
                $dailyStaffActivity[] = ['date' => $date, 'count' => (int)$row['count']];
                $found = true;
                break;
            }
        }
        if (!$found) {
            $dailyStaffActivity[] = ['date' => $date, 'count' => 0];
        }
    }
    
    // Staff by Role Distribution (for chart)
    $staffByRoleStmt = $conn->query("
        SELECT 
            role,
            COUNT(*) as count
        FROM users
        GROUP BY role
        ORDER BY count DESC
    ");
    $staffByRole = $staffByRoleStmt ? $staffByRoleStmt->fetch_all(MYSQLI_ASSOC) : [];
    
    // System Status for Welcome Section
    // Total Submissions (Complaints + Requisitions)
    $totalComplaints = $conn->query("SELECT COUNT(*) as count FROM complaints")->fetch_assoc()['count'] ?? 0;
    $totalRequisitions = $conn->query("SELECT COUNT(*) as count FROM requisition_requests")->fetch_assoc()['count'] ?? 0;
    $totalSubmissions = $totalComplaints + $totalRequisitions;
    
    $totalUsers = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'] ?? 0;
    $onlineStaff = $conn->query("
        SELECT COUNT(DISTINCT s.user_id) as count 
        FROM sessions s 
        WHERE s.expires_at > NOW()
    ")->fetch_assoc()['count'] ?? 0;
    
    // Database Connection Status
    $dbConnected = true; // If we got here, connection is successful
    
} catch (Exception $e) {
    error_log("Analytics error: " . $e->getMessage());
    $complaintsByType = [];
    $topMunicipalities = [];
    $statusDistribution = [];
    $monthlyTrends = [];
    
    // Set default values for new analytics
    $totalPageViews = 0;
    $dailyPageViews = 0;
    $weeklyPageViews = 0;
    $dailyVisits = [];
    $activeStaffToday = 0;
    $activeStaffWeek = 0;
    $newStaffAccounts = 0;
    $dailyStaffActivity = [];
    $staffByRole = [];
    $totalComplaints = 0;
    $totalRequisitions = 0;
    $totalSubmissions = 0;
    $totalUsers = 0;
    $onlineStaff = 0;
    $dbConnected = false; // Connection failed if we're in catch block
}

include __DIR__ . '/includes/header.php';
?>

<!-- Welcome Section -->
<div class="welcome-section">
    <div class="welcome-content">
        <div class="welcome-text">
            <div class="datetime-display">
                <div class="date-text" id="currentDate"></div>
                <div class="time-text" id="currentTime"></div>
            </div>
        </div>
        <div class="system-status">
            <div class="status-item">
                <div class="status-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="status-info">
                    <span class="status-value"><?php echo number_format($totalSubmissions); ?></span>
                    <span class="status-label">Total Submissions</span>
                </div>
            </div>
            <div class="status-item">
                <div class="status-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="status-info">
                    <span class="status-value"><?php echo number_format($totalUsers); ?></span>
                    <span class="status-label">Total Users</span>
                </div>
            </div>
            <div class="status-item">
                <div class="status-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="status-info">
                    <span class="status-value"><?php echo number_format($onlineStaff); ?></span>
                    <span class="status-label">Online Now</span>
                </div>
            </div>
            <div class="status-item db-status">
                <div class="status-icon" style="background: linear-gradient(135deg, <?php echo $dbConnected ? '#10b981 0%, #059669' : '#ef4444 0%, #dc2626'; ?> 100%);">
                    <i class="fas fa-database"></i>
                </div>
                <div class="status-info">
                    <span class="status-value db-indicator">
                        <span class="db-dot <?php echo $dbConnected ? 'connected' : 'disconnected'; ?>"></span>
                        <?php echo $dbConnected ? 'Connected' : 'Disconnected'; ?>
                    </span>
                    <span class="status-label">Database</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateDateTime() {
    const now = new Date();
    
    // Format date: December 15, 2025
    const dateOptions = { year: 'numeric', month: 'long', day: 'numeric' };
    const dateStr = now.toLocaleDateString('en-US', dateOptions);
    
    // Format time: 1:16 PM
    const timeOptions = { hour: 'numeric', minute: '2-digit', hour12: true };
    const timeStr = now.toLocaleTimeString('en-US', timeOptions);
    
    document.getElementById('currentDate').textContent = dateStr;
    document.getElementById('currentTime').textContent = timeStr;
}

// Update immediately and then every second
updateDateTime();
setInterval(updateDateTime, 1000);
</script>

<!-- Page Visits Section -->
<div class="analytics-section-card">
    <div class="analytics-card-header">
        <h2><i class="fas fa-eye"></i> Page Visits</h2>
        <p class="header-description">Homepage visitor analytics</p>
    </div>
    <div class="analytics-card-body">
        <div class="analytics-overview">
            <div class="overview-card">
                <div class="overview-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                    <i class="fas fa-globe"></i>
                </div>
                <div class="overview-content">
                    <h3>Total Page Views</h3>
                    <div class="overview-value"><?php echo number_format($totalPageViews); ?></div>
                    <p class="overview-label">All time visits</p>
                </div>
            </div>
            
            <div class="overview-card">
                <div class="overview-icon" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="overview-content">
                    <h3>Total Page Views (Daily)</h3>
                    <div class="overview-value"><?php echo number_format($dailyPageViews); ?></div>
                    <p class="overview-label">Today's visits</p>
                </div>
            </div>
            
            <div class="overview-card">
                <div class="overview-icon" style="background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <div class="overview-content">
                    <h3>Total Page Views (Weekly)</h3>
                    <div class="overview-value"><?php echo number_format($weeklyPageViews); ?></div>
                    <p class="overview-label">Last 7 days</p>
                </div>
            </div>
        </div>
        
        <!-- Page Visits Charts -->
        <div class="charts-grid">
            <!-- Daily Visits Bar Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-bar"></i> Daily Visits (Last 7 Days)</h3>
                    <p>Homepage visits per day for the past week</p>
                </div>
                <div class="chart-container">
                    <canvas id="dailyVisitsChart"></canvas>
                </div>
            </div>
            
            <!-- Page Views Comparison -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-pie"></i> Page Views Overview</h3>
                    <p>Comparison of total, weekly, and daily page views</p>
                </div>
                <div class="chart-container">
                    <canvas id="pageViewsComparisonChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Staff Analytics Section -->
<div class="analytics-section-card">
    <div class="analytics-card-header">
        <h2><i class="fas fa-users-cog"></i> Staff Analytics</h2>
        <p class="header-description">Staff activity and account statistics</p>
    </div>
    <div class="analytics-card-body">
        <div class="analytics-overview">
            <div class="overview-card">
                <div class="overview-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="overview-content">
                    <h3>Active Staff Today</h3>
                    <div class="overview-value"><?php echo number_format($activeStaffToday); ?></div>
                    <p class="overview-label">Online today</p>
                </div>
            </div>
            
            <div class="overview-card">
                <div class="overview-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="overview-content">
                    <h3>Active Staff This Week</h3>
                    <div class="overview-value"><?php echo number_format($activeStaffWeek); ?></div>
                    <p class="overview-label">Last 7 days</p>
                </div>
            </div>
            
            <div class="overview-card">
                <div class="overview-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="overview-content">
                    <h3>New Staff Accounts</h3>
                    <div class="overview-value"><?php echo number_format($newStaffAccounts); ?></div>
                    <p class="overview-label">Last 30 days</p>
                </div>
            </div>
        </div>
        
        <!-- Staff Analytics Charts -->
        <div class="charts-grid">
            <!-- Daily Staff Activity Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-line"></i> Daily Staff Activity (Last 7 Days)</h3>
                    <p>Active staff members per day for the past week</p>
                </div>
                <div class="chart-container">
                    <canvas id="dailyStaffActivityChart"></canvas>
                </div>
            </div>
            
            <!-- Staff by Role Distribution -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-user-tag"></i> Staff Distribution by Role</h3>
                    <p>Breakdown of staff members by their assigned roles</p>
                </div>
                <div class="chart-container">
                    <canvas id="staffByRoleChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Complaints Analytics Section -->
<div class="analytics-section-card">
    <div class="analytics-card-header">
        <h2><i class="fas fa-chart-line"></i> Complaints Analytics</h2>
        <p class="header-description">Overview of complaint reports and statistics</p>
    </div>
    <div class="analytics-card-body">
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

// Page Visits data
const dailyVisitsData = <?php echo json_encode($dailyVisits); ?>;
const totalPageViews = <?php echo $totalPageViews; ?>;
const weeklyPageViews = <?php echo $weeklyPageViews; ?>;
const dailyPageViews = <?php echo $dailyPageViews; ?>;

// Staff Analytics data
const dailyStaffActivityData = <?php echo json_encode($dailyStaffActivity); ?>;
const staffByRoleData = <?php echo json_encode($staffByRole); ?>;

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

// ============================================
// PAGE VISITS CHARTS
// ============================================

// Daily Visits Bar Chart (Last 7 Days)
const dailyVisitsCtx = document.getElementById('dailyVisitsChart').getContext('2d');
new Chart(dailyVisitsCtx, {
    type: 'bar',
    data: {
        labels: dailyVisitsData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        }),
        datasets: [{
            label: 'Visits',
            data: dailyVisitsData.map(item => item.count),
            backgroundColor: colors.purple,
            borderRadius: 6,
            barThickness: 40
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Visits: ' + context.parsed.y;
                    }
                }
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

// Page Views Comparison Doughnut Chart
const pageViewsComparisonCtx = document.getElementById('pageViewsComparisonChart').getContext('2d');
new Chart(pageViewsComparisonCtx, {
    type: 'doughnut',
    data: {
        labels: ['Today', 'This Week (Remaining)', 'All Time (Remaining)'],
        datasets: [{
            data: [
                dailyPageViews,
                Math.max(0, weeklyPageViews - dailyPageViews),
                Math.max(0, totalPageViews - weeklyPageViews)
            ],
            backgroundColor: [colors.teal, colors.blue, colors.purple],
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
                        const total = totalPageViews;
                        const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// ============================================
// STAFF ANALYTICS CHARTS
// ============================================

// Daily Staff Activity Line Chart (Last 7 Days)
const dailyStaffActivityCtx = document.getElementById('dailyStaffActivityChart').getContext('2d');
new Chart(dailyStaffActivityCtx, {
    type: 'line',
    data: {
        labels: dailyStaffActivityData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        }),
        datasets: [{
            label: 'Active Staff',
            data: dailyStaffActivityData.map(item => item.count),
            borderColor: colors.green,
            backgroundColor: colors.green + '20',
            fill: true,
            tension: 0.4,
            borderWidth: 3,
            pointRadius: 5,
            pointBackgroundColor: colors.green,
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
                intersect: false,
                callbacks: {
                    label: function(context) {
                        return 'Active Staff: ' + context.parsed.y;
                    }
                }
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

// Staff by Role Distribution Doughnut Chart
const staffByRoleCtx = document.getElementById('staffByRoleChart').getContext('2d');
const roleColors = {
    'admin': colors.red,
    'staff': colors.blue,
    'approver': colors.orange
};

new Chart(staffByRoleCtx, {
    type: 'doughnut',
    data: {
        labels: staffByRoleData.map(item => {
            // Handle comma-separated roles (e.g., "staff, approver")
            return item.role.split(',').map(role => 
                role.trim().charAt(0).toUpperCase() + role.trim().slice(1)
            ).join(', ');
        }),
        datasets: [{
            data: staffByRoleData.map(item => item.count),
            backgroundColor: staffByRoleData.map(item => roleColors[item.role] || colors.primary),
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
                        const total = staffByRoleData.reduce((sum, item) => sum + item.count, 0);
                        const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
