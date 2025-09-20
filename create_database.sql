-- GabayLakad Database Schema
-- Create database tables based on MariaDB schema

-- Contact table (caregivers)
CREATE TABLE contact (
    contact_id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_active INTEGER NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Device table
CREATE TABLE device (
    device_id INTEGER PRIMARY KEY AUTOINCREMENT,
    serial_number VARCHAR(50) NOT NULL UNIQUE,
    is_active INTEGER NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- User table (people being monitored)
CREATE TABLE user (
    user_id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20),
    impairment_level VARCHAR(50) NOT NULL,
    age INTEGER,
    device_id INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (device_id) REFERENCES device(device_id)
);

-- User-Contact relationship table
CREATE TABLE user_contact (
    user_contact_id INTEGER PRIMARY KEY AUTOINCREMENT,
    contact_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    relationship VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contact_id) REFERENCES contact(contact_id),
    FOREIGN KEY (user_id) REFERENCES user(user_id)
);

-- GPS tracking table (current location)
CREATE TABLE gps_tracking (
    gps_track_id INTEGER PRIMARY KEY AUTOINCREMENT,
    device_id INTEGER NOT NULL,
    latitude REAL NOT NULL,
    longitude REAL NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (device_id) REFERENCES device(device_id)
);

-- Location log table (historical locations)
CREATE TABLE location_log (
    log_id INTEGER PRIMARY KEY AUTOINCREMENT,
    device_id INTEGER NOT NULL,
    latitude REAL NOT NULL,
    longitude REAL NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (device_id) REFERENCES device(device_id)
);

-- Sensor log table
CREATE TABLE sensor_log (
    sens_log_id INTEGER PRIMARY KEY AUTOINCREMENT,
    device_id INTEGER NOT NULL,
    sensor_type VARCHAR(50) NOT NULL,
    sensor_value DECIMAL(10,2),
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (device_id) REFERENCES device(device_id)
);

-- Alert table
CREATE TABLE alert (
    alert_id INTEGER PRIMARY KEY AUTOINCREMENT,
    device_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    alert_type VARCHAR(100) NOT NULL,
    alert_description TEXT NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_resolved INTEGER DEFAULT 0,
    resolved_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (device_id) REFERENCES device(device_id),
    FOREIGN KEY (user_id) REFERENCES user(user_id)
);

-- Insert sample device data
INSERT INTO device (serial_number, is_active) VALUES 
('GL001', 1),
('GL002', 1),
('GL003', 1);

-- Insert sample caregiver data (password: 'password123')
INSERT INTO contact (name, phone_number, password, is_active) VALUES 
('John Caregiver', '+639123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Insert sample user data
INSERT INTO user (name, phone_number, impairment_level, age, device_id) VALUES 
('Maria Santos', '+639987654321', 'Totally Blind', 65, 1);

-- Link caregiver to user
INSERT INTO user_contact (contact_id, user_id, relationship) VALUES 
(1, 1, 'Child');

-- Insert sample GPS tracking data
INSERT INTO gps_tracking (device_id, latitude, longitude) VALUES 
(1, 14.5995, 120.9842);

-- Insert sample location log
INSERT INTO location_log (device_id, latitude, longitude) VALUES 
(1, 14.5995, 120.9842);

-- Insert sample sensor data
INSERT INTO sensor_log (device_id, sensor_type, sensor_value) VALUES 
(1, 'battery', 85.5),
(1, 'steps', 1250),
(1, 'temperature', 28.5);