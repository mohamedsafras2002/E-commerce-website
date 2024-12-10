document.addEventListener("DOMContentLoaded", () => {
    // View More Button
    document.querySelectorAll(".view-more-btn").forEach(button => {
        button.addEventListener("click", async function () {
            const auctionId = this.dataset.auctionId;

            // Fetch Bidding Records via AJAX
            const response = await fetch(`other-php/fetch-bidding-records.php?auction_id=${auctionId}`);
            const data = await response.json();

            const tableBody = document.getElementById("biddingDetailsBody");
            tableBody.innerHTML = ""; // Clear existing data
            const titleHead = document.getElementById("title-head");
            titleHead.innerHTML = `Bidding Records for Auction ID: ${auctionId}`;
            if (data.length > 0) {
                data.forEach(record => {
                    tableBody.innerHTML += `
                        <tr>
                            <td>${record.bid_id}</td>
                            <td>${record.name}</td>
                            <td>${record.bid_amount}</td>
                            <td>${record.bid_time}</td>
                        </tr>`;
                });
            } else {
                tableBody.innerHTML = "<tr><td colspan='4'>No bidding records found.</td></tr>";
            }

            // Show the popup
            document.getElementById("biddingDetailsPopup").style.display = "flex";
        });
    });

    document.querySelectorAll(".end-auction-btn").forEach(button => {
        button.addEventListener("click", async function () {
            const auctionId = this.dataset.auctionId;
            const productId = this.dataset.productId;
    
            if (!auctionId || !productId) {
                alert("Missing auction or product ID.");
                return;
            }
    
            if (confirm("Are you sure you want to end this auction?")) {
                try {
                    const response = await fetch(`other-php/end-auction.php?auction_id=${auctionId}&product_id=${productId}`);
                    const result = await response.json();  // Expecting JSON response
    
                    if (result.success) {
                        alert(result.message);  // Show success message from PHP response
                        location.reload();  // Optionally reload the page or update the DOM dynamically
                    } else {
                        alert(result.message);  // Show failure message from PHP response (auction hasn't started yet)
                    }
                } catch (error) {
                    console.error("Error:", error);
                    alert("An error occurred while ending the auction.");
                }
            }
        });
    });
    



});

function closeModal() {
    document.getElementById("biddingDetailsPopup").style.display = "none";
}

