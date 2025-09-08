<?php
session_start(); // Start session at the beginning

include 'connect.php';

$errorMsg = '';
$successMsg = '';

if(isset($_POST['signUp'])){
    $firstName = trim($_POST['fName']);
    $lastName = trim($_POST['lName']);
    $phoneNumber = trim($_POST['pNumber']);
    $relationshipUser = trim($_POST['relationship']);
    $otherRelationship = isset($_POST['otherRelationship']) ? trim($_POST['otherRelationship']) : '';
    $deviceID = trim($_POST['deviceId']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // If relationship is Other, use the specified value
    if($relationshipUser === "Other" && !empty($otherRelationship)) {
        $relationshipUser = $otherRelationship;
    }

    // Basic validation
    if(empty($firstName) || empty($lastName) || empty($phoneNumber) || empty($relationshipUser) || empty($deviceID) || empty($password) || empty($confirmPassword)) {
        $errorMsg = "Please fill in all fields!";
    }
    // Check if passwords match
    else if($password !== $confirmPassword) {
        $errorMsg = "Passwords do not match!";
    }
    // Check password strength
    else if(strlen($password) < 8) {
        $errorMsg = "Password must be at least 8 characters long!";
    }
    // Validate phone number (basic check)
    else if(!preg_match('/^[0-9+\-\s\(\)]+$/', $phoneNumber)) {
        $errorMsg = "Please enter a valid phone number!";
    } else {
        // Hash password securely
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Check for duplicate device ID or phone number
        $checkStmt = $conn->prepare("SELECT user_id FROM users WHERE device_id = ? OR phone_number = ?");
        $checkStmt->bind_param("ss", $deviceID, $phoneNumber);
        $checkStmt->execute();
        $checkStmt->store_result();
        if($checkStmt->num_rows > 0) {
            $errorMsg = "Device ID or Phone Number already exists!";
        } else {
            // Insert user into database (without email column)
            $insertStmt = $conn->prepare("INSERT INTO users (first_name, last_name, phone_number, relationship, device_id, password, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $insertStmt->bind_param("ssssss", $firstName, $lastName, $phoneNumber, $relationshipUser, $deviceID, $hashedPassword);
            if($insertStmt->execute()) {
                $successMsg = "Registration successful! You can now log in.";
                // Clear form data on success
                $_POST = array();
                // Redirect after a delay
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 3000);
                </script>";
            } else {
                $errorMsg = "Error during registration. Please try again.";
            }
            $insertStmt->close();
        }   
        $checkStmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GabayLakad - Caregiver Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        * {
            font-family: 'Poppins', sans-serif;
        }
       
        .register-container {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-radius: 16px;
            overflow: hidden;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
        }
        
        .btn-primary:hover:not(:disabled) {
            background: linear-gradient(135deg, #1d4ed8 0%, #4338ca 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }
        
        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .form-input {
            transition: all 0.3s ease;
            border-color: #e2e8f0;
        }
        
        .form-input:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
            border-color: #4f46e5;
        }
        
        .form-input::placeholder {
            color: #94a3b8;
        }
        
        .error-notification {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: slideIn 0.3s ease;
        }
        
        .success-notification {
            background: #dcfce7;
            border: 1px solid #a7f3d0;
            color: #166534;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #64748b;
        }
        
        .password-toggle:hover {
            color: #475569;
        }
        
        .terms-group a {
            transition: all 0.2s ease;
        }
        
        .terms-group a:hover {
            color: #4f46e5;
            text-decoration: underline;
        }
        
        .register-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899);
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        .floating {
            animation: float 6s ease-in-out infinite;
        }
        
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .required:after {
            content: " *";
            color: #ef4444;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full mx-auto">
        <!-- Registration Form -->
        <div class="relative">
            <div class="register-container relative bg-white">
                <div class="p-8">
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4 mx-auto">
                            <i class="fas fa-user-plus text-blue-600 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Create Account</h3>
                        <p class="text-gray-600">Join GabayLakad to monitor and support your loved one</p>
                    </div>
                    
                    <!-- Success Message -->
                    <?php if(!empty($successMsg)): ?>
                    <div class="success-notification">
                        <i class="fas fa-check-circle"></i>
                        <span><?php echo htmlspecialchars($successMsg); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Error Message -->
                    <?php if(!empty($errorMsg)): ?>
                    <div class="error-notification">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo htmlspecialchars($errorMsg); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="fName" class="block text-sm font-medium text-gray-700 mb-2 required">
                                    First Name
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    <input id="fName" name="fName" type="text" required 
                                           value="<?php echo isset($_POST['fName']) ? htmlspecialchars($_POST['fName']) : ''; ?>"
                                           class="form-input pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="Enter first name">
                                </div>
                            </div>
                            
                            <div>
                                <label for="lName" class="block text-sm font-medium text-gray-700 mb-2 required">
                                    Last Name
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    <input id="lName" name="lName" type="text" required 
                                           value="<?php echo isset($_POST['lName']) ? htmlspecialchars($_POST['lName']) : ''; ?>"
                                           class="form-input pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="Enter last name">
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label for="pNumber" class="block text-sm font-medium text-gray-700 mb-2 required">
                                Phone Number
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-phone text-gray-400"></i>
                                </div>
                                <input id="pNumber" name="pNumber" type="tel" required 
                                       value="<?php echo isset($_POST['pNumber']) ? htmlspecialchars($_POST['pNumber']) : ''; ?>"
                                       class="form-input pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Enter phone number">
                            </div>
                        </div>
                        
                        <div>
                            <label for="relationship" class="block text-sm font-medium text-gray-700 mb-2 required">
                                Relationship to User
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-users text-gray-400"></i>
                                </div>
                                <select id="relationship" name="relationship" required 
                                        class="form-input pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        onchange="showOtherRelationship()">
                                    <option value="">Select relationship</option>
                                    <option value="Parent" <?php echo (isset($_POST['relationship']) && $_POST['relationship'] == 'Parent') ? 'selected' : ''; ?>>Parent</option>
                                    <option value="Child" <?php echo (isset($_POST['relationship']) && $_POST['relationship'] == 'Child') ? 'selected' : ''; ?>>Child</option>
                                    <option value="Sibling" <?php echo (isset($_POST['relationship']) && $_POST['relationship'] == 'Sibling') ? 'selected' : ''; ?>>Sibling</option>
                                    <option value="Guardian" <?php echo (isset($_POST['relationship']) && $_POST['relationship'] == 'Guardian') ? 'selected' : ''; ?>>Guardian</option>
                                    <option value="Caregiver" <?php echo (isset($_POST['relationship']) && $_POST['relationship'] == 'Caregiver') ? 'selected' : ''; ?>>Caregiver</option>
                                    <option value="Other" <?php echo (isset($_POST['relationship']) && $_POST['relationship'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div id="otherRelationshipContainer" class="<?php echo (isset($_POST['relationship']) && $_POST['relationship'] == 'Other') ? '' : 'hidden'; ?>">
                            <label for="otherRelationship" class="block text-sm font-medium text-gray-700 mb-2">
                                Please specify relationship
                            </label>
                            <input id="otherRelationship" name="otherRelationship" type="text" 
                                   value="<?php echo isset($_POST['otherRelationship']) ? htmlspecialchars($_POST['otherRelationship']) : ''; ?>"
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Enter relationship">
                        </div>
                        
                        <div>
                            <label for="deviceId" class="block text-sm font-medium text-gray-700 mb-2 required">
                                Device ID
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-mobile-alt text-gray-400"></i>
                                </div>
                                <input id="deviceId" name="deviceId" type="text" required 
                                       value="<?php echo isset($_POST['deviceId']) ? htmlspecialchars($_POST['deviceId']) : ''; ?>"
                                       class="form-input pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Enter device ID">
                            </div>
                            <p class="mt-1 text-sm text-gray-500">The unique ID of the tracking device</p>
                        </div>
                        
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2 required">
                                Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input id="password" name="password" type="password" required 
                                       class="form-input pl-10 pr-12 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Create password" onkeyup="checkPasswordStrength()">
                                <span class="password-toggle" onclick="togglePassword('password')">
                                    <i id="passwordToggleIcon" class="fas fa-eye"></i>
                                </span>
                            </div>
                            <div id="passwordStrength" class="mt-2 text-xs"></div>
                            <p class="mt-1 text-sm text-gray-500">Minimum 8 characters with letters and numbers</p>
                        </div>
                        
                        <div>
                            <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-2 required">
                                Confirm Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input id="confirmPassword" name="confirmPassword" type="password" required 
                                       class="form-input pl-10 pr-12 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Confirm password" onkeyup="checkPasswordMatch()">
                                <span class="password-toggle" onclick="togglePassword('confirmPassword')">
                                    <i id="confirmPasswordToggleIcon" class="fas fa-eye"></i>
                                </span>
                            </div>
                            <div id="passwordMatch" class="mt-2 text-sm"></div>
                        </div>
                        
                        <div class="flex items-start">
                            <input id="terms" name="terms" type="checkbox" required 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1">
                            <label for="terms" class="ml-2 block text-sm text-gray-700">
                                I agree to the <a href="terms.php" class="font-medium text-blue-600 hover:text-blue-500">Terms and Conditions</a> and <a href="privacy.php" class="font-medium text-blue-600 hover:text-blue-500">Privacy Policy</a>
                            </label>
                        </div>
                        
                        <div>
                            <button type="submit" name="signUp" class="btn-primary w-full py-3 px-4 border border-transparent rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-lg font-semibold transition-all">
                                Create Account
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                        <p class="text-sm text-gray-600">
                            Already have an account? 
                            <a href="login.php" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                                Sign in
                            </a>
                        </p>
                    </div>
                </div>
                
                <!-- Decorative elements -->
                <div class="absolute -top-4 -right-4 w-24 h-24 bg-blue-100 rounded-full opacity-50 floating"></div>
                <div class="absolute -bottom-4 -left-4 w-16 h-16 bg-indigo-100 rounded-full opacity-50 floating" style="animation-delay: 2s"></div>
            </div>
            
            <!-- Mobile features moved to bottom -->
            <div class="mt-6">
                <div class="bg-white rounded-xl p-6 shadow-lg">
                    <h4 class="font-semibold text-gray-900 mb-4 text-center">Why Choose GabayLakad?</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-map-marker-alt text-blue-600"></i>
                            </div>
                            <p class="text-xs text-gray-600">GPS Tracking</p>
                        </div>
                        <div class="text-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-exclamation-triangle text-blue-600"></i>
                            </div>
                            <p class="text-xs text-gray-600">Emergency Alerts</p>
                        </div>
                        <div class="text-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-history text-blue-600"></i>
                            </div>
                            <p class="text-xs text-gray-600">Location History</p>
                        </div>
                        <div class="text-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-shield-alt text-blue-600"></i>
                            </div>
                            <p class="text-xs text-gray-600">Secure Access</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Show/hide other relationship field
        function showOtherRelationship() {
            const relationship = document.getElementById('relationship').value;
            const otherContainer = document.getElementById('otherRelationshipContainer');
            const otherInput = document.getElementById('otherRelationship');
            
            if (relationship === 'Other') {
                otherContainer.classList.remove('hidden');
                otherInput.required = true;
            } else {
                otherContainer.classList.add('hidden');
                otherInput.required = false;
                otherInput.value = '';
            }
        }
        
        // Toggle password visibility
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const toggleIcon = document.getElementById(fieldId + 'ToggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Check password strength
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthDiv = document.getElementById('passwordStrength');
            
            if (password.length === 0) {
                strengthDiv.innerHTML = '';
                return;
            }
            
            let strength = 0;
            let message = '';
            let color = '';
            
            // Check length
            if (password.length >= 8) strength++;
            
            // Check for lowercase
            if (/[a-z]/.test(password)) strength++;
            
            // Check for uppercase
            if (/[A-Z]/.test(password)) strength++;
            
            // Check for numbers
            if (/[0-9]/.test(password)) strength++;
            
            // Check for special characters
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            if (strength < 3) {
                message = 'Weak password';
                color = 'text-red-600';
            } else if (strength < 5) {
                message = 'Medium strength';
                color = 'text-yellow-600';
            } else {
                message = 'Strong password';
                color = 'text-green-600';
            }
            
            strengthDiv.innerHTML = `<span class="${color}"><i class="fas fa-info-circle"></i> ${message}</span>`;
        }
        
        // Check password match
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const matchDiv = document.getElementById('passwordMatch');
            
            if (confirmPassword === '') {
                matchDiv.innerHTML = '';
                return;
            }
            
            if (password === confirmPassword) {
                matchDiv.innerHTML = '<span class="text-green-600"><i class="fas fa-check-circle"></i> Passwords match</span>';
            } else {
                matchDiv.innerHTML = '<span class="text-red-600"><i class="fas fa-times-circle"></i> Passwords do not match</span>';
            }
        }
        
        // Form validation on submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const terms = document.getElementById('terms').checked;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
            
            if (!terms) {
                e.preventDefault();
                alert('Please accept the Terms and Conditions!');
                return false;
            }
            
            // Show loading state
            const submitBtn = document.querySelector('button[name="signUp"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loading-spinner"></span> Creating Account...';
        });
        
        // Auto-hide notifications after 5 seconds
        setTimeout(function() {
            const notifications = document.querySelectorAll('.error-notification, .success-notification');
            notifications.forEach(function(notification) {
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(-10px)';
                setTimeout(function() {
                    notification.remove();
                }, 300);
            });
        }, 5000);
        
        // Initialize form on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Check if "Other" relationship is selected on page load
            showOtherRelationship();
            
            // Add input formatting for phone number
            const phoneInput = document.getElementById('pNumber');
            phoneInput.addEventListener('input', function(e) {
                // Remove non-numeric characters except +, -, space, ()
                let value = e.target.value.replace(/[^\d+\-\s\(\)]/g, '');
                e.target.value = value;
            });
            
            // Add real-time validation for device ID
            const deviceInput = document.getElementById('deviceId');
            deviceInput.addEventListener('input', function(e) {
                // Remove spaces and convert to uppercase for consistency
                e.target.value = e.target.value.replace(/\s/g, '').toUpperCase();
            });
            
            // Add character counter for name fields
            const firstNameInput = document.getElementById('fName');
            const lastNameInput = document.getElementById('lName');
            
            [firstNameInput, lastNameInput].forEach(function(input) {
                input.addEventListener('input', function(e) {
                    // Capitalize first letter of each word
                    const words = e.target.value.split(' ');
                    const capitalizedWords = words.map(word => 
                        word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
                    );
                    e.target.value = capitalizedWords.join(' ');
                });
            });
        });
        
        // Enhanced form validation with better error handling
        function validateForm() {
            const firstName = document.getElementById('fName').value.trim();
            const lastName = document.getElementById('lName').value.trim();
            const phoneNumber = document.getElementById('pNumber').value.trim();
            const relationship = document.getElementById('relationship').value;
            const otherRelationship = document.getElementById('otherRelationship').value.trim();
            const deviceId = document.getElementById('deviceId').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const terms = document.getElementById('terms').checked;
            
            let isValid = true;
            let errorMessages = [];
            
            // Name validation
            if (firstName.length < 2) {
                errorMessages.push('First name must be at least 2 characters long.');
                isValid = false;
            }
            
            if (lastName.length < 2) {
                errorMessages.push('Last name must be at least 2 characters long.');
                isValid = false;
            }
            
            // Phone number validation
            const phoneRegex = /^[+]?[\d\s\-\(\)]{10,}$/;
            if (!phoneRegex.test(phoneNumber)) {
                errorMessages.push('Please enter a valid phone number (at least 10 digits).');
                isValid = false;
            }
            
            // Relationship validation
            if (relationship === 'Other' && otherRelationship === '') {
                errorMessages.push('Please specify your relationship when "Other" is selected.');
                isValid = false;
            }
            
            // Device ID validation
            if (deviceId.length < 3) {
                errorMessages.push('Device ID must be at least 3 characters long.');
                isValid = false;
            }
            
            // Password validation
            if (password.length < 8) {
                errorMessages.push('Password must be at least 8 characters long.');
                isValid = false;
            }
            
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/;
            if (!passwordRegex.test(password)) {
                errorMessages.push('Password must contain at least one uppercase letter, one lowercase letter, and one number.');
                isValid = false;
            }
            
            if (password !== confirmPassword) {
                errorMessages.push('Passwords do not match.');
                isValid = false;
            }
            
            // Terms validation
            if (!terms) {
                errorMessages.push('You must accept the Terms and Conditions.');
                isValid = false;
            }
            
            // Display errors if any
            if (!isValid) {
                showValidationErrors(errorMessages);
            }
            
            return isValid;
        }
        
        // Function to display validation errors
        function showValidationErrors(errors) {
            // Remove existing error notifications
            const existingErrors = document.querySelectorAll('.validation-error');
            existingErrors.forEach(error => error.remove());
            
            // Create new error notification
            const errorDiv = document.createElement('div');
            errorDiv.className = 'validation-error error-notification';
            
            let errorContent = '<i class="fas fa-exclamation-circle"></i><div>';
            if (errors.length === 1) {
                errorContent += errors[0];
            } else {
                errorContent += 'Please fix the following errors:<ul class="mt-2 ml-4">';
                errors.forEach(error => {
                    errorContent += `<li class="text-sm">${error}</li>`;
                });
                errorContent += '</ul>';
            }
            errorContent += '</div>';
            
            errorDiv.innerHTML = errorContent;
            
            // Insert error at the top of the form
            const form = document.querySelector('form');
            form.insertBefore(errorDiv, form.firstChild);
            
            // Scroll to top of form to show error
            errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        // Update the form submit event listener
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault(); // Always prevent default first
            
            if (validateForm()) {
                // Show loading state
                const submitBtn = document.querySelector('button[name="signUp"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="loading-spinner"></span> Creating Account...';
                
                // Submit the form after a short delay to show loading state
                setTimeout(() => {
                    this.submit();
                }, 500);
            }
        });
        
        // Add smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Add keyboard navigation support
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.tagName === 'INPUT') {
                // Find next input field and focus it
                const inputs = Array.from(document.querySelectorAll('input, select'));
                const currentIndex = inputs.indexOf(e.target);
                const nextInput = inputs[currentIndex + 1];
                
                if (nextInput && nextInput.type !== 'submit') {
                    e.preventDefault();
                    nextInput.focus();
                } else if (e.target.form) {
                    // If it's the last input, try to submit the form
                    const submitBtn = e.target.form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.click();
                    }
                }
            }
        });
    </script>
</body>
</html>