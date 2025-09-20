// Dashboard data management
class GabayLakadDashboard {
    constructor() {
        this.isConnected = false;
        this.data = {};
        this.updateInterval = null;
        this.init();
    }

    init() {
        this.loadDashboardData();
        this.startAutoUpdate();
    }

    loadDashboardData() {
        fetch('/get_dashboard_data.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.data = data.data;
                    this.updateUI();
                    this.isConnected = true;
                } else {
                    console.error('Failed to load dashboard data:', data.message);
                    this.showError('Failed to load dashboard data');
                }
            })
            .catch(error => {
                console.error('Error loading dashboard data:', error);
                this.showError('Connection error');
            });
    }

    startAutoUpdate() {
        this.updateInterval = setInterval(() => {
            if (this.isConnected) {
                this.loadDashboardData();
            }
        }, 30000);
    }

    updateUI() {
        if (!this.data) return;

        // Update patient information
        if (this.data.patient) {
            this.updateElement('patientName', this.data.patient.name);
            this.updateElement('patientAge', this.data.patient.age);
            this.updateElement('patientCondition', this.data.patient.impairment_level);
        }

        // Update device information
        if (this.data.device) {
            this.updateElement('deviceId', this.data.device.serial_number);
            this.updateDeviceStatus(this.data.device.is_active);
        }

        // Update location information
        if (this.data.location) {
            this.updateElement('currentLocation', this.formatLocationName(this.data.location.latitude, this.data.location.longitude));
            this.updateElement('locationUpdate', this.formatTimeAgo(new Date(this.data.location.timestamp)));
        }

        // Update caregiver information
        if (this.data.caregiver) {
            this.updateElement('emergencyContact', 
                `${this.data.caregiver.name} (Caregiver) - ${this.data.caregiver.phone_number}`);
        }

        // Update sensor data
        if (this.data.sensors && this.data.sensors.length > 0) {
            this.updateSensorData(this.data.sensors);
        }

        // Update activity log
        if (this.data.activities) {
            this.renderActivityList(this.data.activities);
        }

        // Update alerts
        if (this.data.alerts) {
            this.updateAlerts(this.data.alerts);
        }
    }

    updateElement(id, value) {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = value;
        }
    }

    updateDeviceStatus(isActive) {
        const deviceStatusElement = document.getElementById('deviceStatus');
        const deviceStatusTextElement = document.getElementById('deviceStatusText');
        const deviceStatusIndicator = document.getElementById('deviceStatusIndicator');

        if (isActive == 1) {
            if (deviceStatusElement) deviceStatusElement.textContent = 'Connected';
            if (deviceStatusTextElement) deviceStatusTextElement.textContent = 'Online';
            if (deviceStatusIndicator) deviceStatusIndicator.className = 'status-indicator online';
        } else {
            if (deviceStatusElement) deviceStatusElement.textContent = 'Disconnected';
            if (deviceStatusTextElement) deviceStatusTextElement.textContent = 'Offline';
            if (deviceStatusIndicator) deviceStatusIndicator.className = 'status-indicator offline';
        }
    }

    updateSensorData(sensors) {
        const batterySensor = sensors.find(s => s.sensor_type === 'battery');
        if (batterySensor) {
            this.updateElement('batteryLevel', `${Math.floor(batterySensor.sensor_value)}%`);
            this.updateBatteryIcon(batterySensor.sensor_value);
        }

        const stepSensor = sensors.find(s => s.sensor_type === 'steps');
        if (stepSensor) {
            this.updateElement('stepCount', stepSensor.sensor_value.toLocaleString());
        }
    }

    updateBatteryIcon(batteryLevel) {
        const batteryIconElement = document.getElementById('batteryIcon');
        if (!batteryIconElement) return;

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

    renderActivityList(activities) {
        const activityList = document.getElementById('activityList');
        if (!activityList) return;

        activityList.innerHTML = '';

        activities.forEach(activity => {
            const li = document.createElement('li');
            li.className = 'activity-item';

            const iconColor = this.getActivityIconColor(activity.type);
            const icon = this.getActivityIcon(activity.type);

            li.innerHTML = `
                <div class="activity-icon" style="background: ${iconColor}">
                    <i class="fas ${icon}"></i>
                </div>
                <div class="activity-content">
                    <div class="activity-title">${activity.title}</div>
                    <div class="activity-details">${activity.details}</div>
                    <div class="activity-time">${this.formatTimeAgo(new Date(activity.timestamp))}</div>
                </div>
            `;

            activityList.appendChild(li);
        });
    }

    updateAlerts(alerts) {
        const unresolvedAlerts = alerts.filter(alert => alert.is_resolved == 0);
        const emergencySystemStatus = document.getElementById('emergencySystemStatus');
        
        if (emergencySystemStatus) {
            if (unresolvedAlerts.length > 0) {
                emergencySystemStatus.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Alert Active';
                emergencySystemStatus.className = 'status-indicator offline';
            } else {
                emergencySystemStatus.innerHTML = '<i class="fas fa-check"></i> Ready';
                emergencySystemStatus.className = 'status-indicator online';
            }
        }
    }

    getActivityIconColor(type) {
        switch(type) {
            case 'location': return '#e74c3c';
            case 'obstacle': return '#f39c12';
            case 'battery': return '#27ae60';
            case 'movement': return '#3498db';
            case 'alert': return '#e74c3c';
            case 'sensor': return '#9b59b6';
            default: return '#95a5a6';
        }
    }

    getActivityIcon(type) {
        switch(type) {
            case 'location': return 'fa-map-marker-alt';
            case 'obstacle': return 'fa-exclamation-triangle';
            case 'battery': return 'fa-battery-full';
            case 'movement': return 'fa-walking';
            case 'alert': return 'fa-bell';
            case 'sensor': return 'fa-microchip';
            default: return 'fa-info-circle';
        }
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

    formatLocationName(latitude, longitude) {
        if (!latitude || !longitude) return "Unknown Location";
        return "Lat: " + parseFloat(latitude).toFixed(4) + ", Lng: " + parseFloat(longitude).toFixed(4);
    }

    showError(message) {
        console.error(message);
    }
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new GabayLakadDashboard();
});