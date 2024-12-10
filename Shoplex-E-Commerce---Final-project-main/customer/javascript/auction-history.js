const auctionContainer = document.getElementById('auctionContainer');
        const userId = 1; 

        fetch('other-php/get-auction-details.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const auctions = data.auctions;

                    auctions.forEach(auction => {
                        const isOngoing = !auction.end_time || new Date(auction.end_time) > new Date();
                        const statusText = isOngoing ? 'Ongoing' : 'Ended';
                        const statusClass = isOngoing ? 'status' : 'status ended';

                        const card = document.createElement('div');
                        card.className = 'auction-card';

                        const auctionDetails = document.createElement('div');
                        auctionDetails.className = 'auction-details';

                        let resultText = '';
                        if (!isOngoing) {
                            if (auction.isWinner) {
                                resultText = '<p><strong>Result:</strong> <span style="color: green; font-weight: bold;">You Won!</span></p>';
                            } else {
                                resultText = '<p><strong>Result:</strong> <span style="color: red; font-weight: bold;">You Lost.</span></p>';
                            }
                        }

                        auctionDetails.innerHTML = `
                            <h3>Auction ID: ${auction.auction_id}</h3>
                            <h3>${auction.product_name}</h3>
                            <p>${auction.description}</p>
                            <p><strong>Starting Bid:</strong> ${auction.starting_bid}</p>
                            <p><strong>Current Highest Bid:</strong> ${auction.current_highest_bid || 'No Bids Yet'}</p>
                            <p><strong>Auction Ending Date:</strong> ${auction.end_time ? new Date(auction.end_time).toLocaleDateString() : 'Not Available'}</p>
                            <p><strong>Ending Bid:</strong> ${auction.ending_bid || 'Not Decided'}</p>
                            <p><strong>Status:</strong> <span class="${statusClass}">${statusText}</span></p>
                            ${resultText}
                        `;

                        // Right: Bidding records and checkout button
                        const biddingRecords = document.createElement('div');
                        biddingRecords.className = 'bidding-records';

                        if (auction.bids.length > 0) {
                            let tableHTML = `
                                <h4>Your Bidding Records</h4>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Bid Amount</th>
                                            <th>Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                            `;
                            auction.bids.forEach(bid => {
                                tableHTML += `
                                    <tr>
                                        <td>${bid.bid_amount}</td>
                                        <td>${new Date(bid.bid_time).toLocaleString()}</td>
                                    </tr>
                                `;
                            });
                            tableHTML += '</tbody></table>';
                            biddingRecords.innerHTML = tableHTML;
                        } else {
                            biddingRecords.innerHTML = '<p>No bids placed in this auction.</p>';
                        }

                        // Add Checkout Button
                        const checkoutButton = document.createElement('button');
                        checkoutButton.className = 'checkout-btn disabled';
                        checkoutButton.innerText = 'Checkout';
                        checkoutButton.disabled = true;

                        if (!isOngoing && auction.isWinner) {
                            // Fetch purchase status
                            fetch(`other-php/check-won-auction-purchase-status.php?auction_id=${auction.auction_id}&product_id=${auction.product_id}`)
                                .then(response => response.json())
                                .then(purchaseData => {
                                    if (purchaseData.success) {
                                        if (!purchaseData.purchased) {
                                            checkoutButton.classList.remove('disabled');
                                            checkoutButton.disabled = false;
                        
                                            checkoutButton.addEventListener('click', () => {
                                                // Ask for confirmation
                                                if (confirm("Are you sure you want to check out this product?")) {
                                                    // Perform checkout
                                                    fetch(`other-php/checkout-won-bid.php?auction_id=${auction.auction_id}&product_id=${auction.product_id}`)
                                                        .then(response => response.json())
                                                        .then(result => {
                                                            if (result.success) {
                                                                alert('Checkout successful!');
                                                                checkoutButton.classList.add('disabled');
                                                                checkoutButton.disabled = true;
                                                                checkoutButton.innerText = 'Checked Out';
                                                            } else {
                                                                alert(`Checkout failed: ${result.message}`);
                                                            }
                                                        })
                                                        .catch(error => alert('Error during checkout: ' + error.message));
                                                }
                                            });
                                        } else {
                                            checkoutButton.innerText = 'Already Checked Out';
                                        }
                                    } else {
                                        alert(`Error checking purchase status: ${purchaseData.message}`);
                                    }
                                })
                                .catch(error => alert('Error checking purchase status: ' + error.message));
                        }
                        

                        biddingRecords.appendChild(checkoutButton);

                        // Append details and records to card
                        card.appendChild(auctionDetails);
                        card.appendChild(biddingRecords);

                        // Append card to container
                        auctionContainer.appendChild(card);
                    });
                } else {
                    auctionContainer.innerHTML = `<p>${data.message}</p>`;
                }
            })
            .catch(error => console.error('Error fetching auctions:', error));