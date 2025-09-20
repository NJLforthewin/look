// Authentication related functions for registration
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

// Form validation for registration
function validateRegistrationForm() {
    const requiredFields = [
        'fName', 'lName', 'pNumber', 'userFullName', 
        'userAge', 'impairmentLevel', 'relationship', 'deviceId', 
        'password', 'confirmPassword'
    ];
    
    // Check if all required fields are filled
    for (let fieldId of requiredFields) {
        const field = document.getElementById(fieldId);
        if (!field || !field.value.trim()) {
            alert(`Please fill in the ${fieldId.replace(/([A-Z])/g, ' $1').toLowerCase()} field`);
            return false;
        }
    }
    
    // Check if Other relationship is specified
    const relationship = document.getElementById('relationship').value;
    if (relationship === 'Other') {
        const otherRelationship = document.getElementById('otherRelationship').value.trim();
        if (!otherRelationship) {
            alert('Please specify your relationship');
            return false;
        }
    }
    
    // Validate phone number
    const phoneNumber = document.getElementById('pNumber').value.trim();
    const phoneRegex = /^[0-9+\-\s\(\)]+$/;
    if (!phoneRegex.test(phoneNumber)) {
        alert('Please enter a valid phone number');
        return false;
    }
    
    // Validate age
    const age = parseInt(document.getElementById('userAge').value);
    if (age < 1 || age > 120) {
        alert('Please enter a valid age (1-120)');
        return false;
    }
    
    // Validate password
    const password = document.getElementById('password').value;
    if (password.length < 8) {
        alert('Password must be at least 8 characters long');
        return false;
    }
    
    // Check password match
    const confirmPassword = document.getElementById('confirmPassword').value;
    if (password !== confirmPassword) {
        alert('Passwords do not match');
        return false;
    }
    
    // Check terms agreement
    const terms = document.getElementById('terms');
    if (!terms.checked) {
        alert('Please agree to the Terms and Conditions');
        return false;
    }
    
    return true;
}

// Real-time validation helpers
function validatePhoneNumber(phoneNumber) {
    const phoneRegex = /^[0-9+\-\s\(\)]+$/;
    return phoneRegex.test(phoneNumber);
}

function validateAge(age) {
    const ageNum = parseInt(age);
    return ageNum >= 1 && ageNum <= 120;
}

function isPasswordStrong(password) {
    return password.length >= 8 && 
           /[A-Z]/.test(password) && 
           /[a-z]/.test(password) && 
           /[0-9]/.test(password);
}