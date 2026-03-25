<?php
session_start();
// If the user is already logged in, redirect them automatically to their dashboard
if (isset($_SESSION['user_id'])) {
    $target = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? "admin_dashboard.php" : "dashboard.php";
    header("Location: " . $target);
    exit();   
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Event Registration Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-blue: #1e3a8a;
            --emerald: #059669;
            --emerald-hover: #047857;
        }

        body {
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
            background: linear-gradient(135deg, #d4fc79 0%, #96e6a1 100%);
        }

        .hero-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navigation */
        .navbar {
            padding: 25px 50px;
            background: transparent;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-blue) !important;
            letter-spacing: -1px;
        }

        .nav-link {
            color: var(--primary-blue) !important;
            font-weight: 600;
            margin: 0 15px;
        }

        /* Central Card */
        .hero-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .landing-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            border-radius: 40px;
            padding: 80px 50px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.1);
            max-width: 850px;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.4);
        }

        .main-title {
            color: var(--primary-blue);
            font-weight: 800;
            font-size: 3.5rem;
            margin-bottom: 25px;
            line-height: 1.1;
            letter-spacing: -2px;
        }

        .description {
            color: #4b5563;
            font-size: 1.2rem;
            margin-bottom: 45px;
            max-width: 650px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Connected Buttons */
        .btn-join {
            background-color: var(--emerald);
            color: white;
            padding: 16px 45px;
            border-radius: 18px;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(5, 150, 105, 0.2);
        }

        .btn-signin {
            background-color: transparent;
            color: var(--primary-blue);
            border: 2.5px solid var(--primary-blue);
            padding: 14px 45px;
            border-radius: 18px;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            display: inline-block;
            transition: 0.3s;
            margin-left: 15px;
        }

        .btn-join:hover, .btn-signin:hover {
            transform: translateY(-5px);
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .main-title { font-size: 2.5rem; }
            .btn-signin { margin-left: 0; margin-top: 15px; width: 100%; }
            .btn-join { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>

<div class="hero-wrapper">
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-calendar2-check-fill me-2"></i>CampusPortal
            </a>
            <div class="ms-auto d-flex align-items-center">
                <a href="login.php" class="small fw-bold text-decoration-none me-3" style="color: var(--primary-blue)">Sign In</a>
                <a href="register.php" class="btn btn-dark btn-sm rounded-pill px-4 fw-bold">Join Now</a>
            </div>
        </div>
    </nav>

    <div class="hero-content">
        <div class="landing-card shadow">
            <h1 class="main-title">Academic Campus Event &<br>Registration Portal</h1>
            
            <p class="description">
                Discover workshops, fests, and seminars across the campus. Join our centralized platform today.
            </p>

            <div class="d-flex flex-wrap justify-content-center">
                <a href="register.php" class="btn-join">
                    Join Now <i class="bi bi-arrow-right-short fs-4"></i>
                </a>
                <a href="login.php" class="btn-signin">Sign In</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>