<?php
ob_start();
session_start();
include('db.php');

// 1. If already logged in, move them to their dashboard
if (isset($_SESSION['user_id'])) {
    $target = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? "admin_dashboard.php" : "dashboard.php";
    header("Location: " . $target);
    exit();
}

$message = "";

if (isset($_POST['register'])) {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $plain_password = $_POST['password'];
    
    // Hash the password for security
    $password_hashed = password_hash($plain_password, PASSWORD_BCRYPT);
    
    // Auto-assign Admin role for specific email, otherwise Student
    $role = ($email === 'priya@college.edu') ? 'admin' : 'student'; 

    // Use Prepared Statement to check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $check_result = $stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $message = "<div class='alert alert-danger border-0 small py-2'>This email is already registered!</div>";
    } else {
        // Use Prepared Statement for safe insertion
        $insert = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
        $insert->bind_param("ssss", $fullname, $email, $password_hashed, $role);
        
        if ($insert->execute()) {
            $message = "<div class='alert alert-success border-0 small py-2'>Account created! <a href='login.php' class='fw-bold text-dark text-decoration-none'>Login Now →</a></div>";
        } else {
            $message = "<div class='alert alert-danger border-0 small py-2'>Registration failed. Please try again.</div>";
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | Campus Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #2d5a54; --accent: #fce4ec; --sky: #e3f2fd; }
        
        body { 
            background: linear-gradient(135deg, var(--accent) 0%, var(--sky) 100%); 
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            font-family: 'Plus Jakarta Sans', sans-serif; padding: 20px;
        }

        .register-card { 
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(15px);
            border-radius: 28px; 
            border: 1px solid rgba(255, 255, 255, 0.4); 
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08); 
            width: 100%; max-width: 400px;
            padding: 40px; position: relative;
        }

        .header-text { text-align: center; margin-bottom: 30px; }
        .header-text h2 { font-weight: 800; color: var(--primary); letter-spacing: -1px; }

        .form-label { font-weight: 700; color: #555; font-size: 0.75rem; text-transform: uppercase; margin-left: 5px; }
        
        .form-control {
            border-radius: 12px; padding: 12px; border: 1px solid rgba(0,0,0,0.05);
            background: rgba(255, 255, 255, 0.8); transition: 0.3s; font-size: 0.9rem;
        }

        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(45, 90, 84, 0.1);
            border-color: var(--primary); background: #fff;
        }

        .btn-register {
            background: var(--primary); color: white; padding: 12px;
            border-radius: 12px; font-weight: 700; border: none; width: 100%;
            margin-top: 15px; transition: 0.3s;
        }
        .btn-register:hover { transform: translateY(-2px); background: #234742; color: white; }

        .login-link { color: var(--primary); font-weight: 700; text-decoration: none; }
    </style>
</head>
<body>

    <div class="register-card">
        <div class="header-text">
            <h2>Join Portal</h2>
            <p class="text-muted small">Create your account to get started</p>
        </div>

        <?php echo $message; ?>

        <form method="POST" autocomplete="off">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="fullname" class="form-control" placeholder="e.g. Priya Rai" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="name@college.edu" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" name="register" class="btn btn-register">Register Now</button>
        </form>

        <div class="text-center mt-4 pt-3 border-top">
            <p class="small text-muted">Already have an account? 
                <a href="login.php" class="login-link">Sign In</a>
            </p>
        </div>
    </div>

</body>
</html>