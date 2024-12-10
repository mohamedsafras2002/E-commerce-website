// Function to show the modal
function confirmDelivered(orderItemId) {
    const userConfirmed = confirm("Are you sure you want to confirm delivery for this item?");
    if (!userConfirmed) return;

    // Simulate backend request to confirm delivery
    fetch('other-php/confirm-delivery.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ order_item_id: orderItemId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hide the button and show the modal
            const button = document.getElementById(`button-${orderItemId}`);
            if (button) button.style.display = 'none';

            const modal = document.getElementById('reviewModal');
            modal.classList.add('show');

            // Pre-fill form with order item ID (optional)
            document.getElementById('reviewForm').onsubmit = function(event) {
                event.preventDefault();
                submitReview(orderItemId);
            };
        } else {
            alert('Failed to confirm delivery. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

// Close the modal when the close button is clicked
document.getElementById('closeModal').onclick = function() {
    const modal = document.getElementById('reviewModal');
    modal.classList.remove('show');
    window.location.reload();
};

// Function to handle review submission
function submitReview(orderItemId) {
    const rating = document.getElementById('rating').value;
    const review = document.getElementById('review').value;

    if (!rating || !review) {
        alert('Please complete all fields.');
        return;
    }

    // Send the review data to the backend
    fetch('other-php/submit-review.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ order_item_id: orderItemId, rating, review })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Review submitted successfully!');
            const modal = document.getElementById('reviewModal');
            modal.classList.remove('show');
            window.location.reload();
        } else {
            alert('Failed to submit review. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}
