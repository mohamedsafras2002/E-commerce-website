document.addEventListener('DOMContentLoaded', function () {
    const messageForm = document.querySelector('.message-form form');
    const sendButton = messageForm.querySelector('.send-btn');
    const messageTextarea = messageForm.querySelector('textarea');

    messageForm.addEventListener('submit', function (event) {
        event.preventDefault();

        const messageContent = messageTextarea.value.trim();
        if (messageContent === '') {
            alert('Please enter a message before sending.');
            return;
        }

        sendButton.disabled = true;
        sendButton.textContent = 'Sending...';

        const formData = new FormData();
        formData.append('message', messageContent);

        fetch('other-php/send-message.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageTextarea.value = '';
                alert('Message sent successfully!');

                window.location.reload(); 
            } else {

                alert('Error sending message. Please try again.');
            }
            // Re-enable the button
            sendButton.disabled = false;
            sendButton.textContent = 'Send Message';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error sending message. Please try again.');
            sendButton.disabled = false;
            sendButton.textContent = 'Send Message';
        });
    });
});
