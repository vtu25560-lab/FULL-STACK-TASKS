<?php
session_start();
include('db.php');

// 1. Security Check: Ensure a student is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
// 2. Capture the event ID from the 'Cancel' link
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($event_id > 0) {
    // 3. Delete the registration record for this specific student and event
    $stmt = $conn->prepare("DELETE FROM registrations WHERE user_id = ? AND event_id = ?");
    $stmt->bind_param("ii", $user_id, $event_id);
    
    if ($stmt->execute()) {
        // Success: Redirect back to the dashboard
        header("Location: dashboard.php?action=unregistered");
    } else {
        // Database error
        header("Location: dashboard.php?error=cancel_failed");
    }
} else {
    // Invalid ID provided
    header("Location: dashboard.php");
}
?>