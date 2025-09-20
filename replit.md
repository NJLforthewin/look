# Overview

GabayLakad is a smart assistive navigation system designed for visually impaired individuals in the Philippines. The project combines Arduino-based hardware (smart cane with sensors) and a web-based monitoring dashboard that enables caregivers to track users in real-time. The system provides obstacle detection, GPS tracking, emergency alerts via GSM, and automatic night visibility features to enhance mobility and safety for blind users.

# User Preferences

Preferred communication style: Simple, everyday language.

# System Architecture

## Frontend Architecture
- **Technology Stack**: HTML, CSS, JavaScript with Tailwind CSS framework
- **Design Pattern**: Multi-page application with separate login, registration, and dashboard sections
- **UI Components**: Responsive sidebar navigation, collapsible mobile interface, real-time data displays
- **Authentication Flow**: Phone number and password-based login system with form validation

## Backend Architecture
- **Technology Stack**: PHP for server-side logic and API endpoints
- **Database Integration**: MariaDB/MySQL database with structured schema for user management and data logging
- **Data Management**: RESTful API pattern for dashboard data retrieval and user operations
- **Security**: Password-based authentication with session management

## Data Storage Design
The system uses a relational database with the following core entities:
- **Users**: Patient information including impairment level and demographics
- **Devices**: Arduino cane registration and status tracking
- **Contacts**: Caregiver information and authentication credentials
- **GPS Tracking**: Real-time location logging with timestamps
- **Alerts**: Emergency notifications and alert management
- **Sensor Logs**: Historical sensor data from the Arduino device

## Hardware Integration
- **Arduino Components**: Ultrasonic sensors, GPS modules (Neo-6M), GSM modules (SIM800L), light sensors
- **Communication Protocol**: GSM-based data transmission from device to web server
- **Real-time Updates**: Automatic 30-second refresh intervals for dashboard data

## Security Architecture
- **Authentication**: Phone number and password-based login system
- **Data Protection**: Secure caregiver dashboard access with logout functionality
- **Emergency System**: GSM-based SMS alerts with GPS coordinates for emergency situations

# External Dependencies

## Hardware Components
- **Arduino Uno/Nano**: Primary microcontroller platform
- **GPS Module**: Neo-6M for location tracking
- **GSM Module**: SIM800L for emergency communications and data transmission
- **Sensors**: Ultrasonic sensors for obstacle detection, LDR for light sensing
- **Output Devices**: Speakers/buzzers for audio feedback, vibration motors for haptic alerts

## Database System
- **MariaDB/MySQL**: Primary database for user data, location logs, and alert management
- **Schema**: Structured relational database with foreign key relationships between users, devices, and tracking data

## Frontend Libraries
- **Tailwind CSS**: Utility-first CSS framework for responsive design
- **Font Awesome**: Icon library for UI elements
- **Google Fonts**: Poppins font family for typography

## Communication Services
- **GSM Network**: For SMS emergency alerts and data transmission from Arduino devices
- **Web Hosting**: PHP-compatible server environment for dashboard deployment

## Browser APIs
- **Fetch API**: For asynchronous data loading and dashboard updates
- **Local Storage**: For client-side data persistence and session management