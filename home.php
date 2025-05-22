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
                <li><a href="favorite.php" class="link">Favorite</a></li>
                <li><a href="profile.php" class="link">Profile</a></li>  
                <li><a href="sell.php" class="link">Add_Product</a></li>
                <li><a href="#footer" class="link" onclick="scrollToFooter(event)">Contact Us</a></li>
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

    <form action="result.php" method="get" class="search-bar" style="display:flex;justify-content:center;margin:130px 0 30px 0;">
        <input type="text" name="q" placeholder="Search for products..." class="input-field" style="width:300px;padding:10px;border-radius:20px;border:1px solid #ccc;">
        <button type="submit" class="btn" style="margin-left:10px;padding:10px 20px;border-radius:20px;background:#007bff;color:#fff;border:none;">Search</button>
    </form>

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
    
</div>

<footer id="footer" style="background:rgba(39,39,39,0.92); color:#fff; padding:0 0 20px 0; text-align:center; font-size:17px; margin-top:0; box-shadow:0 -2px 18px rgba(0,0,0,0.10);">
    <div style="max-width:1200px; margin:auto; display:flex; flex-direction:column; align-items:center; gap:18px;">
        <div style="margin-bottom:10px;">
            <img src="logo-swopzy.png" alt="Swopzy Logo" style="height:45px; vertical-align:middle; filter:drop-shadow(0 2px 6px rgba(0,0,0,0.10));">
        </div>
        <div style="font-size:22px; font-weight:bold; letter-spacing:1px;">Swopzy</div>
        <div style="display:flex; gap:18px; font-size:18px; margin-bottom:8px;">
            <a href="contact.php" style="color:#50bfff; text-decoration:underline;">Contact Us</a>
            <span style="color:#fff;">|</span>
            <a href="about.php" style="color:#50bfff; text-decoration:underline;">About</a>
            <span style="color:#fff;">|</span>
            <a href="favorite.php" style="color:#50bfff; text-decoration:underline;">Favorites</a>
        </div>
        <div style="font-size:15px; color:#e0e0e0;">&copy; 2025 Swopzy. All rights reserved.</div>
        <div style="font-size:13px; color:#b0e0ff;">Designed by Swopzy Team</div>
    </div>
</footer>

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

function scrollToFooter(event) {
    event.preventDefault();
    document.getElementById('footer').scrollIntoView({ behavior: 'smooth' });
}
</script>

</body>
</html>