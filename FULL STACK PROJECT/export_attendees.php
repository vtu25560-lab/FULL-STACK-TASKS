<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' || !isset($_GET['id'])) {
    die("Access Denied");
}

$event_id = intval($_GET['id']);

// Fetch Event Title for the filename
$event_q = $conn->query("SELECT title FROM events WHERE id = $event_id");
$event = $event_q->fetch_assoc();
$filename = str_replace(' ', '_', $event['title']) . "_Attendees.csv";

// Set headers to force download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

// Open the "output" stream
$output = fopen('php://output', 'w');

// Set column headers for the CSV file
fputcsv($output, array('Student Name', 'Email', 'Registration Date'));

// Fetch the data
$query = "SELECT users.fullname, users.email, registrations.reg_date 
          FROM registrations 
          JOIN users ON registrations.user_id = users.id 
          WHERE registrations.event_id = $event_id";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit();
?>