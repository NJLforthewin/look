<?php
session_start(); // Start session at the beginning

include 'connect.php';

$errorMsg = '';
$successMsg = '';

if(isset($_POST['login'])){
    $fullName = trim($_POST['fullName']);
    $password = $_POST['password'];

    // Validate input
    if(empty($fullName) || empty($password)) {
        $errorMsg = "Please fill in all fields";
    } else {
        // Split full name into first and last name (simple split, may need improvement)
        $nameParts = explode(' ', $fullName, 2);
        $firstName = $nameParts[0];
        $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

        // Use prepared statement to fetch user
        $stmt = $conn->prepare("SELECT * FROM users WHERE first_name = ? AND last_name = ?");
        $stmt->bind_param("ss", $firstName, $lastName);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            // Verify password using password_verify
            if(password_verify($password, $row['password'])){
                $_SESSION['user_id'] = $row['id']; // Store user ID
                $_SESSION['fullName'] = $fullName;
                $_SESSION['firstName'] = $firstName;
                $_SESSION['lastName'] = $lastName;
                $successMsg = "Login successful! Redirecting...";
                // Use JavaScript redirect instead of header redirect to show success message
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'dashboard.php';
                    }, 2000);
                </script>";
            } else {
                $errorMsg = "Incorrect password!";
            }
        } else {
            $errorMsg = "User not found!";
        }
        $stmt->close();
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
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        .gabaylakad-gradient {
            background: linear-gradient(135deg, #1e40af 0%, #3730a3 100%);
        }
        
        .login-container {
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
        
        .forgot-password a {
            transition: all 0.2s ease;
        }
        
        .forgot-password a:hover {
            color: #4f46e5;
            text-decoration: underline;
        }
        
        .register-link a {
            transition: all 0.2s ease;
        }
        
        .register-link a:hover {
            color: #4f46e5;
            text-decoration: underline;
        }
        
        .feature-bullet {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
            color: #475569;
            font-size: 0.95rem;
        }
        
        .feature-bullet i {
            color: #3b82f6;
        }
        
        .login-container::before {
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
    </style>
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
                        
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="space-y-6">
                            <div>
                                <label for="fullName" class="block text-sm font-medium text-gray-700 mb-2">
                                    Full Name
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    <input id="fullName" name="fullName" type="text" required 
                                           value="<?php echo isset($_POST['fullName']) ? htmlspecialchars($_POST['fullName']) : ''; ?>"
                                           class="form-input pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="Enter your full name">
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
                                <div class="text-sm">
                                    <a href="forgot-password.php" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                                        Forgot password?
                                    </a>
                                </div>
                            </div>
                            
                            <div>
                                <button type="submit" name="login" class="btn-primary w-full py-3 px-4 border border-transparent rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-lg font-semibold transition-all">
                                    Sign in to Dashboard
                                </button>
                            </div>
                        </form>
                        
                        <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                            <p class="text-sm text-gray-600">
                                Don't have an account? 
                                <a href="register.php" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
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
        // Password toggle functionality
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
        
        // Add some interactivity to the form fields
        const formInputs = document.querySelectorAll('.form-input');
        formInputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('scale-105');
                this.parentElement.style.transition = 'transform 0.2s ease';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('scale-105');
            });
        });
    </script>
</body>
</html>