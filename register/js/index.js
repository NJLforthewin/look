// Register page specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
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

    // Form submission handling
    const registerForm = document.querySelector('form');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const submitBtn = registerForm.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="loading-spinner"></span> Creating Account...';
            }
        });
    }
});

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

// Password strength checker
function checkPasswordStrength() {
    const password = document.getElementById('password').value;
    const strengthDiv = document.getElementById('passwordStrength');
    
    if (!strengthDiv) return;
    
    if (password.length === 0) {
        strengthDiv.textContent = '';
        return;
    }
    
    let strength = 0;
    let feedback = [];
    
    // Length check
    if (password.length >= 8) strength++;
    else feedback.push('at least 8 characters');
    
    // Uppercase check
    if (/[A-Z]/.test(password)) strength++;
    else feedback.push('uppercase letter');
    
    // Lowercase check
    if (/[a-z]/.test(password)) strength++;
    else feedback.push('lowercase letter');
    
    // Number check
    if (/[0-9]/.test(password)) strength++;
    else feedback.push('number');
    
    // Special character check
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    else feedback.push('special character');
    
    // Update display
    if (strength < 2) {
        strengthDiv.className = 'password-strength weak';
        strengthDiv.textContent = 'Weak - Add: ' + feedback.slice(0, 2).join(', ');
    } else if (strength < 4) {
        strengthDiv.className = 'password-strength medium';
        strengthDiv.textContent = 'Medium - Consider adding: ' + feedback.slice(0, 1).join(', ');
    } else {
        strengthDiv.className = 'password-strength strong';
        strengthDiv.textContent = 'Strong password!';
    }
}

// Password match checker
function checkPasswordMatch() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const matchDiv = document.getElementById('passwordMatch');
    
    if (!matchDiv) return;
    
    if (confirmPassword.length === 0) {
        matchDiv.textContent = '';
        return;
    }
    
    if (password === confirmPassword) {
        matchDiv.className = 'password-match match';
        matchDiv.textContent = '✓ Passwords match';
    } else {
        matchDiv.className = 'password-match no-match';
        matchDiv.textContent = '✗ Passwords do not match';
    }
}