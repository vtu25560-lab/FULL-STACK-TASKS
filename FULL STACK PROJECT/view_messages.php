<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Inbox - Admin Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Light blue background for inbox */
        body { background: #e3f2fd; padding: 50px 0; font-family: 'Segoe UI', sans-serif; }
        
        .inbox-container { 
            background: #fff; 
            border-radius: 0px; 
            padding: 40px; 
            max-width: 900px;
            margin: auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .admin-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #0d6efd;
            font-weight: bold;
        }

        /* Sharp blue button */
        .btn-blue { 
            background-color: #0d6efd; 
            color: white; 
            border-radius: 0px; 
            font-weight: bold; 
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="inbox-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="admin-label">Administrator View</span>
                    <h2 class="fw-bold text-primary mb-0">Student Inbox</h2>
                </div>
                <a href="dashboard.php" class="btn btn-blue px-4">Back to Dashboard</a>
            </div>
            <hr class="mb-4">
            <div class="text-center py-5">
                <p class="text-muted fs-5">No student requests found in the portal.</p>
            </div>
        </div>
    </div>
</body>
</html>