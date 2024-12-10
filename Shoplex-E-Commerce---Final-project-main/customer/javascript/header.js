//Search by image popup animation and transition

// const uploadBox = document.getElementById('uploadBox');
// const imageSearchButton = document.querySelector('.image-search-button');

// imageSearchButton.addEventListener('mouseover', function() {
//     uploadBox.style.display = 'block';   
// });

// imageSearchButton.addEventListener('mouseout', function() {
//     uploadBox.style.display = 'none';   
// });



//Profile poup animation and transition

const profilePopup = document.getElementById('profilePopup');
const profileButton = document.getElementById('profileButton');

profileButton.addEventListener('mouseover', function() {
        profilePopup.style.display = 'block';   
});

profileButton.addEventListener('mouseout', function() {
        profilePopup.style.display = 'none'; 
});



//Side navbar animation and transition

const sideNavBar = document.getElementById('sideNavBar');
const hamburgerButton = document.querySelector('.hamburger-button');
const closeBtn = document.getElementById('closeBtn');

hamburgerButton.addEventListener('click', function() {
    sideNavBar.classList.add('open');
    document.querySelector(".overlay").style.display = "block";
});

closeBtn.addEventListener('click', function() {
    sideNavBar.classList.remove('open');   
    document.querySelector(".overlay").style.display = "none";
});

window.addEventListener('click', function(event) {
    if (!sideNavBar.contains(event.target) && !hamburgerButton.contains(event.target)) {
        sideNavBar.classList.remove('open');
        document.querySelector(".overlay").style.display = "none";
    }
});


let lastScrollTop = 0; 
const navbar = document.querySelector('.nav-bar');

window.addEventListener('scroll', function() {
    const currentScrollTop = window.scrollY; 

    if (currentScrollTop > lastScrollTop && currentScrollTop > 60) { 
        navbar.classList.add('hide'); 
        navbar.classList.add('scroll-down'); 
    } else if (currentScrollTop < lastScrollTop) {
        navbar.classList.remove('hide'); 
        navbar.classList.remove('scroll-down'); 
    }

    lastScrollTop = currentScrollTop <= 0 ? 0 : currentScrollTop; 
});


document.addEventListener("DOMContentLoaded", function() {
    const categoryButton = document.getElementById("category-button");
    const categoryDropdown = document.getElementById("category-dropdown");
    const categoryItems = document.querySelectorAll(".category-item");

    // Check if categoryButton and categoryDropdown exist
    if (!categoryButton || !categoryDropdown) {
        console.error("Category button or dropdown not found.");
        return; // Exit if elements are not found
    }

    // Toggle main category dropdown
    categoryButton.addEventListener("click", function() {
        categoryDropdown.classList.toggle("show");
    });

    // Toggle subcategory dropdowns
    categoryItems.forEach(item => {
        const subcategoryDropdown = item.querySelector(".subcategory-dropdown");
        
        // Ensure the subcategory dropdown exists
        if (!subcategoryDropdown) {
            console.error("Subcategory dropdown not found for item: ", item);
            return; // Exit if subcategory dropdown is not found
        }

        item.addEventListener("click", function(event) {
            // Prevent event from bubbling up to the document click handler
            event.stopPropagation();

            // Toggle the associated subcategory dropdown
            subcategoryDropdown.classList.toggle("show");

            // Hide other subcategory dropdowns if needed
            document.querySelectorAll(".subcategory-dropdown").forEach(sub => {
                if (sub !== subcategoryDropdown) {
                    sub.classList.remove("show");
                }
            });
        });
    });

    // Close the dropdowns when clicking outside
    document.addEventListener("click", function(event) {
        if (!categoryButton.contains(event.target) && !categoryDropdown.contains(event.target)) {
            categoryDropdown.classList.remove("show");
            // Also hide all subcategory dropdowns
            document.querySelectorAll(".subcategory-dropdown").forEach(sub => {
                sub.classList.remove("show");
            });
        }
    });
});




document.addEventListener("DOMContentLoaded", function() {
    const dropdownToggle = document.querySelector("#custom-category-dropdown .custom-dropdown-toggle");
    const dropdownContent = document.querySelector("#custom-category-dropdown .custom-dropdown-content");
    const parentCategories = document.querySelectorAll(".custom-parent-category");

    // Toggle dropdown visibility
    dropdownToggle.addEventListener("click", function() {
        dropdownContent.style.display = dropdownContent.style.display === "block" ? "none" : "block";
    });

    // Handle parent category click to show/hide subcategories
    parentCategories.forEach(category => {
        category.addEventListener("click", function(event) {
            event.stopPropagation(); // Prevent click event from bubbling to the dropdown toggle

            const subcategoryContent = category.nextElementSibling;

            if (subcategoryContent) {
                subcategoryContent.style.display = subcategoryContent.style.display === "none" || subcategoryContent.style.display === "" ? "block" : "none";
            }
        });
    });

    // Close the dropdown if clicking outside
    document.addEventListener("click", function(event) {
        if (!event.target.closest('#custom-category-dropdown')) {
            dropdownContent.style.display = 'none'; // Hide dropdown if clicked outside
        }
    });
});





