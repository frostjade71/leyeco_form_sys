<?php
/**
 * LEYECO III Complaints System
 * Configuration File
 */

// Include main configuration first for BASE_URL
require_once __DIR__ . '/../../../config/config.php';

// Include main database configuration
require_once __DIR__ . '/../../../config/database.php';

// Application Settings
define('APP_NAME', 'LEYECO III Complaints System');
define('APP_VERSION', '1.0.0');

// Database Configuration - Use main config values
// Only define if not already defined by main config
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'leyeco_forms_db');
}
if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}
if (!defined('DB_CHARSET')) {
    define('DB_CHARSET', 'utf8mb4');
}

// Security
if (!defined('CSRF_TOKEN_NAME')) {
    define('CSRF_TOKEN_NAME', 'csrf_token_complaints');
}
if (!defined('SESSION_NAME')) {
    define('SESSION_NAME', 'leyeco_complaints_session');
}

// Upload Settings
define('UPLOAD_DIR', __DIR__ . '/../public/assets/uploads/');
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_ALLOWED_TYPES', ['image/jpeg', 'image/jpg', 'image/png']);
define('UPLOAD_ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png']);

// Pagination
define('COMPLAINTS_PER_PAGE', 20);

// Complaint Types
define('COMPLAINT_TYPES', [
    'NO_POWER' => 'No Power',
    'NO_POWER_BILL' => 'No Power Bill',
    'SPARKING_SDW' => 'Sparking SDW',
    'BILL_CLARIFICATION' => 'Bill Clarification',
    'POWER_FLUCTUATION' => 'Power Fluctuation',
    'BILL_RECOMPUTATION' => 'Bill Recomputation',
    'OTHERS' => 'Others (Please Specify)'
]);

// Complaint Statuses
define('COMPLAINT_STATUSES', [
    'NEW' => 'New',
    'INVESTIGATING' => 'Under Investigation',
    'IN_PROGRESS' => 'In Progress',
    'RESOLVED' => 'Resolved',
    'CLOSED' => 'Closed'
]);

// Municipalities (Leyte III Coverage Area - 9 Municipalities)
define('MUNICIPALITIES', [
    'Alang-alang',
    'Barugo',
    'Capoocan',
    'Carigara',
    'Jaro',
    'Pastrana',
    'San Miguel',
    'Santa Fe',
    'Tunga'
]);

