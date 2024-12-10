



document.addEventListener("DOMContentLoaded", () => {
    const normalBtn = document.getElementById("normalProductBtn");
    const biddingBtn = document.getElementById("biddingProductBtn");
    const tableHeaders = document.querySelector("#tableHeaders");
    const productsTable = document.querySelector("#productsTableBody");

    if (!tableHeaders || !productsTable) {
        console.error("Error: Required table elements not found.");
        return;
    }

    loadProducts("normal");
    setActiveButton(normalBtn, biddingBtn);

    // Define the event handlers as variables
    let normalBtnClickHandler = () => {
        loadProducts("normal");
        setActiveButton(normalBtn, biddingBtn);
    };

    let biddingBtnClickHandler = () => {
        loadProducts("bidding");
        setActiveButton(biddingBtn, normalBtn);
    };

    // Attach event listeners
    normalBtn.addEventListener("click", normalBtnClickHandler);
    biddingBtn.addEventListener("click", biddingBtnClickHandler);

    // Function to reset event listeners
    function resetEventListeners() {
        // Remove previous event listeners
        normalBtn.removeEventListener("click", normalBtnClickHandler);
        biddingBtn.removeEventListener("click", biddingBtnClickHandler);

        // Re-assign the event handlers
        normalBtnClickHandler = () => {
            loadProducts("normal");
            setActiveButton(normalBtn, biddingBtn);
        };

        biddingBtnClickHandler = () => {
            loadProducts("bidding");
            setActiveButton(biddingBtn, normalBtn);
        };

        // Re-add event listeners
        normalBtn.addEventListener("click", normalBtnClickHandler);
        biddingBtn.addEventListener("click", biddingBtnClickHandler);
    }

    function setActiveButton(activeBtn, inactiveBtn) {
        activeBtn.classList.add("active");
        inactiveBtn.classList.remove("active");
    }

    function loadProducts(type) {
        fetch(`other-php/fetch-products.php?type=${type}`)
            .then((response) => response.json())
            .then((data) => {
                if (data.error) {
                    alert(data.error);
                    return;
                }

                // console.log(data)
               
                tableHeaders.innerHTML = "";
                productsTable.innerHTML = "";

                
                if (type === "normal") {
                    tableHeaders.innerHTML = `
              <th>Product ID</th>
              <th>Name</th>
              <th>Description</th>
              <th>Category</th>
              <th>Price (Rs.)</th>
              <th>Discount (%)</th>
              <th>Stock</th>
              <th>Shipping Fee (Rs.)</th>
              <th>Created At</th>
              <th>Updated At</th>
              <th>Actions</th>
            `;
                } else if (type === "bidding") {
                    tableHeaders.innerHTML = `
              <th>Product ID</th>
              <th>Name</th>
              <th>Description</th>
              <th>Sub Category</th>
              <th>Stock</th>
              <th>Bid Starting Price (Rs.)</th>
              <th>Shipping Fee (Rs.)</th>
              <th>Bid Start Date</th>
              <th>Bid End Date</th>
              <th>Created At</th>
              <th>Updated At</th>
              <th>Actions</th>
            `;
                }

                data.forEach((product) => {
                    const row = document.createElement("tr");
                    if (product.bid_activate == 0) {
                        row.dataset.productType = "normal";
                    } else {
                        row.dataset.productType = "bidding";
                    }
                
                    if (type === "normal") {
                        row.innerHTML = `
                            <td>${product.product_id}</td>
                            <td>${product.product_name}</td>
                            <td class="description-container"><div class="description">${product.description}</div></td>
                            <td>${product.category_name}</td>
                            <td>${product.price}</td>
                            <td>${parseFloat(product.discount * 100).toFixed(2)}</td>
                            <td>${product.stock}</td>
                            <td>${product.shipping_fee}</td>
                            <td>${product.created_at}</td>
                            <td>${product.updated_at}</td>
                            <td>
                                <button class="action-buttons edit-btn" data-id="${product.product_id}">Edit</button>
                                <button class="action-buttons delete-btn" data-id="${product.product_id}">Delete</button>
                            </td>`;
                    } else if (type === "bidding") {
                        // Filter auction history for entries where winning_bidder_id is null
                        const activeAuction = product.auction_history.find(auction => auction.winning_bidder_id === null);
                        
                        let start_time = "Not Decided";
                        let end_time = "Not Decided";
                        
                        if (activeAuction) {
                            start_time = new Date(activeAuction.start_time).toLocaleDateString();
                            end_time = activeAuction.end_time ? new Date(activeAuction.end_time).toLocaleDateString() : 'Not Decided';
                        }
                
                        row.innerHTML = `
                            <td>${product.product_id}</td>
                            <td>${product.product_name}</td>
                            <td class="description-container"><div class="description">${product.description}</div></td>
                            <td>${product.category_name}</td>
                            <td>${product.stock}</td>
                            <td>${product.bid_starting_price}</td>
                            <td>${product.shipping_fee}</td>
                            <td>${start_time}</td>
                            <td>${end_time}</td>
                            <td>${product.created_at}</td>
                            <td>${product.updated_at}</td>
                            <td>
                                <button class="action-buttons edit-btn" data-id="${product.product_id}">Edit</button>
                                <button class="action-buttons delete-btn" data-id="${product.product_id}">Delete</button>
                            </td>`;
                    }
                
                    productsTable.appendChild(row);
                });
                
                document.querySelector("#productsTableBody").addEventListener("click", function (event) {
                    if (event.target.classList.contains("edit-btn")) {
                        handleEdit(event);
                    } else if (event.target.classList.contains("delete-btn")) {
                        handleDelete(event);
                    }
                });                
            })
            .catch((err) => {
                console.error("Error loading products:", err);
                alert("Failed to load products.");
            });
    }


    
    function handleEdit(event) {
        document.getElementById("updateSubmitBtn").style.display = "block";
        document.getElementById("addProductSubmitBtn").style.display = "none";
        const productId = event.target.dataset.id;
        document.querySelector('input[name="productId"]').value = productId;
        const row = event.target.closest("tr");
        
        

        const productName = row.cells[1].innerText;
        const description = row.cells[2].innerText;
        const category = row.cells[3].innerText;
        const stock = row.cells[6].innerText;
        const productType = row.dataset.productType;

        productImageLoad(productId, productType);

        document.getElementById("productTypeHidden").value = productType;

        // console.log("Product Type:", productType);

        // Populate the form with product data
        const productForm = document.getElementById("productForm");
        productForm.productName.value = productName;
        productForm.description.value = description;
        productForm.stock.value = stock;

        // Show/hide fields based on product type
        if (productType === "normal") {
            document.getElementById("normalProductFields").style.display = "block";
            document.getElementById("biddingProductFields").style.display = "none";
            const price = row.cells[4].innerText;
            productForm.price.value = price;
            const discount = row.cells[5].innerText;
            productForm.discount.value = discount;
            const shippingFee = row.cells[7].innerText;
            productForm.shippingFee.value = shippingFee;
        } else if (productType === "bidding") {
            document.getElementById("normalProductFields").style.display = "none";
            document.getElementById("biddingProductFields").style.display = "block";
            const stock = row.cells[4].innerText;
            productForm.stock.value = stock;

            const shippingFee = row.cells[6].innerText;
            productForm.shippingFee.value = shippingFee;

            const bidStartingPrice = row.cells[5].innerText;
            const bidStartDate = row.cells[7].innerText;
            const bidEndDate = row.cells[8].innerText;

            productForm.bidStartingPrice.value = bidStartingPrice;
            productForm.bidStartDate.value = formatDateToYYYYMMDD(bidStartDate);
            if (bidEndDate == null || bidEndDate == "Not Decided") {
                productForm.bidEndDate.value = "";
            } else {
                productForm.bidEndDate.value = formatDateToYYYYMMDD(bidEndDate);
            }
            
            
        }

        
        loadDropdowns(productId);

        document.querySelector(".product-type-selector").style.display = "none";

        document.getElementById("productPopup").classList.add("show");
    }

    function formatDateToYYYYMMDD(dateString) {
        let dateParts = dateString.split('/');
        let month = dateParts[0].padStart(2, '0'); 
        let day = dateParts[1].padStart(2, '0'); 
        let year = dateParts[2];

        return `${year}-${month}-${day}`;
    }

    // Function to load parent category and subcategory dropdowns
    function loadDropdowns(productId) {
        // Fetch data from the backend
        fetch(`other-php/fetch-category-data.php?productId=${productId}`)
            .then((response) => response.json())
            .then((data) => {
                // console.log("Fetched Data:", data);
                const parentCategoryDropdown = document.getElementById("parentCategory");
                const subCategoryDropdown = document.getElementById("subCategory");

                // Clear existing options
                parentCategoryDropdown.innerHTML = "<option value=''>Select Parent Category</option>";
                subCategoryDropdown.innerHTML = "<option value=''>Select Subcategory</option>";

                
                data.parentCategories.forEach((parentCategory) => {
                    const option = document.createElement("option");
                    option.value = parentCategory.category_id;
                    option.textContent = parentCategory.category_name;
                    if (parentCategory.category_id == data.parentCategoryId) {
                        option.selected = true; 
                    }
                    parentCategoryDropdown.appendChild(option);
                });

                data.subCategories.forEach((subCategory) => {
                    const option = document.createElement("option");
                    option.value = subCategory.category_id;
                    option.textContent = subCategory.category_name;
                    if (subCategory.category_id == data.subCategoryId) {
                        option.selected = true; 
                        subCategoryDropdown.disabled = false;
                    }
                    subCategoryDropdown.appendChild(option);
                });

            })
            .catch((error) => {
                console.error("Error loading dropdown data:", error);
                alert("Failed to load dropdown data.");
            });
    }



    

    function productImageLoad(productId, productType) {
        fetch(`other-php/fetch-products.php?type=${productType}`)
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then((data) => {
                if (data.length === 0) {
                    console.error("No products found.");
                    return;
                }
    
                const specificProduct = data.find((product) => product.product_id === productId);
    
                if (!specificProduct) {
                    console.error(`Product with ID ${productId} not found.`);
                    return;
                }
    
                // Loop through the product's images
                if (
                    specificProduct.product_images &&
                    Array.isArray(specificProduct.product_images) &&
                    specificProduct.product_images.length > 0
                ) {
                    specificProduct.product_images.forEach((picture_path) => {
                        if (picture_path && picture_path.trim() !== "") {
                            const imgSrc = `../${picture_path}`;
                            createImagePreview(imgSrc, imgSrc.split("/").pop());
                        }
                    });
                } else {
                    console.error("No product images found for product ID:", productId);
                }
            })
            .catch((error) => {
                console.error("Error loading images:", error);
                alert("Failed to load product images.");
            });
    }
});


