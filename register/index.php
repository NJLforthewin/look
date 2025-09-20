<?php
session_start();
include '../connect.php';
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
    $userFullName = trim($_POST['userFullName']);
    $userAge = trim($_POST['userAge']);
    $userPhoneNumber = trim($_POST['userPhoneNumber']) ?: NULL;
    $impairmentLevel = trim($_POST['impairmentLevel']);

    // If relationship is Other, use the specified value
    if($relationshipUser === "Other" && !empty($otherRelationship)) {
        $relationshipUser = $otherRelationship;
    }

    // Basic validation (userPhoneNumber is optional)
    if(empty($firstName) || empty($lastName) || empty($phoneNumber) || empty($relationshipUser) || 
       empty($deviceID) || empty($password) || empty($confirmPassword) || empty($userFullName) || 
       empty($userAge) || empty($impairmentLevel)) {
        $errorMsg = "Please fill in all required fields!";
    }
    else if($password !== $confirmPassword) {
        $errorMsg = "Passwords do not match!";
    }
    else if(strlen($password) < 8) {
        $errorMsg = "Password must be at least 8 characters long!";
    }
    else if(!preg_match('/^[0-9+\-\s\(\)]+$/', $phoneNumber)) {
        $errorMsg = "Please enter a valid caregiver phone number!";
    }
    else if(!empty($userPhoneNumber) && !preg_match('/^[0-9+\-\s\(\)]+$/', $userPhoneNumber)) {
        $errorMsg = "Please enter a valid user phone number!";
    }
    else if(!is_numeric($userAge) || $userAge < 1 || $userAge > 120) {
        $errorMsg = "Please enter a valid age (1-120)!";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $conn->begin_transaction();
        
        try {
            // Check if device exists and is available
            $deviceCheckStmt = $conn->prepare("SELECT device_id FROM device WHERE serial_number = ? AND is_active = 1");
            $deviceCheckStmt->bind_param("s", $deviceID);
            $deviceCheckStmt->execute();
            $deviceResult = $deviceCheckStmt->get_result();
            
            if($deviceResult->num_rows == 0) {
                throw new Exception("Device ID not found or inactive!");
            }
            $deviceRow = $deviceResult->fetch_assoc();
            $deviceDbId = $deviceRow['device_id'];
            $deviceCheckStmt->close();

            // Check if device is already assigned
            $deviceAssignedStmt = $conn->prepare("SELECT user_id FROM user WHERE device_id = ?");
            $deviceAssignedStmt->bind_param("i", $deviceDbId);
            $deviceAssignedStmt->execute();
            $assignedResult = $deviceAssignedStmt->get_result();
            
            if($assignedResult->num_rows > 0) {
                throw new Exception("Device is already assigned to another user!");
            }
            $deviceAssignedStmt->close();

            // Check for duplicate caregiver phone number
            $caregiverCheckStmt = $conn->prepare("SELECT contact_id FROM contact WHERE phone_number = ?");
            $caregiverCheckStmt->bind_param("s", $phoneNumber);
            $caregiverCheckStmt->execute();
            $caregiverResult = $caregiverCheckStmt->get_result();
            
            if($caregiverResult->num_rows > 0) {
                throw new Exception("Caregiver phone number already registered!");
            }
            $caregiverCheckStmt->close();

            // Insert caregiver into contact table
            $caregiverName = $firstName . ' ' . $lastName;
            $insertCaregiverStmt = $conn->prepare("INSERT INTO contact (name, phone_number, password, is_active, created_at, updated_at) VALUES (?, ?, ?, 1, NOW(), NOW())");
            $insertCaregiverStmt->bind_param("sss", $caregiverName, $phoneNumber, $hashedPassword);
            
            if(!$insertCaregiverStmt->execute()) {
                throw new Exception("Error creating caregiver account!");
            }
            
            $contactId = $conn->insert_id;
            $insertCaregiverStmt->close();

            // Insert user being monitored
            $insertUserStmt = $conn->prepare("INSERT INTO user (name, phone_number, impairment_level, age, device_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
            $insertUserStmt->bind_param("sssii", $userFullName, $userPhoneNumber, $impairmentLevel, $userAge, $deviceDbId);
            
            if(!$insertUserStmt->execute()) {
                throw new Exception("Error creating user record!");
            }
            
            $userId = $conn->insert_id;
            $insertUserStmt->close();

            // Link caregiver to user
            $insertRelationshipStmt = $conn->prepare("INSERT INTO user_contact (contact_id, user_id, relationship, created_at) VALUES (?, ?, ?, NOW())");
            $insertRelationshipStmt->bind_param("iis", $contactId, $userId, $relationshipUser);
            
            if(!$insertRelationshipStmt->execute()) {
                throw new Exception("Error creating relationship!");
            }
            $insertRelationshipStmt->close();

            $conn->commit();
            
            $successMsg = "Registration successful! You can now log in as caregiver.";
            $_POST = array();
            
            echo "<script>
                setTimeout(function() {
                    window.location.href = '../login/index.php';
                }, 3000);
            </script>";
        } catch (Exception $e) {
            $conn->rollback();
            $errorMsg = $e->getMessage();
        }
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
    <link rel="stylesheet" href="css/index.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full mx-auto">
        <div class="relative">
            <div class="register-container relative bg-white">
                <div class="p-8">
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4 mx-auto">
                            <i class="fas fa-user-plus text-blue-600 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Create Caregiver Account</h3>
                        <p class="text-gray-600">Register to monitor and support your loved one</p>
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

                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="space-y-6" onsubmit="return validateRegistrationForm()">
                        
                        <!-- Caregiver Information Section -->
                        <div class="section-divider" data-title="Your Information (Caregiver)">
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
                                               placeholder="Enter your first name">
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
                                                placeholder="Enter your last name">
                                        </div>
                                    </div>
                                </div>
                                
                            <div>
                                <label for="pNumber" class="block text-sm font-medium text-gray-700 mb-2 required">
                                    Your Phone Number
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-phone text-gray-400"></i>
                                    </div>
                                    <input id="pNumber" name="pNumber" type="tel" required 
                                           value="<?php echo isset($_POST['pNumber']) ? htmlspecialchars($_POST['pNumber']) : ''; ?>"
                                           class="form-input pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="Enter your phone number">
                                </div>
                            </div>
                        </div>
                        
                       <!-- Person Being Monitored Section -->
<div class="section-divider" data-title="Person You'll Monitor">
    <div>
        <label for="userFullName" class="block text-sm font-medium text-gray-700 mb-2 required">
            Full Name of Person
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-user-circle text-gray-400"></i>
            </div>
            <input id="userFullName" name="userFullName" type="text" required 
                   value="<?php echo isset($_POST['userFullName']) ? htmlspecialchars($_POST['userFullName']) : ''; ?>"
                   class="form-input pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="Enter full name of person you'll monitor">
        </div>
    </div>

    <div>
        <label for="userPhoneNumber" class="block text-sm font-medium text-gray-700 mb-2">
            Phone Number of Person
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-phone text-gray-400"></i>
            </div>
            <input id="userPhoneNumber" name="userPhoneNumber" type="tel" 
                   value="<?php echo isset($_POST['userPhoneNumber']) ? htmlspecialchars($_POST['userPhoneNumber']) : ''; ?>"
                   class="form-input pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="Enter their phone number (optional)">
        </div>
        <p class="mt-1 text-sm text-gray-500">Leave blank if they don't have a phone</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="userAge" class="block text-sm font-medium text-gray-700 mb-2 required">
                Age
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-calendar-alt text-gray-400"></i>
                </div>
                <input id="userAge" name="userAge" type="number" min="1" max="120" required 
                       value="<?php echo isset($_POST['userAge']) ? htmlspecialchars($_POST['userAge']) : ''; ?>"
                       class="form-input pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Age">
            </div>
        </div>

        <div>
            <label for="impairmentLevel" class="block text-sm font-medium text-gray-700 mb-2 required">
                Impairment Level
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-eye text-gray-400"></i>
                </div>
                <select id="impairmentLevel" name="impairmentLevel" required 
                        class="form-input pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Select impairment level</option>
                    <option value="Totally Blind" <?php echo (isset($_POST['impairmentLevel']) && $_POST['impairmentLevel'] == 'Totally Blind') ? 'selected' : ''; ?>>Totally Blind</option>
                    <option value="Partially Sighted" <?php echo (isset($_POST['impairmentLevel']) && $_POST['impairmentLevel'] == 'Partially Sighted') ? 'selected' : ''; ?>>Partially Sighted</option>
                    <option value="Low Vision" <?php echo (isset($_POST['impairmentLevel']) && $_POST['impairmentLevel'] == 'Low Vision') ? 'selected' : ''; ?>>Low Vision</option>
                </select>
            </div>
        </div>
    </div>
</div>
                        
                        <!-- Relationship and Device Section -->
                        <div class="section-divider" data-title="Relationship & Device">
                            <div>
                                <label for="relationship" class="block text-sm font-medium text-gray-700 mb-2 required">
                                    Your Relationship to This Person
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-users text-gray-400"></i>
                                    </div>
                                    <select id="relationship" name="relationship" r
                                        equired 
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
                                    Device Serial Number
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-mobile-alt text-gray-400"></i>
                                    </div>
                                    <input id="deviceId" name="deviceId" type="text" required 
                                           value="<?php echo isset($_POST['deviceId']) ? htmlspecialchars($_POST['deviceId']) : ''; ?>"
                                           class="form-input pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="Enter device serial number">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">The unique serial number found on the tracking device</p>
                            </div>
                        </div>
                        
                        <!-- Password Section -->
                        <div class="section-divider" data-title="Security">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2 required">
                                    Create Password
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
                        </div>
                        
                        <div class="flex items-start terms-group">
                            <input id="terms" name="terms" type="checkbox" required 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1">
                            <label for="terms" class="ml-2 block text-sm text-gray-700">
                                I agree to the <a href="../terms.php" class="font-medium text-blue-600 hover:text-blue-500">Terms and Conditions</a> and <a href="../privacy.php" class="font-medium text-blue-600 hover:text-blue-500">Privacy Policy</a>
                            </label>
                        </div>
                        
                        <div>
                            <button type="submit" name="signUp" class="btn-primary w-full py-3 px-4 border border-transparent rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-lg font-semibold transition-all">
                                Create Caregiver Account
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                        <p class="text-sm text-gray-600">
                            Already have an account? 
                            <a href="../login/index.php" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
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
    
    <script src="js/index.js"></script>
    <script src="js/auth.js"></script>
</body>
</html>