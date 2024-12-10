import { showSpinner, hideSpinner } from './spinner.js';

let emailValid = false;
let initialCheck = false;

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
        togglePassword('password'); // Toggle only the password field
    });

    document.getElementById('toggle-cPassword').addEventListener('click', () => {
        togglePassword('cPassword'); // Toggle only the confirm password field
    });
});

// Function to validate password requirements
function validatePassword() {
    const password = document.getElementById('password').value.trim();
    const passwordError = document.getElementById('psw-msg');

    const lengthRequirement = password.length >= 6;
    const lowerCaseRequirement = /[a-z]/.test(password);
    const upperCaseRequirement = /[A-Z]/.test(password);
    const numberRequirement = /\d/.test(password);
    const specialCharRequirement = /[\W_]/.test(password);

    if (!lengthRequirement) {
        passwordError.innerHTML = 'Password must be at least 6 characters';
    } else if (!lowerCaseRequirement) {
        passwordError.innerHTML = 'Password must include at least one lowercase letter';
    } else if (!upperCaseRequirement) {
        passwordError.innerHTML = 'Password must include at least one uppercase letter';
    } else if (!numberRequirement) {
        passwordError.innerHTML = 'Password must include at least one number';
    } else if (!specialCharRequirement) {
        passwordError.innerHTML = 'Password must include at least one special character';
    } else {
        passwordError.innerHTML = '';
    }
}

// Function to validate email with callback for asynchronous check
function validateEmail(callback) {
    const email = document.getElementById('email').value.trim();
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    const emailError = document.getElementById('email-msg');

    if (email === "") {
        emailError.innerHTML = "Email is required";
        emailValid = false;
        if (callback) callback();
    } else if (!emailPattern.test(email)) {
        emailError.innerHTML = "Invalid email address";
        emailValid = false;
        if (callback) callback();
    } else {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'register-page.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.exists) {
                    emailError.innerHTML = 'Email already exists.';
                    emailValid = false;
                } else {
                    emailError.innerHTML = '';
                    emailValid = true;
                }
                if (callback) callback();
            }
        };
        xhr.send('check_email=' + encodeURIComponent(email));
    }
}

// Live validation for form fields
document.getElementById('fName').addEventListener('input', function () {
    const fname = this.value.trim();
    document.getElementById('fName-msg').innerHTML = fname === "" ? "First name is required" : "";
});

document.getElementById('lName').addEventListener('input', function () {
    const lname = this.value.trim();
    document.getElementById('lName-msg').innerHTML = lname === "" ? "Last name is required" : "";
});

document.getElementById('email').addEventListener('input', function () {
    validateEmail();
});

document.getElementById('password').addEventListener('input', function () {
    validatePassword();
});

document.getElementById('cPassword').addEventListener('input', function () {
    const confirmPassword = this.value.trim();
    const password = document.getElementById('password').value.trim();
    document.getElementById('cPsw-msg').innerHTML = confirmPassword !== password ? "Passwords do not match" : "";
});

// Handle form submission
document.getElementById('register-form').addEventListener('submit', function (event) {
    event.preventDefault();

    const fname = document.getElementById('fName').value.trim();
    const lname = document.getElementById('lName').value.trim();
    const password = document.getElementById('password').value.trim();
    const confirmPassword = document.getElementById('cPassword').value.trim();

    const errors = {
        fName: fname === '' ? 'First name is required' : '',
        lName: lname === '' ? 'Last name is required' : '',
        password: password.length < 6 ? 'Password must be at least 6 characters' : '',
        cPassword: confirmPassword !== password ? 'Passwords do not match' : ''
    };

    document.getElementById('fName-msg').innerHTML = errors.fName;
    document.getElementById('lName-msg').innerHTML = errors.lName;
    document.getElementById('psw-msg').innerHTML = errors.password;
    document.getElementById('cPsw-msg').innerHTML = errors.cPassword;

    initialCheck = true;

    const hasErrors = Object.values(errors).some(error => error !== '');

    // Wait for email validation to complete
    validateEmail(function () {
        if (hasErrors || !emailValid) {
            return;
        }

        // Show spinner
        showSpinner(); // Call function from spinner.js to show the spinner

        // Redirect directly on successful registration
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'register-page.php', true);
        xhr.onload = function () {
            // Hide spinner once the request is complete
            hideSpinner(); // Call function from spinner.js to hide the spinner
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    window.location.href = 'signin-page.php'; 
                }
            }
        };

        const formData = new FormData(document.getElementById('register-form'));
        xhr.send(formData);
    });
});