function createImagePreview(imageSrc, imageName) {
    const imagePreviewContainer = document.getElementById("imagePreviewContainer");
    if (!imagePreviewContainer) {
        console.error("Image preview container not found.");
        return;
    }

    const existingPreview = Array.from(imagePreviewContainer.children).find(
        (child) => child.dataset.name === imageName
    );

    if (existingPreview) {
        console.warn(`Preview for the image "${imageName}" already exists.`);
        return;
    }

    const imagePreview = document.createElement("div");
    imagePreview.className = "image-preview";
    imagePreview.dataset.name = imageName; 

    const img = document.createElement("img");
    img.src = imageSrc;
    img.alt = imageName;

    const removeBtn = document.createElement("span");
    removeBtn.className = "remove-btn";
    removeBtn.innerHTML = "&times;";
    removeBtn.onclick = () => {
        imagePreview.remove();

        if (typeof uploadedFiles !== "undefined") {
            const index = uploadedFiles.findIndex((file) => file.name === imageName);
            if (index > -1) {
                uploadedFiles.splice(index, 1);
            }
        }
    };

    imagePreview.appendChild(img);
    imagePreview.appendChild(removeBtn);

    imagePreviewContainer.appendChild(imagePreview);
}




document.getElementById("addProductBtn").addEventListener("click", function () {
    document.getElementById("productPopup").classList.add("show");
});



