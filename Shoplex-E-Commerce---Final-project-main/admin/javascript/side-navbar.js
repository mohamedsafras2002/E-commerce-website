// const toggleBtn = document.getElementById('toggle-btn');
// const sidebar = document.querySelector('.sidebar');
// const mainContent = document.querySelector('.main-content');

// // Toggle sidebar visibility
// toggleBtn.addEventListener('click', () => {
//     sidebar.classList.toggle('open'); // Slide in/out the sidebar
//     mainContent.classList.toggle('open'); // Shift content accordingly
// });


function toggleSidebar() {
    const sidebar = document.querySelector(".sidebar");
    sidebar.classList.toggle("collapsed");
}