
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
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
            overflow-x: hidden;
            min-height: 100vh;
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
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            transform: translateX(0);
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            z-index: 1000;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
        }

        .sidebar.collapsed {
            transform: translateX(-280px);
        }

        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(52, 152, 219, 0.1) 0%, rgba(155, 89, 182, 0.1) 100%);
            pointer-events: none;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
        }

        .logo {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }

        .sidebar-header h2 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #ecf0f1;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .menu-toggle {
            position: absolute;
            right: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: #bdc3c7;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .menu-toggle:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: scale(1.1);
        }

        .sidebar-menu {
            padding: 1.5rem 0;
        }

        .nav-list {
            list-style: none;
        }

        .nav-item {
            margin: 0.5rem 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            border-left: 3px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.6s ease;
        }

        .nav-link:hover::before {
            left: 100%;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ecf0f1;
            border-left-color: #3498db;
            transform: translateX(5px);
            box-shadow: inset 0 0 20px rgba(52, 152, 219, 0.2);
        }

        .nav-link.active {
            background: linear-gradient(135deg, rgba(52, 152, 219, 0.2), rgba(155, 89, 182, 0.2));
            color: #3498db;
            border-left-color: #3498db;
            box-shadow: inset 0 0 20px rgba(52, 152, 219, 0.3);
        }

        .nav-link i {
            width: 25px;
            margin-right: 1rem;
            font-size: 1.2rem;
            text-align: center;
        }

        .nav-link span {
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .logout-btn {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            margin: 1rem 1.5rem;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
        }

        .logout-btn:hover {
            background: linear-gradient(135deg, #c0392b, #a93226);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.4);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .main-content.expanded {
            margin-left: 0;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }

        .user-details h4 {
            color: #2c3e50;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .user-details p {
            color: #7f8c8d;
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Patient Info Banner */
        .patient-info {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 50%, #8e44ad 100%);
            padding: 2rem;
            border-radius: 20px;
            color: white;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 2rem;
            flex-wrap: wrap;
            box-shadow: 0 12px 40px rgba(52, 152, 219, 0.3);
            position: relative;
            overflow: hidden;
        }

        .patient-info::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(180deg); }
        }

        .patient-avatar {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            font-weight: 700;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }

        .patient-details {
            flex: 1;
            z-index: 1;
        }

        .patient-details h3 {
            font-size: 1.8rem;
            margin-bottom: 0.75rem;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .patient-details p {
            opacity: 0.95;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
            font-weight: 500;
        }

        .device-badge {
            background: rgba(255, 255, 255, 0.25);
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-size: 0.95rem;
            margin-top: 1rem;
            display: inline-block;
            font-weight: 600;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Dashboard Cards */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .dashboard-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px);
            position: relative;
            overflow: hidden;
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3498db, #9b59b6, #e74c3c, #f39c12);
            background-size: 400% 100%;
            animation: gradient-shift 3s ease infinite;
        }

        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .dashboard-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .card-icon.device { 
            background: linear-gradient(135deg, #27ae60, #2ecc71);
        }
        .card-icon.location { 
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }
        .card-icon.battery { 
            background: linear-gradient(135deg, #f39c12, #e67e22);
        }
        .card-icon.activity { 
            background: linear-gradient(135deg, #9b59b6, #8e44ad);
        }
        .card-icon.emergency { 
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }
        .card-icon.night { 
            background: linear-gradient(135deg, #f1c40f, #f39c12);
        }

        .card-title {
            font-size: 0.9rem;
            color: #7f8c8d;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }

        .card-value {
            font-size: 2.2rem;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card-trend {
            font-size: 0.95rem;
            color: #27ae60;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-trend.warning {
            color: #f39c12;
        }

        .card-trend.danger {
            color: #e74c3c;
        }

        .card-trend i {
            font-size: 1.1rem;
        }

        /* Status Indicators */
        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-indicator.online {
            background: linear-gradient(135deg, rgba(39, 174, 96, 0.15), rgba(46, 204, 113, 0.15));
            color: #27ae60;
            border: 2px solid rgba(39, 174, 96, 0.3);
        }

        .status-indicator.offline {
            background: linear-gradient(135deg, rgba(231, 76, 60, 0.15), rgba(192, 57, 43, 0.15));
            color: #e74c3c;
            border: 2px solid rgba(231, 76, 60, 0.3);
        }

        .status-indicator.walking {
            background: linear-gradient(135deg, rgba(52, 152, 219, 0.15), rgba(41, 128, 185, 0.15));
            color: #3498db;
            border: 2px solid rgba(52, 152, 219, 0.3);
        }

        .status-indicator.stationary {
            background: linear-gradient(135deg, rgba(149, 165, 166, 0.15), rgba(127, 140, 141, 0.15));
            color: #95a5a6;
            border: 2px solid rgba(149, 165, 166, 0.3);
        }

        /* Activity Feed */
        .activity-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .section-title i {
            font-size: 1.3rem;
            color: #3498db;
        }

        .activity-list {
            list-style: none;
            max-height: 500px;
            overflow-y: auto;
            padding-right: 1rem;
        }

        .activity-list::-webkit-scrollbar {
            width: 6px;
        }

        .activity-list::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 3px;
        }

        .activity-list::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #3498db, #9b59b6);
            border-radius: 3px;
        }

        .activity-item {
            padding: 1.5rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: flex-start;
            gap: 1.5rem;
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            background: rgba(52, 152, 219, 0.05);
            border-radius: 12px;
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            flex-shrink: 0;
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .activity-details {
            font-size: 0.9rem;
            color: #7f8c8d;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .activity-time {
            font-size: 0.85rem;
            color: #95a5a6;
            font-weight: 600;
        }

        /* Tab Content */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
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
                padding: 1rem;
            }

            .overlay.show {
                display: block;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .patient-info {
                flex-direction: column;
                text-align: center;
                gap: 1.5rem;
            }

            .header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
                padding: 1.5rem;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .card-icon {
                align-self: flex-end;
            }
        }

        /* Additional animations */
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .status-indicator.online i {
            animation: pulse 2s ease-in-out infinite;
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
                                <div class="card-value" style="font-size: 1.4rem;" id="currentLocation">Ayala Center</div>
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
                </div>
            </div>

            <div class="tab-content" id="history">
                <div class="activity-section">
                    <h2 class="section-title">History</h2>
                </div>
            </div>

            <div class="tab-content" id="location">
                <div class="activity-section">
                    <h2 class="section-title">Location Tracking</h2>
                </div>
            </div>

            <div class="tab-content" id="sensor">
                <div class="activity-section">
                    <h2 class="section-title">Sensor Data</h2>
                </div>
            </div>

            <div class="tab-content" id="alerts">
                <div class="activity-section">
                    <h2 class="section-title">Safety Alerts</h2>
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
    </script>
</body>
</html>