function closeProductForm() {
    document.getElementById("productPopup").classList.remove("show");
    document.getElementById("updateSubmitBtn").style.display = "none";
    document.getElementById("addProductSubmitBtn").style.display = "block";
    
    const form = document.getElementById("productForm");
    form.reset();
    
    document.getElementById("subCategory").innerHTML = '<option value="">Select Sub Category</option>';
    document.getElementById("subCategory").disabled = true;
    
    document.getElementById("normalProductFields").style.display = "block";
    document.getElementById("biddingProductFields").style.display = "none";
    
    document.querySelector(".product-type-selector").style.display = "flex";

    // Clear product type button active state
    const productTypeButtons = document.querySelectorAll('.product-type-btn');
    productTypeButtons.forEach(btn => btn.classList.remove('active'));

    // Set default active button
    const activeButton = document.querySelector('.product-type-btn[data-type="normal"]');
    if (activeButton) {
        activeButton.classList.add('active');
    }

    // Clear image previews
    const imagePreviewContainer = document.getElementById("imagePreviewContainer");
    imagePreviewContainer.innerHTML = "";
}


function selectProductType(type) {
    const normalFields = document.getElementById("normalProductFields");
    const biddingFields = document.getElementById("biddingProductFields");
    const typeButtons = document.querySelectorAll(".product-type-btn");

    typeButtons.forEach((btn) => {
        btn.classList.remove("active");
        if (btn.dataset.type === type) {
            btn.classList.add("active");
        }
    });

    if (type === "normal") {
        normalFields.style.display = "block";
        biddingFields.style.display = "none";
    } else {
        normalFields.style.display = "none";
        biddingFields.style.display = "block";
    }
}

