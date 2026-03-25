<?php
session_start();
include('db.php');

// Security Check: Only Admin can see this
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get the Event ID from the URL link
if (!isset($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$event_id = intval($_GET['id']);

// 1. Fetch Event Title so we know which event we are looking at
$event_info = $conn->query("SELECT title FROM events WHERE id = $event_id")->fetch_assoc();

// 2. Fetch all students who registered for THIS specific event
// We JOIN the users table to get their real names and emails
$query = "SELECT users.fullname, users.email, registrations.registration_date 
          FROM registrations 
          JOIN users ON registrations.user_id = users.id 
          WHERE registrations.event_id = $event_id 
          ORDER BY registrations.registration_date DESC";

$attendees = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registered Students | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --primary: #2d5a54; --bg-gradient: linear-gradient(135deg, #fce4ec 0%, #e3f2fd 100%); }
        body { background: var(--bg-gradient); min-height: 100vh; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            padding: 40px;
            border: 1px solid white;
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
            margin-top: 50px;
        }

        .table thead { background: var(--primary); color: white; }
        .table th { padding: 15px; border: none; font-size: 0.8rem; text-transform: uppercase; }
        .table td { padding: 15px; vertical-align: middle; border-bottom: 1px solid rgba(0,0,0,0.05); }
        
        .btn-back {
            background: var(--primary); color: white; border-radius: 12px; font-weight: 700;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="glass-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-800 text-dark mb-0">Registered Students</h2>
                <p class="text-muted">Event: <span class="fw-bold" style="color: var(--primary);"><?php echo htmlspecialchars($event_info['title']); ?></span></p>
            </div>
            <a href="admin_dashboard.php" class="btn btn-back shadow-sm px-4">
                <i class="bi bi-arrow-left me-2"></i> Back to Dashboard
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="ps-4">S.No</th>
                        <th>Student Name</th>
                        <th>Email Address</th>
                        <th>Registration Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($attendees->num_rows > 0): ?>
                        <?php $count = 1; while($row = $attendees->fetch_assoc()): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-muted"><?php echo $count++; ?></td>
                            <td><div class="fw-700"><?php echo htmlspecialchars($row['fullname']); ?></div></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td class="small text-muted">
                                <?php echo date('D, M d, Y - h:i A', strtotime($row['registration_date'])); ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-3 text-muted">No students have registered for this event yet.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>