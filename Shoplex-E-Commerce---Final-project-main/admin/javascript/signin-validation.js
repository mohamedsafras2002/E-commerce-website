import { showSpinner, hideSpinner } from './spinner.js';

// Function to toggle password visibility
function togglePassword(id) {
    const passwordInput = document.getElementById(id);
    const eyeIcon = passwordInput.nextElementSibling.querySelector('i');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('toggle-password').addEventListener('click', () => {
        togglePassword('password'); 
    });
});

// Email Validation
function validateEmail() {
    const email = document.getElementById('email').value.trim();
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    const message = emailPattern.test(email) ? "" : "Invalid email address";
    document.getElementById('email-msg').textContent = message; 
    return message === '';
}

// Password Validation
function validatePassword() {
    const password = document.getElementById('password').value.trim();
    const message = password === '' ? 'Password is required' : '';
    document.getElementById('psw-msg').textContent = message; 
    return message === '';
}

// Event listeners for input validation
document.getElementById('email').addEventListener('input', validateEmail);
document.getElementById('password').addEventListener('input', validatePassword);

// Handle Form Submission
document.getElementById('signin-form').addEventListener('submit', function (event) {
    event.preventDefault();

    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();

    if (email === '' && password === '') {
        document.getElementById('email-msg').textContent = 'Email is required';
        document.getElementById('psw-msg').textContent = 'Password is required';
        hideSpinner();
        return;
    } else if (email === '') {
        document.getElementById('email-msg').textContent = 'Email is required';
        hideSpinner();
        return;
    } else if (password === '') {
        document.getElementById('psw-msg').textContent = 'Password is required';
        hideSpinner();
        return;
    }

    const isEmailValid = validateEmail();

    if (isEmailValid) {
        showSpinner();

        document.getElementById('email-msg').textContent = '';
        document.getElementById('psw-msg').textContent = '';

        fetch('signin-process.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                hideSpinner();

                if (data.email_exists) {
                    if (data.success) {
                        localStorage.setItem('user_id', data.user_id);
                        localStorage.setItem('user_name', data.user_name);

                        // Redirect based on user type
                        if (data.is_admin) {
                            window.location.href = 'dashboard-page.php'; // Redirect for admin
                        } else {
                            window.location.href = 'index.php'; // Redirect for non-admin users
                        }
                    } else {
                        document.getElementById('psw-msg').textContent = data.message || 'Incorrect password.';
                    }
                } else {
                    document.getElementById('email-msg').textContent = data.message || 'Email not found.';
                }
            })
            .catch(error => {
                hideSpinner();
                console.error('Error:', error);
                alert('An error occurred while processing your request. Please try again.');
            });
    } else {
        hideSpinner();
    }
});


// Check login status on page load
document.addEventListener('DOMContentLoaded', function() {
    const userName = localStorage.getItem('user_name');

    if (userName) {
        document.getElementById('user-info').style.display = 'block'; 
        document.getElementById('logout-btn').style.display = 'block'; 
        document.getElementById('user-name-display').textContent = `Welcome, ${userName}`;
    } else {
        document.getElementById('user-info').style.display = 'none'; 
        document.getElementById('logout-btn').style.display = 'none'; 
    }
});