function updateSubCategories() {
    const parentCategory = document.getElementById("parentCategory").value;
    const subCategorySelect = document.getElementById("subCategory");

    subCategorySelect.innerHTML = '<option value="">Select Sub Category</option>';

    if (parentCategory) {
        subCategorySelect.disabled = false;
        parentCategories[parentCategory].forEach((subCat) => {
            const option = document.createElement("option");
            option.value = subCat.toLowerCase();
            option.textContent = subCat;
            subCategorySelect.appendChild(option);
        });
    } else {
        subCategorySelect.disabled = true;
    }
}

document.querySelector(".image-upload").addEventListener("click", function () {
    this.querySelector('input[type="file"]').click();
});

document
    .querySelector('.image-upload input[type="file"]')
    .addEventListener("change", function (e) {
        const files = e.target.files;
        this.parentElement.querySelector(
            "p"
        ).textContent = `${files.length} image(s) selected`;
    });


const uploadedFiles = [];
document.addEventListener("DOMContentLoaded", () => {
    const imageUploadContainer = document.getElementById("imageUploadContainer");
    const imageInput = document.getElementById("imageInput");
    const imagePreviewContainer = document.getElementById(
        "imagePreviewContainer"
    );
    // Array to store uploaded files

    // Handle clicking on the upload container
    imageUploadContainer.addEventListener("click", () => {
        imageInput.click();
    });

    // Handle file selection
    imageInput.addEventListener("change", () =>
        handleFileSelect(imageInput.files)
    );

    // Handle drag-and-drop functionality
    imageUploadContainer.addEventListener("dragover", (event) => {
        event.preventDefault();
        imageUploadContainer.classList.add("dragging");
    });

    imageUploadContainer.addEventListener("dragleave", () => {
        imageUploadContainer.classList.remove("dragging");
    });

    imageUploadContainer.addEventListener("drop", (event) => {
        event.preventDefault();
        imageUploadContainer.classList.remove("dragging");
        handleFileSelect(event.dataTransfer.files);
    });


    // Function to handle file selection
    function handleFileSelect(files) {
        Array.from(files).forEach((file) => {
            if (!file.type.startsWith("image/")) {
                alert("Only image files are allowed!");
                return;
            }

            // Avoid adding duplicate images based on name
            if (
                uploadedFiles.some((uploadedFile) => uploadedFile.name === file.name)
            ) {
                alert(`The file "${file.name}" is already added.`);
                return;
            }

            uploadedFiles.push(file);

            // Preview the image
            const reader = new FileReader();
            reader.onload = (event) => {
                createImagePreview(event.target.result, file.name);
            };
            reader.readAsDataURL(file);
        });

        imageInput.value = ""; // Reset input
    }



    // Create image preview with remove button
    function createImagePreview(imageSrc, imageName) {
        const imagePreview = document.createElement("div");
        imagePreview.className = "image-preview";

        const img = document.createElement("img");
        img.src = imageSrc;
        img.alt = imageName;

        const removeBtn = document.createElement("span");
        removeBtn.className = "remove-btn";
        removeBtn.innerHTML = "&times;";
        removeBtn.onclick = () => {
            // Remove the preview
            imagePreview.remove();

            // Remove the file from the uploadedFiles array
            const index = uploadedFiles.findIndex((file) => file.name === imageName);
            if (index > -1) {
                uploadedFiles.splice(index, 1);
            }
        };

        imagePreview.appendChild(img);
        imagePreview.appendChild(removeBtn);
        imagePreviewContainer.appendChild(imagePreview);
    }
});

