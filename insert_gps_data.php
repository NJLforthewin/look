<?php
include '../connect.php';

function insertGPSData($device_id, $latitude, $longitude) {
    global $conn;
    
    try {
        $conn->begin_transaction();
        
        // Insert into history (location_log)
        $history_stmt = $conn->prepare("INSERT INTO location_log (device_id, latitude, longitude, timestamp) VALUES (?, ?, ?, NOW())");
        $history_stmt->bind_param("idd", $device_id, $latitude, $longitude);
        $history_stmt->execute();
        
        // Update current location (gps_tracking) - REPLACE ensures only 1 record per device
        $current_stmt = $conn->prepare("REPLACE INTO gps_tracking (device_id, latitude, longitude, timestamp) VALUES (?, ?, ?, NOW())");
        $current_stmt->bind_param("idd", $device_id, $latitude, $longitude);
        $current_stmt->execute();
        
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
        $conn->rollback();
        error_log("GPS Insert Error: " . $e->getMessage());
        return false;
    }
}

// Test the function (optional - remove after testing)
if ($_GET['test'] == '1') {
    $result = insertGPSData(1, 14.5995, 120.9842);
    echo $result ? "GPS data inserted successfully!" : "Error inserting GPS data";
}
?>