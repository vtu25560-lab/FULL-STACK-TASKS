<?php
ob_start();
session_start();
include('db.php');

/**
 * 1. SECURITY CHECK
 * Ensures only Priya (Admin) can trigger this file.
 */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

/**
 * 2. DELETE LOGIC
 */
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // A. Fetch the image filename before deleting the record
    $stmt_img = $conn->prepare("SELECT image FROM events WHERE id = ?");
    $stmt_img->bind_param("i", $id);
    $stmt_img->execute();
    $result = $stmt_img->get_result();
    $event_data = $result->fetch_assoc();
    $stmt_img->close();

    if ($event_data) {
        $image_file = "uploads/" . $event_data['image'];

        // B. Delete Registrations First (Prevents Foreign Key Errors)
        $stmt_reg = $conn->prepare("DELETE FROM registrations WHERE event_id = ?");
        $stmt_reg->bind_param("i", $id);
        $stmt_reg->execute();
        $stmt_reg->close();

        // C. Delete the Event Record
        $stmt_del = $conn->prepare("DELETE FROM events WHERE id = ?");
        $stmt_del->bind_param("i", $id);
        
        if ($stmt_del->execute()) {
            // D. Remove the physical file from your HP Pavilion 'uploads' folder
            if (!empty($event_data['image']) && file_exists($image_file)) {
                unlink($image_file);
            }
            
            // Redirect with success message
            header("Location: admin_dashboard.php?msg=deleted");
            exit();
        } else {
            echo "Error deleting event: " . $conn->error;
        }
        $stmt_del->close();
    }
} else {
    header("Location: admin_dashboard.php");
}

ob_end_flush();
exit();
?>