document.addEventListener("DOMContentLoaded", function () {
    loadParentCategories();
});

function loadParentCategories() {
    const parentCategoryDropdown = document.getElementById("parentCategory");
    parentCategoryDropdown.innerHTML = '<option value="">Loading...</option>';

    fetch("other-php/fetchSubCategories.php")
        .then((response) => response.json())
        .then((data) => {
            parentCategoryDropdown.innerHTML =
                '<option value="">Select Parent Category</option>';
            data.forEach((category) => {
                const option = document.createElement("option");
                option.value = category.category_id;
                option.textContent = category.category_name;
                parentCategoryDropdown.appendChild(option);
            });
        })
        .catch((error) => {
            console.error("Error fetching parent categories:", error);
            parentCategoryDropdown.innerHTML =
                '<option value="">Error loading categories</option>';
        });
}

function fetchSubCategories(parentId) {
    const subCategoryDropdown = document.getElementById("subCategory");
    subCategoryDropdown.innerHTML = '<option value="">Loading...</option>';
    subCategoryDropdown.disabled = true;

    if (!parentId) {
        subCategoryDropdown.innerHTML =
            '<option value="">Select Sub Category</option>';
        return;
    }

    fetch(`other-php/fetchSubCategories.php?parent_id=${parentId}`)
        .then((response) => response.json())
        .then((data) => {
            subCategoryDropdown.innerHTML =
                '<option value="">Select Sub Category</option>';
            data.forEach((category) => {
                const option = document.createElement("option");
                option.value = category.category_id;
                option.textContent = category.category_name;
                subCategoryDropdown.appendChild(option);
            });
            subCategoryDropdown.disabled = false;
        })
        .catch((error) => {
            console.error("Error fetching subcategories:", error);
            subCategoryDropdown.innerHTML =
                '<option value="">Error loading subcategories</option>';
        });
}

