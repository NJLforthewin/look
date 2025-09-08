<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GabayLakad Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            color: #333;
            overflow-x: hidden;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background: #2c3e50;
            color: white;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            transform: translateX(0);
            transition: transform 0.3s ease;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar.collapsed {
            transform: translateX(-280px);
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid #34495e;
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
        }

        .logo {
            width: 40px;
            height: 40px;
            background: #3498db;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .sidebar-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #ecf0f1;
        }

        .menu-toggle {
            position: absolute;
            right: 1rem;
            background: none;
            border: none;
            color: #bdc3c7;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .menu-toggle:hover {
            background: #34495e;
            color: white;
        }

        .sidebar-menu {
            padding: 1rem 0;
        }

        .nav-list {
            list-style: none;
        }

        .nav-item {
            margin: 0.25rem 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.875rem 1.5rem;
            color: #bdc3c7;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .nav-link:hover {
            background: #34495e;
            color: #ecf0f1;
            border-left-color: #3498db;
        }

        .nav-link.active {
            background: #34495e;
            color: #3498db;
            border-left-color: #3498db;
        }

        .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }

        .nav-link span {
            font-weight: 500;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
            transition: margin-left 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        .header {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: #3498db;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .user-details h4 {
            color: #2c3e50;
            font-weight: 600;
        }

        .user-details p {
            color: #7f8c8d;
            font-size: 0.875rem;
        }

        /* Logout Button */
        .logout-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            margin-left: 1rem;
        }

        .logout-btn:hover {
            background: #c0392b;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(231, 76, 60, 0.3);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* Patient Info Banner */
        .patient-info {
            background: linear-gradient(135deg, #3498db, #2980b9);
            padding: 1.5rem;
            border-radius: 12px;
            color: white;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .patient-avatar {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
        }

        .patient-details h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .patient-details p {
            opacity: 0.9;
            margin-bottom: 0.25rem;
        }

        .device-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            display: inline-block;
        }

        /* Dashboard Cards */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .dashboard-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .card-icon.device { background: #27ae60; }
        .card-icon.location { background: #e74c3c; }
        .card-icon.battery { background: #f39c12; }
        .card-icon.activity { background: #9b59b6; }
        .card-icon.emergency { background: #e74c3c; }
        .card-icon.night { background: #f1c40f; }

        .card-title {
            font-size: 0.875rem;
            color: #7f8c8d;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .card-value {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .card-trend {
            font-size: 0.875rem;
            color: #27ae60;
        }

        .card-trend.warning {
            color: #f39c12;
        }

        .card-trend.danger {
            color: #e74c3c;
        }

        /* Status Indicators */
        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-indicator.online {
            background: rgba(39, 174, 96, 0.1);
            color: #27ae60;
        }

        .status-indicator.offline {
            background: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
        }

        .status-indicator.walking {
            background: rgba(52, 152, 219, 0.1);
            color: #3498db;
        }

        .status-indicator.stationary {
            background: rgba(149, 165, 166, 0.1);
            color: #95a5a6;
        }

        /* Emergency Button Styles */
        .emergency-controls {
            margin-top: 1rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .emergency-btn {
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .emergency-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-emergency {
            background: #e74c3c;
            color: white;
        }

        .btn-emergency:hover {
            background: #c0392b;
        }

        .btn-contact {
            background: #3498db;
            color: white;
        }

        .btn-contact:hover {
            background: #2980b9;
        }

        /* Activity Feed */
        .activity-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .activity-list {
            list-style: none;
            max-height: 400px;
            overflow-y: auto;
        }

        .activity-item {
            padding: 1rem 0;
            border-bottom: 1px solid #ecf0f1;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            color: white;
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.25rem;
        }

        .activity-time {
            font-size: 0.875rem;
            color: #7f8c8d;
        }

        .activity-details {
            font-size: 0.8rem;
            color: #95a5a6;
            margin-bottom: 0.25rem;
        }

        /* Tab Content */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-280px);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .overlay.show {
                display: block;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .patient-info {
                flex-direction: column;
                text-align: center;
            }

            .header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .header-actions {
                justify-content: center;
            }

            .emergency-controls {
                justify-content: center;
            }
        }
    </style>
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
                        <div class="user-avatar">KS</div>
                        <div class="user-details">
                            <h4>Kevin Keith P. Selisana</h4>
                            <p>Caregiver</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="tab-content active" id="dashboard">
                <!-- Patient Information Banner -->
                <div class="patient-info">
                    <div class="patient-avatar">JB</div>
                    <div class="patient-details">
                        <h3 id="patientName">Joanna Marie Baguio</h3>
                        <p>Age: <span id="patientAge">22</span> • Condition: <span id="patientCondition">Totally Blind</span></p>
                        <p>Emergency Contact: <span id="emergencyContact">Loading...</span></p>
                        <div class="device-badge">
                            <i class="fas fa-walking"></i> Smart Stick #<span id="deviceId">GL001</span> - <span id="deviceStatus">Connected</span>
                        </div>
                    </div>
                    <div class="emergency-controls">
                        <button class="emergency-btn btn-emergency" id="emergencyBtn">
                            <i class="fas fa-exclamation-triangle"></i> EMERGENCY ALERT
                        </button>
                        <button class="emergency-btn btn-contact" id="contactBtn">
                            <i class="fas fa-phone"></i> CONTACT CAREGIVER
                        </button>
                    </div>
                </div>

                <!-- Status Cards -->
                <div class="dashboard-grid">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <div>
                                <div class="card-title">Device Status</div>
                                <div class="card-value">
                                    <span class="status-indicator" id="deviceStatusIndicator">
                                        <i class="fas fa-circle"></i> <span id="deviceStatusText">Online</span>
                                    </span>
                                </div>
                                <div class="card-trend">
                                    <i class="fas fa-wifi"></i> Last sync: <span id="lastSync">5 seconds ago</span>
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
                                <div class="card-value" style="font-size: 1.2rem;" id="currentLocation">Ayala Center</div>
                                <div class="card-trend">
                                    <i class="fas fa-clock"></i> Updated <span id="locationUpdate">10 seconds ago</span>
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
                                <div class="card-value" id="batteryLevel">78%</div>
                                <div class="card-trend">
                                    <i class="fas fa-battery-three-quarters" id="batteryIcon"></i> <span id="batteryTime">~6 hours remaining</span>
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
                                    <span class="status-indicator" id="activityStatus">
                                        <i class="fas fa-walking"></i> <span id="activityText">Walking</span>
                                    </span>
                                </div>
                                <div class="card-trend">
                                    <i class="fas fa-shoe-prints"></i> <span id="stepCount">1,247</span> steps today
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
                        <!-- Activities will be populated by JavaScript -->
                    </ul>
                </div>
            </div>

            <!-- Other tabs content placeholders -->
            <div class="tab-content" id="profile">
                <div class="activity-section">
                    <h2 class="section-title">My Profile</h2>
                    <p>Profile management interface will be implemented here...</p>
                </div>
            </div>

            <div class="tab-content" id="history">
                <div class="activity-section">
                    <h2 class="section-title">History</h2>
                    <p>Historical data and analytics will be displayed here...</p>
                </div>
            </div>

            <div class="tab-content" id="location">
                <div class="activity-section">
                    <h2 class="section-title">Location Tracking</h2>
                    <p>Detailed location tracking and route history will be displayed here...</p>
                </div>
            </div>

            <div class="tab-content" id="sensor">
                <div class="activity-section">
                    <h2 class="section-title">Sensor Data</h2>
                    <p>Real-time sensor readings and analytics will be displayed here...</p>
                </div>
            </div>

            <div class="tab-content" id="alerts">
                <div class="activity-section">
                    <h2 class="section-title">Safety Alerts</h2>
                    <p>Alert history and safety notifications will be displayed here...</p>
                </div>
            </div>
        </main>
    </div>

    <script>
        // GabayLakad Backend Simulation
        class GabayLakadBackend {
            constructor() {
                this.isConnected = false;
                this.websocket = null;
                this.data = {
                    patient: {
                        id: 'PAT001',
                        name: 'Joanna Marie Baguio',
                        age: 22,
                        condition: 'Totally Blind',
                        emergencyContact: {
                            name: 'Kevin Keith P. Selisana',
                            relationship: 'Caregiver', 
                            phone: '+63 918 214 8193'
                        }
                    },
                    device: {
                        id: 'GL001',
                        status: 'online',
                        battery: 78,
                        lastSync: new Date(),
                        location: {
                            name: 'Ayala Center Cebu',
                            coordinates: { lat: 10.3157, lng: 123.8854 },
                            lastUpdate: new Date()
                        }
                    },
                    activity: {
                        status: 'walking',
                        stepCount: 1247,
                        speed: 2.1
                    },
                    sensors: {
                        obstacleDistance: 0.85,
                        tiltAngle: 15,
                        pressure: 'normal'
                    },
                    activities: []
                };
                
                this.init();
            }

            init() {
                this.simulateConnection();
                this.startDataUpdate();
                this.populateInitialActivities();
            }

            simulateConnection() {
                // Simulate backend connection
                setTimeout(() => {
                    this.isConnected = true;
                    this.updateConnectionStatus();
                    console.log('Backend connected successfully');
                    // Initial UI update
                    this.updateUI();
                }, 2000);
            }

            updateConnectionStatus() {
                const statusElement = document.getElementById('backendStatus');
                const statusText = document.getElementById('statusText');
                
                if (this.isConnected) {
                    statusElement.className = 'backend-status connected';
                    statusText.textContent = 'Backend Connected';
                } else {
                    statusElement.className = 'backend-status disconnected';
                    statusText.textContent = 'Backend Disconnected';
                }
            }

            startDataUpdate() {
                // Update data every 5 seconds
                setInterval(() => {
                    if (this.isConnected) {
                        this.updateSensorData();
                        this.updateUI();
                    }
                }, 5000);

                // Update activity log every 30 seconds
                setInterval(() => {
                    if (this.isConnected) {
                        this.addRandomActivity();
                    }
                }, 30000);
            }

            updateSensorData() {
                // Simulate sensor data changes
                this.data.device.battery = Math.max(10, this.data.device.battery - 0.1);
                this.data.device.lastSync = new Date();
                this.data.activity.stepCount += Math.floor(Math.random() * 10);
                this.data.sensors.obstacleDistance = (Math.random() * 2 + 0.3).toFixed(2);
                this.data.sensors.tiltAngle = Math.floor(Math.random() * 30);
                
                // Occasionally change location
                if (Math.random() > 0.8) {
                    const locations = [
                        'Ayala Center Cebu',
                        'SM City Cebu',
                        'Colon Street',
                        'University of Cebu',
                        'Carbon Market'
                    ];
                    this.data.device.location.name = locations[Math.floor(Math.random() * locations.length)];
                    this.data.device.location.lastUpdate = new Date();
                }
            }

            updateUI() {
                // Update all UI elements with current data
                document.getElementById('patientName').textContent = this.data.patient.name;
                document.getElementById('patientAge').textContent = this.data.patient.age;
                document.getElementById('deviceId').textContent = this.data.device.id;
                document.getElementById('currentLocation').textContent = this.data.device.location.name;
                document.getElementById('batteryLevel').textContent = `${Math.floor(this.data.device.battery)}%`;
                document.getElementById('stepCount').textContent = this.data.activity.stepCount.toLocaleString();
                
                // Update timestamps
                const syncTime = this.formatTimeAgo(this.data.device.lastSync);
                const locationTime = this.formatTimeAgo(this.data.device.location.lastUpdate);
                document.getElementById('lastSync').textContent = syncTime;
                document.getElementById('locationUpdate').textContent = locationTime;

                // Update emergency contact display
                this.updateEmergencyContactDisplay();
                
                // Update device status
                const deviceStatusElement = document.getElementById('deviceStatus');
                const deviceStatusTextElement = document.getElementById('deviceStatusText');
                const deviceStatusIndicator = document.getElementById('deviceStatusIndicator');
                
                if (this.data.device.status === 'online') {
                    deviceStatusElement.textContent = 'Connected';
                    deviceStatusTextElement.textContent = 'Online';
                    deviceStatusIndicator.className = 'status-indicator online';
                } else {
                    deviceStatusElement.textContent = 'Disconnected';
                    deviceStatusTextElement.textContent = 'Offline';
                    deviceStatusIndicator.className = 'status-indicator offline';
                }
                
                // Update activity status
                const activityTextElement = document.getElementById('activityText');
                const activityStatusElement = document.getElementById('activityStatus');
                
                if (this.data.activity.status === 'walking') {
                    activityTextElement.textContent = 'Walking';
                    activityStatusElement.className = 'status-indicator walking';
                } else {
                    activityTextElement.textContent = 'Stationary';
                    activityStatusElement.className = 'status-indicator stationary';
                }
                
                // Update battery icon
                const batteryIconElement = document.getElementById('batteryIcon');
                const batteryLevel = this.data.device.battery;
                if (batteryLevel > 75) {
                    batteryIconElement.className = 'fas fa-battery-full';
                } else if (batteryLevel > 50) {
                    batteryIconElement.className = 'fas fa-battery-three-quarters';
                } else if (batteryLevel > 25) {
                    batteryIconElement.className = 'fas fa-battery-half';
                } else {
                    batteryIconElement.className = 'fas fa-battery-quarter';
                }
            }

            updateEmergencyContactDisplay() {
                const contact = this.data.patient.emergencyContact;
                const contactElement = document.getElementById('emergencyContact');
                contactElement.textContent = `${contact.name} (${contact.relationship}) - ${contact.phone}`;
            }

            formatTimeAgo(date) {
                const now = new Date();
                const seconds = Math.floor((now - date) / 1000);
                
                if (seconds < 60) return `${seconds} seconds ago`;
                const minutes = Math.floor(seconds / 60);
                if (minutes < 60) return `${minutes} minutes ago`;
                const hours = Math.floor(minutes / 60);
                if (hours < 24) return `${hours} hours ago`;
                const days = Math.floor(hours / 24);
                return `${days} days ago`;
            }

            populateInitialActivities() {
                const sampleActivities = [
                    { type: 'location', title: 'Entered Ayala Center', time: new Date(Date.now() - 300000), details: 'Automatic check-in via geofence' },
                    { type: 'obstacle', title: 'Obstacle Detected', time: new Date(Date.now() - 600000), details: '0.8m ahead, slight right turn suggested' },
                    { type: 'battery', title: 'Battery Level Normal', time: new Date(Date.now() - 900000), details: 'Battery at 82%' },
                    { type: 'movement', title: 'Walking Detected', time: new Date(Date.now() - 1200000), details: 'Speed: 2.1 km/h' }
                ];
                
                this.data.activities = sampleActivities;
                this.renderActivityList();
            }

            addRandomActivity() {
                const activityTypes = [
                    { type: 'location', title: 'Location Updated', details: 'New position recorded' },
                    { type: 'obstacle', title: 'Obstacle Detected', details: 'Caution: object detected ahead' },
                    { type: 'battery', title: 'Battery Level Checked', details: 'System running normally' },
                    { type: 'movement', title: 'Movement Detected', details: 'User is walking' },
                    { type: 'alert', title: 'Near Miss Avoided', details: 'Automatic vibration alert triggered' }
                ];
                
                const randomActivity = activityTypes[Math.floor(Math.random() * activityTypes.length)];
                const newActivity = {
                    ...randomActivity,
                    time: new Date()
                };
                
                this.data.activities.unshift(newActivity);
                if (this.data.activities.length > 20) {
                    this.data.activities.pop();
                }
                
                this.renderActivityList();
            }

            renderActivityList() {
                const activityList = document.getElementById('activityList');
                activityList.innerHTML = '';
                
                this.data.activities.forEach(activity => {
                    const li = document.createElement('li');
                    li.className = 'activity-item';
                    
                    let iconColor = '#3498db';
                    switch(activity.type) {
                        case 'location': iconColor = '#e74c3c'; break;
                        case 'obstacle': iconColor = '#f39c12'; break;
                        case 'battery': iconColor = '#27ae60'; break;
                        case 'alert': iconColor = '#e74c3c'; break;
                    }
                    
                    li.innerHTML = `
                        <div class="activity-icon" style="background: ${iconColor}">
                            <i class="fas ${this.getActivityIcon(activity.type)}"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">${activity.title}</div>
                            <div class="activity-details">${activity.details}</div>
                            <div class="activity-time">${this.formatTimeAgo(activity.time)}</div>
                        </div>
                    `;
                    
                    activityList.appendChild(li);
                });
            }

            getActivityIcon(type) {
                switch(type) {
                    case 'location': return 'fa-map-marker-alt';
                    case 'obstacle': return 'fa-exclamation-triangle';
                    case 'battery': return 'fa-battery-full';
                    case 'movement': return 'fa-walking';
                    case 'alert': return 'fa-bell';
                    default: return 'fa-info-circle';
                }
            }
        }

        // Initialize backend
        const backend = new GabayLakadBackend();

        // Logout functionality
        document.getElementById('logoutBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to logout?')) {
                // Optional: redirect to login page
                window.location.href = '/index.php';
            }
        });

        // Sidebar toggle
        document.getElementById('menu-toggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const overlay = document.getElementById('overlay');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            
            if (sidebar.classList.contains('collapsed')) {
                this.innerHTML = '<i class="fas fa-chevron-right"></i>';
                overlay.classList.remove('show');
            } else {
                this.innerHTML = '<i class="fas fa-chevron-left"></i>';
                if (window.innerWidth <= 768) {
                    overlay.classList.add('show');
                }
            }
        });

        // Mobile overlay click
        document.getElementById('overlay').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const menuToggle = document.getElementById('menu-toggle');
            
            sidebar.classList.add('collapsed');
            mainContent.classList.remove('expanded');
            this.classList.remove('show');
            menuToggle.innerHTML = '<i class="fas fa-chevron-right"></i>';
        });

        // Handle resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            if (window.innerWidth > 768) {
                overlay.classList.remove('show');
            }
        });

        // Tab navigation
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all links and tabs
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
                
                // Add active class to clicked link
                this.classList.add('active');
                
                // Show corresponding tab
                const tabId = this.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });

        // Emergency button
        document.getElementById('emergencyBtn').addEventListener('click', function() {
            alert('🚨 EMERGENCY ALERT SENT!\nSMS and notifications dispatched to emergency contacts.');
        });

        // Contact caregiver button
        document.getElementById('contactBtn').addEventListener('click', function() {
            alert('📞 Calling caregiver: Kevin Keith P. Selisana...');
        });
    </script>
</body>
</html>