// Authentication related functions
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

// Form validation
function validateLoginForm() {
    const phoneNumber = document.getElementById('phoneNumber').value.trim();
    const password = document.getElementById('password').value;
    
    if (!phoneNumber || !password) {
        alert('Please fill in all fields');
        return false;
    }
    
    // Basic phone number validation
    const phoneRegex = /^[0-9+\-\s\(\)]+$/;
    if (!phoneRegex.test(phoneNumber)) {
        alert('Please enter a valid phone number');
        return false;
    }
    
    return true;
}