<?php
session_start();
include('db.php');

// Security Check: Only Admin can see this
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get the Event ID from the URL
if (!isset($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$event_id = intval($_GET['id']);

// Fetch Event Details
$event_query = $conn->query("SELECT title FROM events WHERE id = $event_id");
$event = $event_query->fetch_assoc();

// Fetch Registered Students for this event
$query = "SELECT users.fullname, users.email, registrations.registration_date 
          FROM registrations 
          JOIN users ON registrations.user_id = users.id 
          WHERE registrations.event_id = $event_id 
          ORDER BY registrations.registration_date DESC";

$registrations = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Registrations | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --primary: #2d5a54; --accent: #fce4ec; --sky: #e3f2fd; }
        body { 
            background: linear-gradient(135deg, var(--accent) 0%, var(--sky) 100%); 
            min-height: 100vh; font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .navbar-custom { background: var(--primary); padding: 15px 40px; }
        .glass-card { 
            background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);
            border-radius: 24px; padding: 30px; margin-top: 50px;
            border: 1px solid white; box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        .table { border-radius: 15px; overflow: hidden; }
        .thead-dark { background: var(--primary); color: white; }
    </style>
</head>
<body>

<nav class="navbar-custom d-flex justify-content-between align-items-center shadow">
    <h4 class="text-white mb-0 fw-800">CampusPortal Admin</h4>
    <a href="admin_dashboard.php" class="btn btn-light btn-sm rounded-pill px-3 fw-bold">Back to Events</a>
</nav>

<div class="container">
    <div class="glass-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-800 mb-0" style="color: var(--primary);">Registrations</h2>
                <p class="text-muted">Event: <strong><?php echo htmlspecialchars($event['title']); ?></strong></p>
            </div>
            <span class="badge bg-success rounded-pill px-3 py-2">
                Total: <?php echo $registrations->num_rows; ?> Students
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover bg-white shadow-sm">
                <thead class="thead-dark">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Student Name</th>
                        <th>Email Address</th>
                        <th>Registration Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($registrations->num_rows > 0): ?>
                        <?php $count = 1; while($row = $registrations->fetch_assoc()): ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?php echo $count++; ?></td>
                                <td class="fw-600"><?php echo htmlspecialchars($row['fullname']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td class="text-muted"><?php echo date('M d, Y - h:i A', strtotime($row['registration_date'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">No students registered for this event yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>