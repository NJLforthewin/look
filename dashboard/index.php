<?php
session_start();
include '../connect.php';

// Check if user is logged in
if (!isset($_SESSION['contact_id'])) {
    header("Location: ../login/index.php");
    exit();
}

$contact_id = $_SESSION['contact_id'];

// Get caregiver information
$caregiver_stmt = $conn->prepare("SELECT * FROM contact WHERE contact_id = ?");
$caregiver_stmt->bind_param("i", $contact_id);
$caregiver_stmt->execute();
$caregiver_result = $caregiver_stmt->get_result();
$caregiver = $caregiver_result->fetch_assoc();

// Get the user(s) this caregiver is monitoring
$user_stmt = $conn->prepare("
    SELECT u.*, uc.relationship, d.serial_number, d.is_active as device_active
    FROM user u 
    JOIN user_contact uc ON u.user_id = uc.user_id 
    LEFT JOIN device d ON u.device_id = d.device_id
    WHERE uc.contact_id = ?
    ORDER BY u.created_at DESC
    LIMIT 1
");
$user_stmt->bind_param("i", $contact_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

if (!$user) {
    $error_message = "No user assigned to your account. Please contact administrator.";
}

// Helper function to get caregiver initials for avatar
function getInitials($name) {
    $words = explode(' ', $name);
    $initials = '';
    foreach ($words as $word) {
        if (!empty($word)) {
            $initials .= strtoupper($word[0]);
        }
    }
    return substr($initials, 0, 2);
}

// Helper function to get patient initials for avatar
function getPatientInitials($name) {
    $words = explode(' ', $name);
    $initials = '';
    foreach ($words as $word) {
        if (!empty($word)) {
            $initials .= strtoupper($word[0]);
        }
    }
    return substr($initials, 0, 2);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GabayLakad Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="overlay" id="overlay"></div>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-walking"></i>
                </div>
                <h2>GabayLakad</h2>
                <button class="menu-toggle" id="menu-toggle" aria-label="Toggle menu">
                    <i class="fas fa-chevron-left"></i>
                </button>
            </div>
            <div class="sidebar-menu">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="#" class="nav-link active" data-tab="dashboard">
                            <i class="fas fa-tachometer-alt"></i> 
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-tab="profile">
                            <i class="fas fa-user"></i> 
                            <span>My Profile</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-tab="history">
                            <i class="fas fa-history"></i> 
                            <span>History</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-tab="location">
                            <i class="fas fa-map-marker-alt"></i> 
                            <span>Location Tracking</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-tab="sensor">
                            <i class="fas fa-microchip"></i> 
                            <span>Sensor Data</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-tab="alerts">
                            <i class="fas fa-bell"></i> 
                            <span>Alerts & Safety</span>
                        </a>
                    </li>
                    <button class="logout-btn" id="logoutBtn" aria-label="Logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <main class="main-content" id="main-content">
            <!-- Header -->
            <div class="header">
                <h1 class="header-title">Monitoring Dashboard</h1>
                <div class="header-actions">
                    <div class="user-info">
                        <div class="user-avatar"><?php echo getInitials($caregiver['name']); ?></div>
                        <div class="user-details">
                            <h4><?php echo htmlspecialchars($caregiver['name']); ?></h4>
                            <p>Caregiver</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="tab-content active" id="dashboard">
                <?php if(isset($error_message)): ?>
                    <div class="error-notification">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo htmlspecialchars($error_message); ?></span>
                    </div>
                <?php else: ?>
                
                <!-- Patient Information Banner -->
                <div class="patient-info">
                    <div class="patient-avatar"><?php echo getPatientInitials($user['name']); ?></div>
                    <div class="patient-details">
                        <h3 id="patientName"><?php echo htmlspecialchars($user['name']); ?></h3>
                        <p>Age: <span id="patientAge"><?php echo htmlspecialchars($user['age'] ?? 'N/A'); ?></span> • Condition: <span id="patientCondition"><?php echo htmlspecialchars($user['impairment_level']); ?></span></p>
                        <p>Emergency Contact: <span id="emergencyContact"><?php echo htmlspecialchars($caregiver['name'] . ' (Caregiver) - ' . $caregiver['phone_number']); ?></span></p>
                        <div class="device-badge">
                            <i class="fas fa-walking"></i> Smart Stick #<span id="deviceId"><?php echo htmlspecialchars($user['serial_number'] ?? 'N/A'); ?></span> - <span id="deviceStatus"><?php echo ($user['device_active'] == 1) ? 'Connected' : 'Disconnected'; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Status Cards -->
                <div class="dashboard-grid">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <div>
                                <div class="card-title">Device Status</div>
                                <div class="card-value">
                                    <span class="status-indicator <?php echo ($user['device_active'] == 1) ? 'online' : 'offline'; ?>" id="deviceStatusIndicator">
                                        <i class="fas fa-circle"></i> <span id="deviceStatusText"><?php echo ($user['device_active'] == 1) ? 'Online' : 'Offline'; ?></span>
                                    </span>
                                </div>
                                <div class="card-trend">
                                    <i class="fas fa-wifi"></i> Last sync: <span id="lastSync">Loading...</span>
                                </div>
                            </div>
                            <div class="card-icon device">
                                <i class="fas fa-walking"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="dashboard-card">
                        <div class="card-header">
                            <div>
                                <div class="card-title">Current Location</div>
                                <div class="card-value" style="font-size: 1.4rem;" id="currentLocation">Loading...</div>
                                <div class="card-trend">
                                    <i class="fas fa-clock"></i> Updated <span id="locationUpdate">Loading...</span>
                                </div>
                            </div>
                            <div class="card-icon location">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <div>
                                <div class="card-title">Battery Level</div>
                                <div class="card-value" id="batteryLevel">Loading...</div>
                                <div class="card-trend">
                                    <i class="fas fa-battery-three-quarters" id="batteryIcon"></i> <span id="batteryTime">Loading...</span>
                                </div>
                            </div>
                            <div class="card-icon battery">
                                <i class="fas fa-battery-three-quarters"></i>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <div>
                                <div class="card-title">Activity Status</div>
                                <div class="card-value">
                                    <span class="status-indicator walking" id="activityStatus">
                                        <i class="fas fa-walking"></i> <span id="activityText">Loading...</span>
                                    </span>
                                </div>
                                <div class="card-trend">
                                    <i class="fas fa-shoe-prints"></i> <span id="stepCount">Loading...</span> steps today
                                </div>
                            </div>
                            <div class="card-icon activity">
                                <i class="fas fa-shoe-prints"></i>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <div>
                                <div class="card-title">EMERGENCY SYSTEM</div>
                                <div class="card-value">
                                    <span class="status-indicator online" id="emergencySystemStatus">
                                        <i class="fas fa-check"></i> Ready
                                    </span>
                                </div>
                                <div class="card-trend">
                                    <i class="fas fa-mobile-alt"></i> SMS alerts via GSM network
                                </div>
                            </div>
                            <div class="card-icon emergency">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <div>
                                <div class="card-title">NIGHT REFLECTOR</div>
                                <div class="card-value">
                                    <span class="status-indicator online" id="nightReflectorStatus">
                                        <i class="fas fa-sun"></i> Active
                                    </span>
                                </div>
                                <div class="card-trend">
                                    <i class="fas fa-lightbulb"></i> Auto-activates in low light
                                </div>
                            </div>
                            <div class="card-icon night">
                                <i class="fas fa-sun"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Feed -->
                <div class="activity-section">
                    <h2 class="section-title">
                        <i class="fas fa-history"></i> Real-time Activity Log
                    </h2>
                    <ul class="activity-list" id="activityList">
                        <li class="activity-item">
                            <div class="activity-icon" style="background: #3498db">
                                <i class="fas fa-sync"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Loading Activity Data...</div>
                                <div class="activity-details">Please wait while we fetch the latest activity information</div>
                                <div class="activity-time">Now</div>
                            </div>
                        </li>
                    </ul>
                </div>
                
                <?php endif; ?>
            </div>

            <!-- Other tabs content placeholders -->
            <div class="tab-content" id="profile">
                <div class="activity-section">
                    <h2 class="section-title">My Profile</h2>
                    <p>Profile management functionality will be implemented here.</p>
                </div>
            </div>

            <div class="tab-content" id="history">
                <div class="activity-section">
                    <h2 class="section-title">History</h2>
                    <p>Historical data and reports will be displayed here.</p>
                </div>
            </div>

            <div class="tab-content" id="location">
                <div class="activity-section">
                    <h2 class="section-title">Location Tracking</h2>
                    <p>Real-time location tracking and maps will be implemented here.</p>
                </div>
            </div>

            <div class="tab-content" id="sensor">
                <div class="activity-section">
                    <h2 class="section-title">Sensor Data</h2>
                    <p>Detailed sensor readings and analytics will be displayed here.</p>
                </div>
            </div>

            <div class="tab-content" id="alerts">
                <div class="activity-section">
                    <h2 class="section-title">Safety Alerts</h2>
                    <p>Alert management and notification settings will be available here.</p>
                </div>
            </div>
        </main>
    </div>

    <script src="js/index.js"></script>
    <script src="js/dashboard.js"></script>
</body>
</html>