<?php
session_start();
include '../connect.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['contact_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$contact_id = $_SESSION['contact_id'];

try {
    // Get caregiver information
    $caregiver_stmt = $conn->prepare("SELECT * FROM contact WHERE contact_id = ?");
    $caregiver_stmt->bind_param("i", $contact_id);
    $caregiver_stmt->execute();
    $caregiver_result = $caregiver_stmt->get_result();
    $caregiver = $caregiver_result->fetch_assoc();

    // Get the user this caregiver is monitoring
    $user_stmt = $conn->prepare("
        SELECT u.*, uc.relationship, d.serial_number, d.is_active as device_active, d.device_id
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

    // Get latest location data from gps_tracking table
    $location_data = null;
    if ($user && $user['device_id']) {
        $location_stmt = $conn->prepare("
            SELECT * FROM gps_tracking 
            WHERE device_id = ? 
            ORDER BY timestamp DESC 
            LIMIT 1
        ");
        $location_stmt->bind_param("i", $user['device_id']);
        $location_stmt->execute();
        $location_result = $location_stmt->get_result();
        $location_data = $location_result->fetch_assoc();
    }

    // Get latest sensor data from sensor_log table
    $sensor_data = [];
    if ($user && $user['device_id']) {
        $sensor_stmt = $conn->prepare("
            SELECT sensor_type, sensor_value, timestamp 
            FROM sensor_log 
            WHERE device_id = ? 
            ORDER BY timestamp DESC 
            LIMIT 10
        ");
        $sensor_stmt->bind_param("i", $user['device_id']);
        $sensor_stmt->execute();
        $sensor_result = $sensor_stmt->get_result();
        while ($row = $sensor_result->fetch_assoc()) {
            $sensor_data[] = $row;
        }
    }

    // Get recent alerts from alert table
    $alerts_data = [];
    if ($user) {
        $alerts_stmt = $conn->prepare("
            SELECT alert_id, device_id, user_id, alert_type, alert_description, timestamp, is_resolved, resolved_at
            FROM alert 
            WHERE user_id = ? 
            ORDER BY timestamp DESC 
            LIMIT 5
        ");
        $alerts_stmt->bind_param("i", $user['user_id']);
        $alerts_stmt->execute();
        $alerts_result = $alerts_stmt->get_result();
        while ($row = $alerts_result->fetch_assoc()) {
            $alerts_data[] = $row;
        }
    }

    // Create activity log from recent data
    $activities = [];
    
    // Add location activities
    if ($location_data) {
        $activities[] = [
            'type' => 'location',
            'title' => 'GPS Location Updated',
            'details' => 'Coordinates: ' . number_format($location_data['latitude'], 4) . ', ' . number_format($location_data['longitude'], 4),
            'timestamp' => $location_data['timestamp']
        ];
    }

    // Add sensor activities
    foreach ($sensor_data as $sensor) {
        $title = '';
        $details = '';
        
        switch($sensor['sensor_type']) {
            case 'battery':
                $title = 'Battery Level Check';
                $details = 'Current level: ' . $sensor['sensor_value'] . '%';
                break;
            case 'steps':
                $title = 'Step Counter Update';
                $details = 'Total steps: ' . number_format($sensor['sensor_value']);
                break;
            case 'temperature':
                $title = 'Temperature Reading';
                $details = 'Current temperature: ' . $sensor['sensor_value'] . '°C';
                break;
            default:
                $title = ucfirst($sensor['sensor_type']) . ' Reading';
                $details = 'Value: ' . $sensor['sensor_value'];
        }
        
        $activities[] = [
            'type' => 'sensor',
            'title' => $title,
            'details' => $details,
            'timestamp' => $sensor['timestamp']
        ];
    }

    // Add alert activities
    foreach ($alerts_data as $alert) {
        $activities[] = [
            'type' => 'alert',
            'title' => $alert['alert_type'],
            'details' => $alert['alert_description'],
            'timestamp' => $alert['timestamp']
        ];
    }

    // Sort activities by timestamp (newest first)
    usort($activities, function($a, $b) {
        return strtotime($b['timestamp']) - strtotime($a['timestamp']);
    });

    // Prepare response data
    $response_data = [
        'caregiver' => $caregiver,
        'patient' => $user,
        'device' => [
            'serial_number' => $user['serial_number'] ?? 'N/A',
            'is_active' => $user['device_active'] ?? 0
        ],
        'location' => $location_data,
        'sensors' => $sensor_data,
        'alerts' => $alerts_data,
        'activities' => array_slice($activities, 0, 10) // Limit to 10 activities
    ];

    echo json_encode(['success' => true, 'data' => $response_data]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>