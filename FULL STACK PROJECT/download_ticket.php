<?php
session_start();
include('db.php');

// 1. Security: Ensure the student is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['user_name'];
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 2. Fetch event details from the database
$sql = "SELECT title, event_date, venue FROM events WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $event = $result->fetch_assoc();
    
    // Clean filename (remove spaces/special characters)
    $safe_title = preg_replace('/[^A-Za-z0-9_\-]/', '_', $event['title']);

    // 3. Set headers to force a .txt file download
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="Ticket_'.$safe_title.'.txt"');

    // 4. Ticket Content Layout - Updated with new Portal Name
    echo "==========================================\n";
    echo "   ACADEMIC CAMPUS EVENT & REGISTRATION   \n";
    echo "                PORTAL                    \n";
    echo "==========================================\n";
    echo "           OFFICIAL ENTRY TICKET          \n";
    echo "------------------------------------------\n\n";
    echo " ATTENDEE: " . strtoupper($user_name) . "\n";
    echo " EVENT:    " . strtoupper($event['title']) . "\n";
    echo " DATE:     " . $event['event_date'] . "\n";
    echo " VENUE:    " . strtoupper($event['venue']) . "\n\n";
    echo "------------------------------------------\n";
    echo "    Please present this at the entrance.  \n";
    echo "     Generated via Campus Portal Hub      \n";
    echo "==========================================\n";
    exit();
} else {
    echo "Error: Event details not found in database.";
}
?>