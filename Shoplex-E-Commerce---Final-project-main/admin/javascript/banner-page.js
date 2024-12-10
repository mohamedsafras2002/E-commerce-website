function hideMessage(messageId) {
    setTimeout(function() {
        var message = document.getElementById(messageId);
        if (message) {
            message.style.display = 'none';  // Hide the message
        }
    }, 5000);  // Hide the message after 5 seconds
}

// Automatically hide the error or success message after 5 seconds if they exist
if (document.getElementById('error-message')) {
    hideMessage('error-message');
}
if (document.getElementById('success-message')) {
    hideMessage('success-message');
}