<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$fullname = $_SESSION['user_name']; 
$role = $_SESSION['role'];

// --- Handle Registration Logic ---
if (isset($_POST['action'])) {
    $event_id = intval($_POST['event_id']);
    if ($_POST['action'] == 'register') {
        $conn->query("INSERT INTO registrations (user_id, event_id) VALUES ($user_id, $event_id)");
    } elseif ($_POST['action'] == 'unregister') {
        $conn->query("DELETE FROM registrations WHERE user_id = $user_id AND event_id = $event_id");
    }
    header("Location: dashboard.php");
    exit();
}

// Fetch Events
$events = $conn->query("SELECT * FROM events ORDER BY event_date ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #c0f3ea; font-family: 'Segoe UI', sans-serif; }
        .navbar-custom { background: #2d5a54; color: white; padding: 15px 30px; }
        .event-card { background: white; border-radius: 0px; margin-bottom: 30px; box-shadow: 0 10px 20px rgba(0,0,0,0.1); overflow: hidden; }
        .event-banner { width: 100%; height: 180px; object-fit: cover; }
        .btn-custom { border-radius: 0px; font-weight: bold; }
        .btn-register { background: #2d5a54; color: white; }
        .btn-unregister { background: #dc3545; color: white; }
    </style>
</head>
<body>

<nav class="navbar-custom d-flex justify-content-between align-items-center shadow-sm">
    <h4 class="mb-0">Welcome, <?php echo htmlspecialchars($fullname); ?></h4>
    <a href="logout.php" class="btn btn-sm btn-outline-light rounded-0">Logout</a>
</nav>

<div class="container mt-5">
    <h2 class="fw-bold mb-4" style="color: #2d5a54;">Upcoming Campus Events</h2>
    <div class="row">
        <?php while($row = $events->fetch_assoc()): 
            // Check if Surya is already registered for this specific event
            $check = $conn->query("SELECT * FROM registrations WHERE user_id = $user_id AND event_id = {$row['id']}");
            $is_registered = ($check->num_rows > 0);
        ?>
            <div class="col-md-6">
                <div class="card event-card border-0">
                    <img src="uploads/<?php echo $row['image'] ? $row['image'] : 'default.jpg'; ?>" class="event-banner">
                    <div class="card-body p-4">
                        <h3 class="fw-bold"><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p class="text-muted mb-4">📍 <?php echo htmlspecialchars($row['venue']); ?></p>
                        
                        <div class="d-flex gap-2">
                            <form method="POST" class="flex-grow-1">
                                <input type="hidden" name="event_id" value="<?php echo $row['id']; ?>">
                                <?php if (!$is_registered): ?>
                                    <button type="submit" name="action" value="register" class="btn btn-register btn-custom w-100 py-2">Register Now</button>
                                <?php else: ?>
                                    <button type="submit" name="action" value="unregister" class="btn btn-unregister btn-custom w-100 py-2">Unregister</button>
                                <?php endif; ?>
                            </form>

                            <?php if ($is_registered): ?>
                                <a href="download_ticket.php?id=<?php echo $row['id']; ?>" class="btn btn-dark btn-custom flex-grow-1 py-2">
                                    Download Ticket
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>