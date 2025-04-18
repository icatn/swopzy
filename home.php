<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="home.css">
    <title>Home | Swopzy</title>
</head>
<body>

<div class="wrapper">
    <nav class="nav">
        <div class="nav-logo">
            <img src="logo-swopzy.png" alt="Swopzy Logo">
        </div>
        <div class="nav-menu" id="navMenu">
            <ul>
                <li><a href="logout.php" class="link">Logout</a></li>
                <li><a href="home.php" class="link active">Home</a></li>
                <li><a href="profile.php" class="link">Profile</a></li>  
                <li><a href="sell.php" class="link">Add_Product</a></li>
                <li><a href="#" class="link" onclick="scrollToSection('contact')">Contact</a></li>
                <li><a href="#" class="link" onclick="scrollToSection('about')">About Us</a></li>
            </ul>
        </div>
        <div class="nav-menu-btn">
            <i class="bx bx-menu" onclick="toggleMenu()"></i>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-text">
            <h1>Welcome to Swopzy</h1>
            <p>Swopzy is designed to be a comprehensive solution for individuals looking to buy or sell products online</p>
            <button onclick="scrollToSection('categories')" class="btn">explore more</button>
        </div>
    </section>

    <section id="categories" class="categories-section">
        <h2>Categories</h2>
        <div class="categories-container">
            <div class="category-card">
                <a href="electronics.php">
                    <img src="electronics.png" alt="Category 1">
                    <p>Electronics</p>
                </a>
            </div>
            <div class="category-card">
                <a href="fashion.php">
                    <img src="fashion.jpg" alt="Category 2">
                    <p>Fashion</p>
                </a>
            </div>
            <div class="category-card">
                <a href="living.php">
                    <img src="home&living.jpg" alt="Category 3">
                    <p>Home & Living</p>
                </a>
            </div>
            <div class="category-card">
                <a href="sports.php">
                    <img src="sports.jpg" alt="Category 4">
                    <p>Sports</p>
                </a>
            </div>
            <div class="category-card">
                <a href="books.php">
                    <img src="books.jpg" alt="Category 5">
                    <p>Books</p>
                </a>
            </div>
            <div class="category-card">
                <a href="cars.php">
                    <img src="cars.jpg" alt="Category 5">
                    <p>Cars</p>
                </a>
            </div>
            <div class="category-card">
                <a href="job.php">
                    <img src="job.jpg" alt="Category 6">
                    <p>job application</p>
                </a>
            </div>
            <div class="category-card">
                <a href="travel.php">
                    <img src="travel.jpg" alt="Category 7">
                    <p>travel and tourism</p>
                </a>
            </div>
            <div class="category-card">
                <a href="health.php">
                    <img src="health.jpg" alt="Category 8">
                    <p>health</p>
                </a>
            </div>
            <div class="category-card">
                <a href="gardening.php">
                    <img src="gardening.jpg" alt="Category 9">
                    <p>gardening and farming</p>
                </a>
            </div>
            <div class="category-card">
                <a href="education.php">
                    <img src="education.jpg" alt="Category 10">
                    <p>education</p>
                </a>
            </div>
            <div class="category-card">
                <a href="kids.php">
                    <img src="kids.jpg" alt="Category 11">
                    <p>kids clothes</p>
                </a>
            </div>
            <div class="category-card">
                <a href="women.php">
                    <img src="women.jpg" alt="Category 11">
                    <p>women clothes</p>
                </a>
            </div>
            <div class="category-card">
                <a href="men.php">
                    <img src="men.jpg" alt="Category 11">
                    <p>men clothes</p>
                </a>
            </div>
        </div>
    </section>
    
    <section id="about" class="info-section">
        <h2>About Us</h2>
        <p>PFC INFO</p>
    </section>

    <section id="contact" class="info-section">
        <h2>Contact Us</h2>
        <p>EMAIL , PHONE ,...</p>
    </section>
</div>

<script>
function toggleMenu() {
    const navMenu = document.getElementById('navMenu');
    navMenu.classList.toggle('active');
}

function scrollToSection(sectionId) {
    var element = document.getElementById(sectionId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
}
</script>

</body>
</html>