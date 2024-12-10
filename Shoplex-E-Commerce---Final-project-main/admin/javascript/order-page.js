
document.querySelectorAll('.update-btn').forEach(button => {
    button.addEventListener('click', () => {
        const orderItemId = button.getAttribute('data-order-item-id');
        const selectedStatus = document.querySelector(`.status-dropdown[data-order-item-id="${orderItemId}"]`).value;

        const confirmUpdate = confirm("Are you sure you want to update the status?");
        if (!confirmUpdate) {
            return; 
        }

        
        fetch('other-php/update-order-status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                order_item_id: orderItemId,
                status_id: selectedStatus,
            }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Order status updated successfully.');
                    location.reload();
                } else {
                    alert('Failed to update order status: ' + data.message);
                }
            })
            .catch(error => alert('Error: ' + error));
    });
});
