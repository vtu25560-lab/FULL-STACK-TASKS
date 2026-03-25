<?php
session_start();
include('db.php');

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
// 2. Capture the ID from the URL link
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($event_id > 0) {
    // 3. Verify if the student is already registered
    $check = $conn->prepare("SELECT id FROM registrations WHERE user_id = ? AND event_id = ?");
    $check->bind_param("ii", $user_id, $event_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Already registered
        header("Location: dashboard.php?error=already_joined");
    } else {
        // 4. Insert the new registration
        $stmt = $conn->prepare("INSERT INTO registrations (user_id, event_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $event_id);
        
        if ($stmt->execute()) {
            // Success: Trigger the SweetAlert popup on the dashboard
            header("Location: dashboard.php?registration=success");
        } else {
            header("Location: dashboard.php?error=db_fail");
        }
    }
} else {
    header("Location: dashboard.php?error=invalid_event");
}
?>