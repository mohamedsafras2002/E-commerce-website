// Get all the slides, previous button, and next button
const slides = document.querySelectorAll('.slide');
const prevButton = document.querySelector('.arrow-back');
const nextButton = document.querySelector('.arrow-forward');
let currentSlideIndex = 0;

// Function to show a specific slide
function showSlide(index) {
    const slideshowImageBox = document.querySelector('.slideshow-image-box');

    slideshowImageBox.style.transform = `translateX(-${index * 100}%)`;
}

// Function to go to the next slide
function nextSlide() {
    currentSlideIndex = (currentSlideIndex + 1) % slides.length;
    showSlide(currentSlideIndex);
}

// Function to go to the previous slide
function prevSlide() {
    currentSlideIndex = (currentSlideIndex - 1 + slides.length) % slides.length;
    showSlide(currentSlideIndex);
}

nextButton.addEventListener('click', nextSlide);
prevButton.addEventListener('click', prevSlide);

setInterval(nextSlide, 5000);

showSlide(currentSlideIndex);