document.addEventListener("DOMContentLoaded", function () {
    const productForm = document.getElementById("productForm");

    if (!productForm) {
        console.error("Product form not found in the DOM.");
        return;
    }

    productForm.addEventListener("submit", async function (event) {
        event.preventDefault();

        clearMessages();

        const productType = document.querySelector(".product-type-btn.active")?.getAttribute("data-type");
        const productName = this.querySelector('input[name="productName"]')?.value.trim();
        const description = this.querySelector('textarea[name="description"]')?.value.trim();
        const parentCategory = this.querySelector("#parentCategory")?.value.trim();
        const subCategory = this.querySelector("#subCategory")?.value.trim();
        const price = productType !== "bidding" ? this.querySelector('input[name="price"]')?.value.trim() : null;
        const discount = this.querySelector('input[name="discount"]')?.value.trim();
        const stock = this.querySelector('input[name="stock"]')?.value.trim();
        const shippingFee = this.querySelector('input[name="shippingFee"]')?.value.trim();

        const bidStartingPrice = productType === "bidding" ? this.querySelector('#biddingProductFields input[name="bidStartingPrice"]')?.value.trim() : null;
        const bidStartDate = productType === "bidding" ? this.querySelector('#biddingProductFields input[name="bidStartDate"]')?.value.trim() : null;
        const bidEndDate = productType === "bidding" ? this.querySelector('#biddingProductFields input[name="bidEndDate"]')?.value.trim() : null;


        const errors = [];
        if (!productName) errors.push("Product name is required.");
        if (!description) errors.push("Description is required.");
        if (!parentCategory) errors.push("Parent category is required.");
        if (!subCategory || subCategory === "Select Sub Category") errors.push("Subcategory is required.");
        if (productType !== "bidding" && (!price || isNaN(price))) errors.push("Price is required for non-bidding products.");
        if (!stock || isNaN(stock)) errors.push("Stock is required.");
        if (!shippingFee || isNaN(shippingFee)) errors.push("Shipping fee is required.");

        
        if (productType === "bidding") {
            if (!bidStartingPrice || isNaN(bidStartingPrice)) errors.push("Starting price is required for bidding products.");
            if (!bidStartDate) errors.push("Start date is required for bidding products.");
            if (!bidEndDate) errors.push("End date is required for bidding products.");
            if (bidStartDate && bidEndDate) {
                const startDate = new Date(bidStartDate);
                const endDate = new Date(bidEndDate);
                if (startDate >= endDate) errors.push("End date must be later than the start date for bidding products.");
            }
        }

        if (errors.length > 0) {
            displayMessage(errors[0], "#d9534f");
            return false;
        }


        const formData = new FormData();
        formData.append("productType", productType);
        formData.append("productName", productName);
        formData.append("description", description);
        formData.append("parentCategory", parentCategory);
        formData.append("subCategory", subCategory);
        if (productType !== "bidding") formData.append("price", price);
        formData.append("discount", discount);
        formData.append("stock", stock);
        formData.append("shippingFee", shippingFee);

        if (productType === "bidding") {
            formData.append("bidStartingPrice", bidStartingPrice);
            formData.append("bidStartDate", bidStartDate);
            formData.append("bidEndDate", bidEndDate);
        }

        // Add images to FormData
        if (Array.isArray(uploadedFiles) && uploadedFiles.length > 0) {
            for (const file of uploadedFiles) {
                formData.append("images[]", file);
            }
        }

        console.log("Fine")

        // Submit form data
        try {
            const response = await fetch("other-php/add-product-process.php", {
                method: "POST",
                body: formData,
            });
            console.log("Fine 2")

            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            console.log("Fine 3")
            const result = await response.json();
            console.log("Fine 4")
            if (result.status === "success") {
                displayMessage("Product added successfully!", "#5cb85c");
                setTimeout(() => location.reload(), 1000);
            } else {
                displayMessage(result.message || "Failed to add product", "#d9534f");
            }
        } catch (error) {
            console.error("Error submitting the form:", error);
            displayMessage("An error occurred while submitting the form.", "#d9534f");
        }
    });
});









