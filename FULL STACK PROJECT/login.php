<?php
ob_start(); 
session_start();
include('db.php');

$error = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // 🚩 MATCHING YOUR REGISTER: Using 'fullname' instead of 'full_name'
    $stmt = $conn->prepare("SELECT id, fullname, password, role FROM users WHERE email = ?");
    
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify the BCRYPT hash from your register.php
            if (password_verify($password, $user['password'])) {
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['fullname'];
                
                $user_role = trim(strtolower($user['role']));
                $_SESSION['role'] = $user_role;

                session_write_close();

                if ($user_role === 'admin') {
                    header("Location: admin_dashboard.php");
                    exit();
                } else {
                    // Make sure you have a dashboard.php for students!
                    header("Location: dashboard.php");
                    exit();
                }
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Email not found. Please register.";
        }
    } else {
        $error = "Database error. Check your columns.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Academic Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #2d5a54; --accent: #fce4ec; --sky: #e3f2fd; }
        body { 
            background: linear-gradient(135deg, var(--accent) 0%, var(--sky) 100%); 
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            font-family: 'Plus Jakarta Sans', sans-serif; 
        }
        .glass-login-card { 
            background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(15px);
            border-radius: 24px; padding: 35px 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1); border: 1px solid rgba(255,255,255,0.5);
            width: 100%; max-width: 350px;
        }
        .portal-header { text-align: center; margin-bottom: 25px; }
        .portal-header h2 { color: var(--primary); font-weight: 800; font-size: 1.1rem; text-transform: uppercase; }
        .form-control { border-radius: 10px; padding: 12px; font-size: 0.85rem; border: 1px solid rgba(0,0,0,0.1); }
        .btn-login { background-color: var(--primary); color: white; border-radius: 10px; font-weight: 800; padding: 12px; border: none; width: 100%; margin-top: 10px; }
    </style>
</head>
<body>

<div class="glass-login-card">
    <div class="portal-header">
        <h2>Academic Portal</h2>
        <p class="small text-muted mb-0">Student & Admin Login</p>
    </div>

    <?php if($error): ?>
        <div class="alert alert-danger py-2 small text-center" style="font-size: 0.75rem;"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="small fw-bold text-muted text-uppercase">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-4">
            <label class="small fw-bold text-muted text-uppercase">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" name="login" class="btn-login">LOGIN</button>
    </form>
    
    <div class="text-center mt-4 pt-3 border-top">
        <a href="register.php" class="small fw-bold text-decoration-none" style="color: var(--primary);">Create Account</a>
    </div>
</div>

</body>
</html>