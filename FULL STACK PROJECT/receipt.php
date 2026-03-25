<?php
session_start();
include('db.php');

if (!isset($_GET['event_id']) || !isset($_SESSION['user_id'])) {
    die("Invalid Access");
}

$user_id = $_SESSION['user_id'];
$event_id = $_GET['event_id'];

// Get registration details
$sql = "SELECT users.fullname, events.title, events.event_date, events.venue, registrations.reg_date 
        FROM registrations 
        JOIN users ON registrations.user_id = users.id 
        JOIN events ON registrations.event_id = events.id 
        WHERE registrations.user_id = '$user_id' AND registrations.event_id = '$event_id'";
$result = $conn->query($sql);
$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration Receipt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @print { .no-print { display: none; } }
        .receipt-card { border: 2px dashed #ccc; padding: 20px; max-width: 500px; margin: auto; }
    </style>
</head>
<body class="bg-white py-5">
    <div class="receipt-card shadow-sm">
        <h3 class="text-center">Event Registration Confirmation</h3>
        <hr>
        <p><strong>Student Name:</strong> <?php echo $data['fullname']; ?></p>
        <p><strong>Event:</strong> <?php echo $data['title']; ?></p>
        <p><strong>Date:</strong> <?php echo date('l, M d, Y', strtotime($data['event_date'])); ?></p>
        <p><strong>Venue:</strong> <?php echo $data['venue']; ?></p>
        <p class="text-muted small">Registered on: <?php echo $data['reg_date']; ?></p>
        <hr>
        <div class="text-center no-print">
            <button onclick="window.print()" class="btn btn-primary">Print Receipt</button>
            <a href="dashboard.php" class="btn btn-link">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>