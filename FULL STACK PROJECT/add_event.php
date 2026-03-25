<?php
session_start();
include('db.php');

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";

if (isset($_POST['submit'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $event_date = $_POST['event_date'];
    $venue = mysqli_real_escape_string($conn, $_POST['venue']);
    $max_slots = intval($_POST['max_slots']); 
    
    $image_name = $_FILES['event_image']['name'];
    $unique_image_name = time() . '_' . basename($image_name);
    $target_file = "uploads/" . $unique_image_name;
    
    $check = getimagesize($_FILES['event_image']['tmp_name']);
    
    if($check !== false && $_FILES['event_image']['size'] < 5000000) {
        if (!is_dir('uploads')) { mkdir('uploads', 0777, true); }

        $stmt = $conn->prepare("INSERT INTO events (title, event_date, venue, max_slots, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssis", $title, $event_date, $venue, $max_slots, $unique_image_name);

        if ($stmt->execute()) {
            if (move_uploaded_file($_FILES['event_image']['tmp_name'], $target_file)) {
                $message = "<div class='alert alert-success border-0 shadow-sm rounded-3'>✨ Event published successfully!</div>";
            }
        }
        $stmt->close();
    } else {
        $message = "<div class='alert alert-danger border-0 shadow-sm rounded-3'>❌ Invalid image or file too large.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event | Campus Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #2d5a54; --accent: #fce4ec; --sky: #e3f2fd; }
        body { background: linear-gradient(135deg, var(--accent) 0%, var(--sky) 100%); min-height: 100vh; font-family: 'Plus Jakarta Sans', sans-serif; }
        .navbar-admin { background: rgba(45, 90, 84, 0.95); backdrop-filter: blur(10px); padding: 15px 30px; color: white; }
        .glass-card { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(15px); border-radius: 28px; box-shadow: 0 25px 50px rgba(0,0,0,0.08); border: 1px solid rgba(255,255,255,0.6); padding: 40px; }
        .form-label { color: var(--primary); font-weight: 700; font-size: 0.9rem; margin-bottom: 8px; }
        
        /* 🚩 Clean, Empty Entry Bars */
        .form-control { 
            border-radius: 15px; 
            padding: 14px 18px; 
            border: 1px solid rgba(0,0,0,0.1);
            background: white;
            font-weight: 600;
        }
        .form-control:focus { box-shadow: 0 0 0 4px rgba(45, 90, 84, 0.1); border-color: var(--primary); }

        .btn-custom { background-color: var(--primary); color: white; border-radius: 15px; font-weight: 800; padding: 16px; border: none; transition: 0.4s; }
        .btn-custom:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(45, 90, 84, 0.25); color: white; }
        #imagePreview { width: 100%; height: 200px; border-radius: 18px; object-fit: cover; display: none; margin-top: 15px; border: 2px dashed rgba(45, 90, 84, 0.2); }
    </style>
</head>
<body>

<nav class="navbar-admin d-flex justify-content-between align-items-center">
    <h5 class="mb-0 fw-800">Campus<span style="opacity: 0.7;">Portal</span></h5>
    <a href="admin_dashboard.php" class="btn btn-sm btn-light fw-bold px-3" style="border-radius: 10px;">Back to Panel</a>
</nav>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="glass-card">
                <?php echo $message; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label class="form-label">Event Title</label>
                        <input type="text" name="title" class="form-control" placeholder="" required>
                    </div>

                    <div class="row">
                        <div class="col-md-7 mb-4">
                            <label class="form-label">Date & Time</label>
                            <input type="datetime-local" name="event_date" class="form-control" required>
                        </div>
                        <div class="col-md-5 mb-4">
                            <label class="form-label">Total Slots</label>
                            <input type="text" name="max_slots" class="form-control" placeholder="" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Venue</label>
                        <input type="text" name="venue" class="form-control" placeholder="" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Event Poster</label>
                        <input type="file" name="event_image" class="form-control" accept="image/*" required onchange="previewImage(event)">
                        <img id="imagePreview" alt="Image Preview">
                    </div>

                    <button type="submit" name="submit" class="btn btn-custom w-100">PUBLISH EVENT</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('imagePreview');
            output.src = reader.result;
            output.style.display = 'block';
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>

</body>
</html>