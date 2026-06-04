<?php
// index.php (Public Landing Page)
session_start();
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') header("Location: admin/dashboard.php");
    else if ($_SESSION['role'] === 'extension_worker') header("Location: extension-worker/dashboard.php");
    else header("Location: farmer/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADSSU Farmers' Extension Services Portal</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-green: #22C55E;
            --dark-green: #166534;
            --light-bg: #F8FAFC;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light-bg);
        }
        .navbar-brand {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            color: var(--dark-green) !important;
        }
        .hero-section {
            background: linear-gradient(rgba(22, 101, 52, 0.8), rgba(34, 197, 94, 0.8)), url('https://images.unsplash.com/photo-1625246333195-78d9c38ad449?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover;
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .hero-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 3.5rem;
            margin-bottom: 20px;
        }
        .hero-subtitle {
            font-size: 1.25rem;
            font-weight: 300;
            max-width: 800px;
            margin: 0 auto 40px auto;
        }
        .btn-custom {
            background-color: var(--primary-green);
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            background-color: var(--dark-green);
            color: white;
            transform: translateY(-2px);
        }
        .btn-outline-custom {
            background-color: transparent;
            color: white;
            border: 2px solid white;
            padding: 10px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-outline-custom:hover {
            background-color: white;
            color: var(--dark-green);
        }
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-10px);
        }
        .feature-icon {
            font-size: 3rem;
            color: var(--primary-green);
            margin-bottom: 20px;
        }
        .section-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            color: var(--dark-green);
            margin-bottom: 50px;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-leaf text-success me-2"></i>ADSSU FESMS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a class="nav-link btn-custom text-white" href="login.php">Login</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="nav-link btn btn-outline-success rounded-pill fw-bold" href="signup.php">Sign Up</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title">Cultivating Growth Together</h1>
            <p class="hero-subtitle">The ADSSU Farmers’ Extension Services Portal is your online hub for modern farming methods, agricultural techniques, training schedules, and technology updates tailored for local farmers.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="signup.php" class="btn-custom">Join as Farmer</a>
                <a href="#services" class="btn-outline-custom">Learn More</a>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-5">
        <div class="container py-5">
            <h2 class="section-title">What We Offer</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-seedling feature-icon"></i>
                        <h4 class="fw-bold mb-3">Modern Techniques</h4>
                        <p class="text-muted">Discover new farming methods and sustainable practices to improve your crop yield and farm efficiency.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-chalkboard-teacher feature-icon"></i>
                        <h4 class="fw-bold mb-3">Training & Seminars</h4>
                        <p class="text-muted">Stay updated with the latest seminar schedules and get hands-on training from agricultural experts.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-tractor feature-icon"></i>
                        <h4 class="fw-bold mb-3">Technology Updates</h4>
                        <p class="text-muted">Access information on the latest agricultural technology, machinery, and support programs available to you.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 text-center">
        <div class="container">
            <p class="mb-0">&copy; <?= date('Y') ?> Agusan Del Sur State University. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
