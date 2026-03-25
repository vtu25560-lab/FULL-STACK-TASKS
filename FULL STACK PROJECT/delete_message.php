<?php
session_start();
include('db.php'); // Database connection

// 1. Security Check: Only Admin (Priya) can delete student requests
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// 2. Capture and sanitize the Message ID
$message_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($message_id > 0) {
    // 3. Delete the specific message from the database
    $stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    
    if ($stmt->execute()) {
        // Success: Redirect back to inbox with confirmation
        header("Location: view_messages.php?status=deleted");
    } else {
        // Database Error: Redirect with error status
        header("Location: view_messages.php?status=error");
    }
    $stmt->close();
} else {
    // No valid ID: Simply return to inbox
    header("Location: view_messages.php");
}
exit();
?>