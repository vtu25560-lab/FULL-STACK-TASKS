<?php
session_start();
include('db.php');

$error = "";

if (isset($_POST['admin_login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Fetch user and check if they are an admin
    $stmt = $conn->prepare("SELECT id, fullname, password, role FROM users WHERE email = ? AND role = 'admin'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            // Set session and redirect to the dashboard
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['fullname'];
            $_SESSION['role'] = 'admin';
            
            header("Location: dashboard.php");
            exit(); 
        } else {
            $error = "Incorrect Admin Password.";
        }
    } else {
        $error = "Access Denied: This account is not an Administrator.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Access - Campus Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #1a1a1a; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .admin-card { border-radius: 15px; border: 2px solid #ffc107; background: #2d2d2d; color: white; width: 400px; }
    </style>
</head>
<body>
    <div class="card admin-card shadow-lg">
        <div class="card-body p-5">
            <h3 class="text-center fw-bold mb-4 text-warning">Admin Portal</h3>
            
            <?php if($error): ?>
                <div class="alert alert-danger py-2 small text-center"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Admin Email</label>
                    <input type="email" name="email" class="form-control bg-dark text-white border-secondary" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold">Secret Password</label>
                    <input type="password" name="password" class="form-control bg-dark text-white border-secondary" required>
                </div>
                <button type="submit" name="admin_login" class="btn btn-warning w-100 fw-bold">Enter Dashboard</button>
            </form>
            <div class="text-center mt-3">
                <a href="login.php" class="text-secondary small text-decoration-none">Student Login? Click Here</a>
            </div>
        </div>
    </div>
</body>
</html>