// Barangays per Municipality (Total: 285 Barangays)
define('BARANGAYS', [
    'Alang-alang' => [
        'Aslum', 'Astorga (Burabod)', 'Bato', 'Binongto-an', 'Binotong', 'Blumentritt (Poblacion)',
        'Bobonon', 'Borseth', 'Buenavista', 'Bugho', 'Buri', 'Cabadsan', 'Calaasan', 'Cambahanon',
        'Cambolao', 'Canvertudes', 'Capiz', 'Cavite', 'Cogon', 'Dapdap', 'Divisoria', 'Ekiran',
        'Hinapolan', 'Holy Child I (Poblacion)', 'Holy Child II (Poblacion)', 'Hubang', 'Hupit',
        'Langit', 'Lingayon', 'Lourdes', 'Lukay', 'Magsaysay', 'Milagrosa (Poblacion)', 'Mudboron',
        'P. Barrantes', 'Peñalosa', 'Pepita', 'Salvacion (Poblacion)', 'San Antonio', 'San Antonio Pob.',
        'San Diego', 'San Francisco East (Francia)', 'San Francisco West', 'San Isidro', 'San Pedro',
        'San Roque (Poblacion)', 'San Vicente', 'Santiago', 'Santo Niño (Poblacion)', 'Santol',
        'Tabangohay', 'Tombo', 'Veteranos'
    ],
    'Barugo' => [
        'Abango', 'Amahit', 'Balire', 'Balud', 'Bukid', 'Bulod', 'Busay', 'Cabarasan', 'Cabolo-an',
        'Calingcaguing', 'Can-isak', 'Canomantag', 'Cuta', 'Domogdog', 'Duka', 'Guindaohan', 'Hiagsam',
        'Hilaba', 'Hinugayan', 'Ibag', 'Minuhang', 'Minuswang', 'Pikas', 'Pitogo', 'Poblacion Dist. I',
        'Poblacion Dist. II', 'Poblacion Dist. III', 'Poblacion Dist. IV', 'Poblacion Dist. V',
        'Poblacion Dist. VI (New Road)', 'Pongso', 'Roosevelt', 'San Isidro', 'San Roque', 'Santa Rosa',
        'Santarin', 'Tutug-an'
    ],
    'Capoocan' => [
        'Balucanad', 'Balud', 'Balugo', 'Cabul-an', 'Culasian', 'Gayad', 'Guinadiongan', 'Lemon',
        'Libertad', 'Manloy', 'Nauguisan', 'Pinamopoan', 'Poblacion Zone I', 'Poblacion Zone II',
        'Potot', 'San Joaquin', 'Santo Niño', 'Talairan', 'Talisay', 'Tolibao', 'Visares'
    ],
    'Carigara' => [
        'Bagong Lipunan', 'Balilit', 'Barayong', 'Barugohay Central', 'Barugohay Norte', 'Barugohay Sur',
        'Baybay (Poblacion)', 'Binibihan', 'Bislig', 'Caghalo', 'Camansi', 'Canal', 'Candigahub',
        'Canfabi', 'Canlampay', 'Cogon', 'Cutay', 'East Visoria', 'Guindapunan East', 'Guindapunan West',
        'Hiluctogan', 'Jugaban (Poblacion)', 'Libo', 'Lower Hiraan', 'Lower Sogod', 'Macalpi', 'Manloy',
        'Nauguisan', 'Paglaum', 'Pangna', 'Parag-um', 'Parina', 'Piloro', 'Ponong (Poblacion)',
        'Rizal (Tagak East)', 'Sagkahan', 'San Isidro', 'San Juan', 'San Mateo (Poblacion)', 'Santa Fe',
        'Sawang (Poblacion)', 'Tagak', 'Tangnan', 'Tigbao', 'Tinaguban', 'Upper Hiraan', 'Upper Sogod',
        'Uyawan', 'West Visoria'
    ],
    'Jaro' => [
        'Alahag', 'Anibongon', 'Atipolo', 'Badiang', 'Batug', 'Bias-Zabala', 'Buenavista', 'Bukid',
        'Burabod', 'Buri', 'Caglawaan', 'Canapu-an', 'Canhandugan', 'Crossing Rubas', 'Daro',
        'District I (Poblacion)', 'District II (Poblacion)', 'District III (Poblacion)', 'District IV (Poblacion)',
        'Hiagsam', 'Hibucawan', 'Hibunawon', 'Kalinawan', 'La Paz', 'Licod', 'Macanip', 'Macopa',
        'Mag-aso', 'Malobago', 'Olotan', 'Palanog', 'Pange', 'Parasan', 'Pitogo', 'Sagkahan',
        'San Agustin', 'San Pedro', 'San Roque', 'Santa Cruz', 'Santo Niño', 'Sari-Sari', 'Tinambacan',
        'Tuba', 'Uguiao', 'Villa Conzoilo', 'Villa Paz'
    ],
    'Pastrana' => [
        'Arabunog', 'Aringit', 'Aures', 'Bahay', 'Cabaohan', 'Calsadahay', 'Cancaraja', 'Caninoan',
        'Capilla', 'Colawen', 'District 1 (Poblacion)', 'District 2 (Poblacion)', 'District 3 (Poblacion)',
        'District 4 (Poblacion)', 'Dumarag', 'Guindapunan', 'Halaba', 'Jones', 'Lanawan', 'Lima',
        'Lourdes', 'Macalpiay', 'Malitbogay', 'Manaybanay', 'Maricum', 'Patong', 'Sapsap', 'Socsocon',
        'Tingib', 'Yapad'
    ],
    'San Miguel' => [
        'Bagacay', 'Bahay', 'Bairan', 'Cabatianuhan', 'Canap', 'Capilihan', 'Caraycaray',
        'Cayare (West Poblacion)', 'Guinciaman', 'Impo', 'Kinalumsan', 'Libtong (East Poblacion)',
        'Lukay', 'Malaguinabot', 'Malpag', 'Mawodpawod', 'Patong', 'Pinarigusan', 'San Andres',
        'Santa Cruz', 'Santol'
    ],
    'Santa Fe' => [
        'Baculanad', 'Badiangay', 'Bulod', 'Catoogan', 'Cutay', 'Gapas', 'Katipunan', 'Milagrosa',
        'Pilit', 'Pitogo', 'San Isidro', 'San Juan', 'San Miguelay', 'San Roque', 'Tibak', 'Victoria',
        'Zone 1 (Poblacion)', 'Zone 2 (Poblacion)', 'Zone 3 (Poblacion)', 'Zone 4 Poblacion (Cabangcalan)'
    ],
    'Tunga' => [
        'Astorga (Barrio Upat)', 'Balire', 'Banawang', 'San Antonio (Poblacion)', 'San Pedro (Poblacion)',
        'San Roque (Poblacion)', 'San Vicente (Poblacion)', 'Santo Niño (Poblacion)'
    ]
]);

// Timezone
date_default_timezone_set('Asia/Manila');

// Error Reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
