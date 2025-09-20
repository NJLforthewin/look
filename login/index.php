<?php
session_start();
include '../connect.php';

$errorMsg = '';
$successMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $phoneNumber = trim($_POST['phoneNumber']);
        $password = $_POST['password'];
        
        // DEBUG INFO to console
        echo "<script>console.log('DEBUG INFO:');</script>";
        echo "<script>console.log('Phone Number: " . addslashes($phoneNumber) . "');</script>";
        echo "<script>console.log('Password: " . addslashes($password) . "');</script>";
        
        if(empty($phoneNumber) || empty($password)) {
            $errorMsg = "Please fill in all fields";
        } else {
            $stmt = $conn->prepare("SELECT * FROM contact WHERE phone_number = ? AND is_active = 1");
            if (!$stmt) {
                $errorMsg = "Database error occurred";
            } else {
                $stmt->bind_param("s", $phoneNumber);
                $stmt->execute();
                $result = $stmt->get_result();
                
                // DEBUG INFO to console
                echo "<script>console.log('Records found: " . $result->num_rows . "');</script>";
                
                if($result->num_rows > 0){
                    $row = $result->fetch_assoc();
                    
                    // DEBUG INFO to console
                    echo "<script>console.log('Database password hash: " . addslashes($row['password']) . "');</script>";
                    echo "<script>console.log('is_active: " . $row['is_active'] . "');</script>";
                    $verifyResult = password_verify($password, $row['password']) ? 'TRUE' : 'FALSE';
                    echo "<script>console.log('Password verify result: " . $verifyResult . "');</script>";
                    
                    // ADDITIONAL DEBUGGING TESTS
                    echo "<script>console.log('=== DIRECT TESTS ===');</script>";
                    
                    if(password_verify($password, $row['password'])){
                        $_SESSION['contact_id'] = $row['contact_id'];
                        $_SESSION['caregiver_name'] = $row['name'];
                        $_SESSION['phone_number'] = $row['phone_number'];
                        
                        $successMsg = "Login successful! Redirecting...";
                        
                        echo "<script>
                            setTimeout(function() {
                                window.location.href = '../dashboard/index.php';
                            }, 2000);
                        </script>";
                    } else {
                        $errorMsg = "Incorrect password!";
                    }
                } else {
                    $errorMsg = "Phone number not found or account inactive!";
                }
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GabayLakad - Caregiver Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-4xl w-full mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            <!-- Left Side - Features -->
            <div class="hidden lg:block">
                <div class="text-center lg:text-left mb-8">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Welcome to GabayLakad</h2>
                    <p class="text-lg text-gray-600">Secure access to monitor and support your loved one</p>
                </div>
                
                <div class="bg-white rounded-xl p-8 shadow-lg">
                    <div class="space-y-6">
                        <div class="feature-bullet">
                            <i class="fas fa-check-circle text-xl"></i>
                            <span>Real-time GPS tracking of visually impaired individuals</span>
                        </div>
                        <div class="feature-bullet">
                            <i class="fas fa-check-circle text-xl"></i>
                            <span>Emergency alerts with automatic location sharing</span>
                        </div>
                        
                        <div class="feature-bullet">
                            <i class="fas fa-check-circle text-xl"></i>
                            <span>24/7 monitoring through comprehensive dashboard</span>
                        </div>
                        
                        <div class="feature-bullet">
                            <i class="fas fa-check-circle text-xl"></i>
                            <span>Automatic location logging every 2 minutes</span>
                        </div>
                        
                        <div class="feature-bullet">
                            <i class="fas fa-check-circle text-xl"></i>
                            <span>Secure access with encrypted authentication</span>
                        </div>
                    </div>
                    <div class="mt-8 p-6 bg-blue-50 rounded-lg">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-shield-alt text-blue-600 text-2xl mt-1"></i>
                            <div>
                                <h4 class="font-semibold text-blue-900 mb-2">Security First</h4>
                                <p class="text-blue-800 text-sm">Your data is protected with industry-standard encryption and secure authentication protocols.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Side - Login Form -->
            <div class="relative">
                <div class="login-container relative bg-white">
                    <div class="p-8">
                        <div class="text-center mb-6">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4 mx-auto">
                                <i class="fas fa-sign-in-alt text-blue-600 text-2xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Caregiver Login</h3>
                            <p class="text-gray-600">Access your secure dashboard to monitor and support your loved one</p>
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
                        
                        <form method="POST" action="" class="space-y-6">
                            <div>
                                <label for="phoneNumber" class="block text-sm font-medium text-gray-700 mb-2">
                                    Phone Number
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-phone text-gray-400"></i>
                                    </div>
                                    <input id="phoneNumber" name="phoneNumber" type="tel" required 
                                           value="<?php echo isset($_POST['phoneNumber']) ? htmlspecialchars($_POST['phoneNumber']) : ''; ?>"
                                           class="form-input pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="Enter your phone number">
                                </div>
                            </div>
                            
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Password
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-400"></i>
                                    </div>
                                    <input id="password" name="password" type="password" required 
                                           value=""
                                           class="form-input pl-10 pr-12 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="Enter your password">
                                    <span class="password-toggle" onclick="togglePassword()">
                                        <i id="toggleIcon" class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input id="remember-me" name="remember-me" type="checkbox" 
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                                        Remember me
                                    </label>
                                </div>
                                
                                <div class="text-sm forgot-password">
                                    <a href="../forgot-password.php" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                                        Forgot password?
                                    </a>
                                </div>
                            </div>
                            
                            <div>
                                <button type="submit" name="login" value="1" class="btn-primary w-full py-3 px-4 border border-transparent rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-lg font-semibold transition-all">
                                    Sign in to Dashboard
                                </button>
                            </div>
                        </form>
                        
                        <div class="mt-6 pt-6 border-t border-gray-200 text-center register-link">
                            <p class="text-sm text-gray-600">
                                Don't have an account? 
                                <a href="../register/index.php" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                                    Register now
                                </a>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Decorative elements -->
                    <div class="absolute -top-4 -right-4 w-24 h-24 bg-blue-100 rounded-full opacity-50 floating"></div>
                    <div class="absolute -bottom-4 -left-4 w-16 h-16 bg-indigo-100 rounded-full opacity-50 floating" style="animation-delay: 2s"></div>
                </div>
                
                <!-- Mobile features -->
                <div class="lg:hidden mt-6">
                    <div class="bg-white rounded-xl p-6 shadow-lg">
                        <h4 class="font-semibold text-gray-900 mb-4 text-center">Key Features</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <i class="fas fa-map-marker-alt text-blue-600"></i>
                                </div>
                                <p class="text-xs text-gray-600">Real-time Tracking</p>
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
    </div>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
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
    </script>
</body>
</html>