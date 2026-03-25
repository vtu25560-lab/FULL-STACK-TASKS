<?php
ob_start();
session_start();
include('db.php');

// Security Wall
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 1. Fetch Summary Stats
$total_events = $conn->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'];
$total_regs = $conn->query("SELECT COUNT(*) as count FROM registrations")->fetch_assoc()['count'];

// 2. Fetch Events with Image & Registration Count
$query = "SELECT events.*, 
          (SELECT COUNT(*) FROM registrations WHERE event_id = events.id) as reg_count 
          FROM events ORDER BY event_date DESC";
$events = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Campus Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2d5a54;
            --accent: #fce4ec;
            --sky: #e3f2fd;
            --danger: #dc3545;
        }

        body { 
            background: linear-gradient(135deg, var(--accent) 0%, var(--sky) 100%); 
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif; 
        }

        .navbar-admin { 
            background: rgba(45, 90, 84, 0.9);
            backdrop-filter: blur(10px);
            padding: 15px 30px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 25px;
            border: 1px solid rgba(255,255,255,0.4);
            transition: 0.3s ease;
        }

        .glass-table-card { 
            background: rgba(255, 255, 255, 0.8); 
            backdrop-filter: blur(15px);
            border-radius: 24px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.05); 
            border: 1px solid rgba(255,255,255,0.5);
            overflow: hidden;
            margin-bottom: 50px;
        }

        .table thead { background: var(--primary); color: white; }
        .table th { font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; padding: 20px; border: none; }
        .table td { padding: 20px; vertical-align: middle; border-bottom: 1px solid rgba(0,0,0,0.03); }

        /* 🚩 LARGE IMAGE STYLE */
        .event-img-large {
            width: 140px; 
            height: 80px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: 0.3s transform;
        }
        .event-img-large:hover { transform: scale(1.05); }

        .btn-add {
            background: var(--primary);
            color: white;
            border-radius: 12px;
            font-weight: 700;
            padding: 12px 25px;
            transition: 0.3s;
        }
        
        .btn-manage { background: #333; color: white; }
        .btn-delete { background: var(--danger); color: white; }
        
        .action-btns .btn {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 8px 16px;
            border-radius: 10px;
            border: none;
        }

        .badge-count {
            background: rgba(45, 90, 84, 0.1);
            color: var(--primary);
            font-weight: 800;
            padding: 8px 15px;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<nav class="navbar-admin d-flex justify-content-between align-items-center sticky-top shadow-sm">
    <h5 class="mb-0 text-white fw-800">Campus<span style="opacity: 0.7;">Portal</span></h5>
    <div class="d-flex align-items-center">
        <span class="text-white me-3 small">Admin: <strong>Priya</strong></span>
        <a href="logout.php" class="btn btn-sm btn-light fw-bold rounded-pill px-3">Logout</a>
    </div>
</nav>

<div class="container py-5">
    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
            <?php 
                if($_GET['msg'] == 'deleted') echo "🗑️ Event and associated image deleted successfully.";
                if($_GET['msg'] == 'added') echo "✨ New event published successfully.";
            ?>
        </div>
    <?php endif; ?>

    <div class="row mb-5 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-800" style="color: var(--primary); letter-spacing: -1.5px;">Dashboard Overview</h2>
            <p class="text-muted">Manage your campus events and track registrations.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="add_event.php" class="btn btn-add shadow-sm">+ Create New Event</a>
        </div>
    </div>

    <div class="row mb-5 g-4">
        <div class="col-md-4">
            <div class="stat-card">
                <p class="text-muted fw-bold small mb-1">LIVE EVENTS</p>
                <h2 class="fw-800 mb-0"><?php echo $total_events; ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <p class="text-muted fw-bold small mb-1">TOTAL REGISTRATIONS</p>
                <h2 class="fw-800 mb-0"><?php echo $total_regs; ?></h2>
            </div>
        </div>
    </div>

    <div class="glass-table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Poster Preview</th>
                        <th>Event Details</th>
                        <th>Venue</th>
                        <th class="text-center">Students</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($events->num_rows > 0): ?>
                        <?php while($row = $events->fetch_assoc()): ?>
                        <tr>
                            <td class="ps-4">
                                <img src="uploads/<?php echo $row['image']; ?>" class="event-img-large" onerror="this.src='https://via.placeholder.com/140x80?text=No+Image'">
                            </td>
                            <td>
                                <div class="fw-800 text-dark"><?php echo htmlspecialchars($row['title']); ?></div>
                                <small class="text-muted"><?php echo date('M d, Y | h:i A', strtotime($row['event_date'])); ?></small>
                            </td>
                            <td><span class="text-muted small fw-bold"><?php echo htmlspecialchars($row['venue']); ?></span></td>
                            <td class="text-center">
                                <span class="badge-count"><?php echo $row['reg_count']; ?></span>
                            </td>
                            <td class="text-center">
                                <div class="action-btns d-flex gap-2 justify-content-center">
                                    <a href="view_attendees.php?id=<?php echo $row['id']; ?>" class="btn btn-manage">Manage</a>
                                    <a href="delete_event.php?id=<?php echo $row['id']; ?>" 
                                       class="btn btn-delete" 
                                       onclick="return confirm('Are you sure you want to delete this event? This will also remove the image file.')">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">No events found. Start by creating one!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>