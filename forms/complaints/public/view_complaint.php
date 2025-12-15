<?php
/**
 * LEYECO III Complaints System
 * View Complaint Page
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/functions.php';
require_once __DIR__ . '/../app/ComplaintController.php';

// Create a debug log function
function debugLog($message, $data = null) {
    $log = "[" . date('Y-m-d H:i:s') . "] " . $message . PHP_EOL;
    if ($data !== null) {
        $log .= "Data: " . print_r($data, true) . PHP_EOL;
    }
    file_put_contents(__DIR__ . '/debug.log', $log, FILE_APPEND);
}

$complaint = null;
$error = null;

debugLog("=== New Request ===");
debugLog("GET Parameters:", $_GET);

if (isset($_GET['ref'])) {
    try {
        $referenceCode = trim($_GET['ref']);
        debugLog("Reference code after trim: '$referenceCode'");
        debugLog("Reference code length: " . strlen($referenceCode));
        
        // Log the exact bytes of the reference code
        $bytes = [];
        for ($i = 0; $i < strlen($referenceCode); $i++) {
            $bytes[] = ord($referenceCode[$i]);
        }
        debugLog("Reference code bytes: " . implode(' ', $bytes));
        
        // Direct database query - simplified without user joins
        $db = getDB();
        $stmt = $db->prepare("
            SELECT * FROM complaints 
            WHERE reference_code = ?
        ");
        $stmt->execute([$referenceCode]);
        $complaint = $stmt->fetch();
        
        if ($complaint) {
            debugLog("Complaint found with ID: " . $complaint['id']);
            
            // Get comments - simplified without user join
            $stmt = $db->prepare("
                SELECT * FROM complaint_comments 
                WHERE complaint_id = ?
                ORDER BY created_at DESC
            ");
            $stmt->execute([$complaint['id']]);
            $complaint['comments'] = $stmt->fetchAll();
        } else {
            // For debugging: List all reference codes
            $allRefs = $db->query("SELECT id, reference_code FROM complaints")->fetchAll(PDO::FETCH_ASSOC);
            debugLog("No complaint found. Available reference codes:", $allRefs);
            
            $error = 'Complaint not found. Please check your reference code and try again.';
        }
    } catch (Exception $e) {
        $error = 'An error occurred while processing your request.';
        debugLog("Exception: " . $e->getMessage());
        debugLog("Stack trace: " . $e->getTraceAsString());
    }
} else {
    $error = 'No reference code provided.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Complaint - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="homepage.css">
    <?php if ($complaint && ($complaint['lat'] && $complaint['lon'])): ?>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
    <?php endif; ?>
    <style>
        .complaint-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border: 3px solid var(--accent-yellow);
        }
        .complaint-header {
            border-bottom: 3px solid var(--accent-yellow);
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .complaint-header h1 {
            color: var(--primary-red);
            font-weight: 800;
            font-size: 28px;
            margin-bottom: 10px;
        }
        .reference-code {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-dark);
            font-family: 'Courier New', monospace;
            background: var(--light-gray);
            padding: 10px 15px;
            border-radius: 8px;
            display: inline-block;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-left: 15px;
        }
        .status-NEW {
            background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
            color: #78350f;
            border: 2px solid var(--accent-yellow);
        }
        .status-INVESTIGATING {
            background: linear-gradient(135deg, #DBEAFE 0%, #BFDBFE 100%);
            color: #1E40AF;
            border: 2px solid var(--info-blue);
        }
        .status-IN_PROGRESS {
            background: linear-gradient(135deg, #FEE2E2 0%, #FCA5A5 100%);
            color: #991B1B;
            border: 2px solid var(--primary-red);
        }
        .status-RESOLVED {
            background: linear-gradient(135deg, #D1FAE5 0%, #A7F3D0 100%);
            color: #065F46;
            border: 2px solid var(--success-green);
        }
        .status-CLOSED {
            background: linear-gradient(135deg, #E5E7EB 0%, #D1D5DB 100%);
            color: #374151;
            border: 2px solid #9CA3AF;
        }
        .detail-section {
            margin-bottom: 30px;
        }
        .detail-section h3 {
            font-size: 20px;
            color: var(--text-dark);
            font-weight: 700;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--border-light);
        }
        .detail-row {
            display: grid;
            grid-template-columns: 180px 1fr;
            gap: 15px;
            margin-bottom: 15px;
            padding: 12px;
            background: var(--off-white);
            border-radius: 8px;
        }
        .detail-label {
            font-weight: 700;
            color: var(--text-gray);
            font-size: 14px;
        }
        .detail-value {
            color: var(--text-dark);
            font-size: 15px;
        }
        .complaint-photo {
            max-width: 100%;
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            margin-top: 10px;
        }
        .comment-list {
            list-style: none;
            padding: 0;
        }
        .comment-item {
            background: var(--off-white);
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 12px;
            border-left: 4px solid var(--accent-yellow);
        }
        .comment-meta {
            font-size: 13px;
            color: var(--text-gray);
            margin-bottom: 8px;
        }
        .comment-date {
            color: var(--primary-red);
            font-weight: 600;
        }
        .comment-user {
            font-weight: 700;
            color: var(--primary-red);
        }
        .comment-message {
            color: var(--text-dark);
            font-size: 15px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 25px;
            color: var(--primary-red);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 16px;
        }
        .back-link:hover {
            color: var(--primary-red-dark);
            transform: translateX(-5px);
        }
        .error-box {
            background: linear-gradient(135deg, #FEE2E2 0%, #FECACA 100%);
            border-left: 5px solid var(--primary-red);
            padding: 25px 30px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: var(--shadow);
            text-align: center;
        }
        .error-box h2 {
            color: var(--primary-red-dark);
            font-weight: 800;
            margin-bottom: 10px;
        }
        .error-box p {
            color: var(--primary-red-dark);
            font-weight: 600;
        }
        #map {
            height: 250px;
            width: 100%;
            border-radius: 10px;
            border: 2px solid var(--border-light);
            margin-top: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .hidden-comment {
            display: none;
        }
        .btn-secondary {
            background: linear-gradient(135deg, var(--accent-yellow) 0%, #F59E0B 100%);
            color: var(--text-dark);
            padding: 6px 16px;
            border: 2px solid var(--accent-yellow);
            border-radius: 8px;
            font-weight: 700;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .btn-secondary:hover {
            background: linear-gradient(135deg, #F59E0B 0%, var(--accent-yellow) 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }
        @media (max-width: 767px) {
            .complaint-container {
                padding: 25px 20px;
                margin: 20px 15px;
            }
            .detail-row {
                grid-template-columns: 1fr;
                gap: 5px;
            }
            .status-badge {
                margin-left: 0;
                margin-top: 10px;
                display: block;
                text-align: center;
            }
            #map {
                height: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="homepage.php" class="back-link">← Back to Home</a>

        <?php if ($error): ?>
            <div class="error-box">
                <h2>❌ Complaint Not Found</h2>
                <p><?php echo e($error); ?></p>
                <a href="homepage.php" class="btn btn-primary" style="margin-top: 20px;">Return to Homepage</a>
            </div>
        <?php elseif ($complaint): ?>
            <div class="complaint-container">
                <div class="complaint-header">
                    <h1>Complaint Details</h1>
                    <div>
                        <span class="reference-code"><?php echo e($complaint['reference_code']); ?></span>
                        <span class="status-badge status-<?php echo e($complaint['status']); ?>">
                            <?php echo e(COMPLAINT_STATUSES[$complaint['status']]); ?>
                        </span>
                    </div>
                </div>

                <div class="detail-section">
                    <h3>📋 Complaint Information</h3>
                    <div class="detail-row">
                        <div class="detail-label">Type:</div>
                        <div class="detail-value"><?php echo e(COMPLAINT_TYPES[$complaint['type']]); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Description:</div>
                        <div class="detail-value"><?php echo nl2br(e($complaint['description'])); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Submitted:</div>
                        <div class="detail-value"><?php echo formatDate($complaint['created_at']); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Last Updated:</div>
                        <div class="detail-value"><?php echo formatDate($complaint['updated_at']); ?></div>
                    </div>
                </div>

                <div class="detail-section">
                    <h3>📍 Location Information</h3>
                    <div class="detail-row" style="grid-template-columns: 180px 1fr 180px 1fr;">
                        <div class="detail-label">Municipality:</div>
                        <div class="detail-value"><?php echo e($complaint['municipality']); ?></div>
                        <div class="detail-label">Barangay:</div>
                        <div class="detail-value"><?php echo e($complaint['barangay']); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Address:</div>
                        <div class="detail-value"><?php echo e($complaint['address']); ?></div>
                    </div>
                    <?php if ($complaint['lat'] && $complaint['lon']): ?>
                        <div class="detail-row">
                            <div class="detail-label">Coordinates:</div>
                            <div class="detail-value">
                                Lat: <?php echo e($complaint['lat']); ?>, Lon: <?php echo e($complaint['lon']); ?>
                            </div>
                        </div>
                        <div id="map"></div>
                    <?php endif; ?>
                </div>

                <?php if ($complaint['reporter_name'] || $complaint['contact']): ?>
                <div class="detail-section">
                    <h3>👤 Reporter Information</h3>
                    <?php if ($complaint['reporter_name']): ?>
                    <div class="detail-row">
                        <div class="detail-label">Name:</div>
                        <div class="detail-value"><?php echo e($complaint['reporter_name']); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if ($complaint['contact']): ?>
                    <div class="detail-row">
                        <div class="detail-label">Contact:</div>
                        <div class="detail-value"><?php echo e($complaint['contact']); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if ($complaint['dispatch_to'] || $complaint['dispatch_mode'] || $complaint['dispatch_by'] || $complaint['dispatch_date'] || $complaint['action_taken'] || $complaint['acknowledged_by'] || $complaint['date_settled']): ?>
                <div class="detail-section">
                    <h3>🚀 Dispatcher Details</h3>
                    <?php if ($complaint['dispatch_to']): ?>
                    <div class="detail-row">
                        <div class="detail-label">Dispatched To:</div>
                        <div class="detail-value"><?php echo e($complaint['dispatch_to']); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if ($complaint['dispatch_mode']): ?>
                    <div class="detail-row">
                        <div class="detail-label">Dispatch Mode:</div>
                        <div class="detail-value"><?php echo e($complaint['dispatch_mode']); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if ($complaint['dispatch_by']): ?>
                    <div class="detail-row">
                        <div class="detail-label">Dispatched By (Staff ID):</div>
                        <div class="detail-value"><?php echo e($complaint['dispatch_by']); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if ($complaint['dispatch_date']): ?>
                    <div class="detail-row">
                        <div class="detail-label">Dispatch Date:</div>
                        <div class="detail-value"><?php echo formatDate($complaint['dispatch_date']); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if ($complaint['action_taken']): ?>
                    <div class="detail-row">
                        <div class="detail-label">Action Taken:</div>
                        <div class="detail-value"><?php echo nl2br(e($complaint['action_taken'])); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if ($complaint['acknowledged_by']): ?>
                    <div class="detail-row">
                        <div class="detail-label">Acknowledged By:</div>
                        <div class="detail-value"><?php echo e($complaint['acknowledged_by']); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if ($complaint['date_settled']): ?>
                    <div class="detail-row">
                        <div class="detail-label">Date Settled:</div>
                        <div class="detail-value"><?php echo formatDate($complaint['date_settled']); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if ($complaint['photo_path']): ?>
                <div class="detail-section">
                    <h3>📷 Attached Photo</h3>
                    <img src="<?php echo e($complaint['photo_path']); ?>" alt="Complaint Photo" class="complaint-photo">
                </div>
                <?php endif; ?>

                <?php if (!empty($complaint['comments'])): ?>
                <div class="detail-section">
                    <h3>💬 Activity Timeline</h3>
                    <ul class="comment-list">
                        <?php foreach ($complaint['comments'] as $index => $comment): ?>
                            <li class="comment-item <?php echo $index >= 2 ? 'hidden-comment' : ''; ?>">
                                <div class="comment-meta">
                                    <?php if (!empty($comment['user_name'])): ?>
                                    <span class="comment-user">
                                        <?php echo e($comment['user_name']); ?>
                                    </span>
                                    • 
                                    <?php endif; ?>
                                    <span class="comment-date"><?php echo formatDate($comment['created_at']); ?></span>
                                </div>
                                <div class="comment-message"><?php echo e($comment['message']); ?></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if (count($complaint['comments']) > 2): ?>
                    <div style="text-align: center; margin-top: 15px;">
                        <button id="toggleTimelineBtn" class="btn btn-secondary" onclick="toggleTimeline()">
                            Read more
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div style="text-align: center; margin-top: 30px;">
                    <a href="homepage.php" class="btn btn-primary">Back to Homepage</a>
                </div>
            </div>
        <?php else: ?>
            <div class="error-box">
                <h2>🔍 Enter Reference Code</h2>
                <p>Please enter a reference code to view complaint details.</p>
                <a href="homepage.php" class="btn btn-primary" style="margin-top: 20px;">Return to Homepage</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Toggle timeline visibility
        function toggleTimeline() {
            const hiddenComments = document.querySelectorAll('.hidden-comment');
            const toggleBtn = document.getElementById('toggleTimelineBtn');
            
            hiddenComments.forEach(comment => {
                if (comment.style.display === 'none' || comment.style.display === '') {
                    comment.style.display = 'block';
                    toggleBtn.textContent = 'Show less';
                } else {
                    comment.style.display = 'none';
                    toggleBtn.textContent = 'Read more';
                }
            });
        }
    </script>

    <?php if ($complaint && ($complaint['lat'] && $complaint['lon'])): ?>
    <script>
        // Initialize map centered on complaint location
        var map = L.map('map').setView([<?php echo $complaint['lat']; ?>, <?php echo $complaint['lon']; ?>], 15);
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19,
        }).addTo(map);
        
        // Add marker at complaint location
        var marker = L.marker([<?php echo $complaint['lat']; ?>, <?php echo $complaint['lon']; ?>]).addTo(map);
        marker.bindPopup("<b>Complaint Location</b><br><?php echo e($complaint['address']); ?>").openPopup();
    </script>
    <?php endif; ?>
</body>
</html>
