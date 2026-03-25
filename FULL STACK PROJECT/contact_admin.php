<?php
session_start();
include('db.php');

// 1. Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message_sent = false;

if (isset($_POST['send_msg'])) {
    $user_id = $_SESSION['user_id'];
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $content = mysqli_real_escape_string($conn, $_POST['message']);

    // 2. Insert into the existing messages table
    $stmt = $conn->prepare("INSERT INTO messages (user_id, subject, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $subject, $content);
    
    if ($stmt->execute()) {
        $message_sent = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Admin - Campus Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Matching Dashboard Gradient */
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }

        /* Glass-morphism Card */
        .contact-card { 
            background: rgba(255, 255, 255, 0.95); 
            border-radius: 20px; 
            padding: 40px; 
            width: 100%; 
            max-width: 500px; 
            margin: auto;
            border: none;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 12px;
        }

        .btn-submit {
            background: #764ba2;
            border: none;
            color: white;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background: #667eea;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="contact-card shadow-lg">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-dark">Contact Admin</h2>
                <p class="text-muted small">Have an issue? Send us a message.</p>
            </div>

            <?php if($message_sent): ?>
                <div class="alert alert-success rounded-pill text-center small">
                    Message sent! <a href="dashboard.php" class="fw-bold text-decoration-none">Return to Dashboard</a>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Subject</label>
                    <input type="text" name="subject" class="form-control" placeholder="e.g., Tech Fest Registration Query" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary">Your Message</label>
                    <textarea name="message" class="form-control" rows="5" placeholder="Describe your issue here..." required></textarea>
                </div>
                <button type="submit" name="send_msg" class="btn btn-submit w-100 rounded-pill py-2 shadow">Send Message</button>
                
                <div class="text-center mt-3">
                    <a href="dashboard.php" class="text-muted small text-decoration-none">← Back to Dashboard</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>