// Toggle menu visibility for mobile view
function toggleMenu() {
    const menu = document.getElementById('navMenu');
    menu.classList.toggle('active');
}

function scrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    section.scrollIntoView({ behavior: 'smooth' });
}


// Mock function to check if the user is logged in
function isLoggedIn() {
    return localStorage.getItem('isLoggedIn') === 'true'; // Replace this with actual authentication check
}

// Navigate to the appropriate page based on login status
function handleProfileClick() {
    if (isLoggedIn()) {
        window.location.href = "profile.html"; // Redirect to profile page
    } else {
        window.location.href = "page1.html"; // Redirect to login page
    }
}

// Attach event listener to the Profile link in the navigation menu
document.querySelector('a[href="page1.html"]').addEventListener('click', (event) => {
    event.preventDefault(); // Prevent default navigation
    handleProfileClick();
});


// Simulate user login
function login() {
    localStorage.setItem('isLoggedIn', 'true');
    alert("Logged in successfully!");
}

// Simulate user logout
function logout() {
    localStorage.setItem('isLoggedIn', 'false');
    alert("Logged out successfully!");
}


