document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    
    // Email validation
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const emailMessage = document.createElement('small');
    const passwordMessage = document.createElement('small');
    emailMessage.classList.add('error-msg');
    passwordMessage.classList.add('error-msg');

    emailInput.insertAdjacentElement('afterend', emailMessage);
    passwordInput.insertAdjacentElement('afterend', passwordMessage);

    emailInput.addEventListener('input', () => {
        const email = emailInput.value.trim();
        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        
        if (!emailPattern.test(email)) {
            emailMessage.textContent = 'Invalid email format.';
        } else {
            emailMessage.textContent = '';
            checkEmailExists(email);
        }
    });

    passwordInput.addEventListener('input', () => {
        const password = passwordInput.value.trim();
        const hasSymbol = /[!@#$%^&*(),.?":{}|<>]/.test(password);
        const hasNumber = /\d/.test(password);
        const hasUppercase = /[A-Z]/.test(password);
        const hasLowercase = /[a-z]/.test(password);

        if (password.length < 6) {
            passwordMessage.textContent = 'Password must be at least 6 characters.';
        } else if (!hasSymbol) {
            passwordMessage.textContent = 'Password must contain at least one symbol (!, @, #, etc.).';
        } else if (!hasNumber) {
            passwordMessage.textContent = 'Password must contain at least one number.';
        } else if (!hasUppercase) {
            passwordMessage.textContent = 'Password must contain at least one uppercase letter.';
        } else if (!hasLowercase) {
            passwordMessage.textContent = 'Password must contain at least one lowercase letter.';
        } else {
            passwordMessage.textContent = '';
        }
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault(); 

        const formData = new FormData(form);

        fetch('add-user.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                window.location.href = data.redirect; 
            } else {
                alert(data.message); 
            }
        })
        .catch(error => {
            console.error('Error during form submission:', error);
            alert('There was an error submitting the form.');
        });
    });

    function checkEmailExists(email) {
        fetch(`add-user.php?email=${encodeURIComponent(email)}`)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    emailMessage.textContent = 'This email is already registered.';
                } else {
                    emailMessage.textContent = '';
                }
            })
            .catch(error => {
                console.error('Error checking email existence:', error);
            });
    }
});