document.addEventListener("DOMContentLoaded", function () {
    const productForm = document.getElementById("productForm");
    const updateSubmitBtn = document.getElementById("updateSubmitBtn");

    if (productForm) {
        // Function to handle form validation and preparation of data
        function handleFormSubmission() {
            // Clear previous messages (optional if using UI feedback elements)
            clearMessages();

            // Collect form data dynamically on each submission
            const productId = productForm.querySelector('input[name="productId"]')?.value.trim();
            
            const productType = productForm.querySelector('input[name="productType"]')?.value.trim();
            console.log(productType)
            const productName = productForm.querySelector('input[name="productName"]')?.value.trim();
            const description = productForm.querySelector('textarea[name="description"]')?.value.trim();
            const parentCategory = productForm.querySelector("#parentCategory")?.value.trim();
            const subCategory = productForm.querySelector("#subCategory")?.value.trim();
            const price = productType !== "bidding" ? productForm.querySelector('input[name="price"]')?.value.trim() : null;
            const discount = productForm.querySelector('input[name="discount"]')?.value.trim();
            const stock = productForm.querySelector('input[name="stock"]')?.value.trim();
            const shippingFee = productForm.querySelector('input[name="shippingFee"]')?.value.trim();

            // Bidding-specific fields
            const bidStartingPrice = productType === "bidding" ? productForm.querySelector('#biddingProductFields input[name="bidStartingPrice"]')?.value.trim() : null;
            const bidStartDate = productType === "bidding" ? productForm.querySelector('#biddingProductFields input[name="bidStartDate"]')?.value.trim() : null;
            const bidEndDate = productType === "bidding" ? productForm.querySelector('#biddingProductFields input[name="bidEndDate"]')?.value.trim() : null;
            console.log(productType)
            // Validate fields dynamically
            const errors = [];
            if (!productName) errors.push("Product name is required");
            if (!description) errors.push("Description is required");
            if (!parentCategory) errors.push("Parent category is required");
            if (!subCategory || subCategory === "Select Sub Category") errors.push("Subcategory is required");
            if (productType !== "bidding" && (!price || isNaN(price))) errors.push("Price is required for non-bidding products");
            if (!stock || isNaN(stock)) errors.push("Stock is required");
            if (!shippingFee || isNaN(shippingFee)) errors.push("Shipping fee is required");

            // Bidding validation
            if (productType === "bidding") {
                if (!bidStartingPrice || isNaN(bidStartingPrice)) {
                    errors.push("Starting price is required for bidding products");
                }
                if (!bidStartDate) {
                    errors.push("Start date is required for bidding products");
                }
                if (!bidEndDate) {
                    errors.push("End date is required for bidding products");
                }
                if (bidStartDate && bidEndDate) {
                    const startDate = new Date(bidStartDate);
                    const endDate = new Date(bidEndDate);
                    if (startDate >= endDate) {
                        errors.push("End date must be greater than the start date for bidding products");
                    }
                }
            }

            // If there are errors, display the first one
            if (errors.length > 0) {
                displayMessage(errors[0], "#d9534f");
                return false; // Don't proceed to form submission
            }

            // Proceed if no errors
            const formData = new FormData();
            formData.append("productId", productId); // Append the product ID for updating
            formData.append("productType", productType);
            formData.append("productName", productName);
            formData.append("description", description);
            formData.append("parentCategory", parentCategory);
            formData.append("subCategory", subCategory);
            if (productType !== "bidding") formData.append("price", price);
            formData.append("discount", discount);
            formData.append("stock", stock);
            formData.append("shippingFee", shippingFee);

            if (productType === "bidding") {
                formData.append("bidStartingPrice", bidStartingPrice);
                formData.append("bidStartDate", bidStartDate);
                formData.append("bidEndDate", bidEndDate);
            }

            // Add images if uploaded
            const imageFiles = uploadedFiles; // Use the uploadedFiles array, not the input files
            if (imageFiles.length > 0) {
                for (const file of imageFiles) {
                    formData.append("images[]", file);
                }
            }

            return formData;
        }


        // When the update button is clicked, trigger form submission
        updateSubmitBtn.addEventListener("click", async function (event) {
            event.preventDefault(); // Prevent default form submission

            // Get the form data
            const formData = handleFormSubmission();
            if (!formData) return; // If validation failed, don't proceed with submission

            try {
                const response = await fetch("other-php/update-product-process.php", { // Update endpoint
                    method: "POST",
                    body: formData,
                });

                const result = await response.json();
                console.log(result);

                if (response.ok && result.status === "success") {
                    displayMessage("Product updated successfully!", "#5cb85c");
                    closeProductForm();
                    setTimeout(() => {
                        location.reload(); // Reload to reflect the changes
                    }, 1000);
                } else {
                    displayMessage(result.message || "Failed to update product", "#d9534f");
                }
            } catch (error) {
                console.error("Error submitting the form:", error);
                displayMessage("An error occurred while submitting the form.", "#d9534f");
            }
        });
    } else {
        console.error("Product form not found in the DOM.");
    }
});



document.addEventListener("DOMContentLoaded", function () {
    const productTable = document.getElementById("productsTable");

    productTable.addEventListener("click", async function (event) {
        if (event.target.classList.contains("delete-btn")) {
            const productId = event.target.dataset.id;

            // Confirm deletion
            const confirmDelete = confirm("Are you sure you want to delete this product?");
            if (!confirmDelete) return;

            try {
                const response = await fetch(`other-php/delete-product.php`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({ productId }),
                });

                const result = await response.json();

                if (response.ok && result.status === "success") {
                    displayMessage("Product deleted successfully!", "#5cb85c");
                    
                    setTimeout(() => {
                        
                    }, 1000);
                    const row = event.target.closest("tr");
                    row.remove();
                } else {
                    alert(result.message || "Failed to delete the product.");
                }
            } catch (error) {
                console.error("Error deleting the product:", error);
                alert("An error occurred while trying to delete the product.");
            }
        }
    });
});








function clearMessages() {
    const messageBox = document.getElementById("messageBox");
    if (messageBox) {
        messageBox.textContent = "";
        messageBox.classList.remove("show");
    }
}

function displayMessage(message, backgroundColor) {
    const successMessage = document.getElementById("success-message");
    successMessage.classList.add("show");
    successMessage.innerText = message;
    successMessage.style.backgroundColor = backgroundColor;
    setTimeout(() => {
        successMessage.classList.remove("show");
    }, 3000);
}
