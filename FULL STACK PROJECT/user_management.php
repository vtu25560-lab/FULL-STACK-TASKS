<?php
session_start();
include('db.php');

// 1. Admin Security Check: Only Priya (admin) can access this
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// 2. Handle User Deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    // Prevent the Admin from deleting their own account
    if ($delete_id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            header("Location: user_management.php?msg=user_deleted");
        }
    } else {
        header("Location: user_management.php?msg=cannot_delete_self");
    }
}

// 3. Fetch all Users
$result = $conn->query("SELECT id, fullname, email, role FROM users ORDER BY role ASC, fullname ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Campus Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 0; }
        .glass-panel { background: rgba(255, 255, 255, 0.95); border-radius: 20px; padding: 30px; }
        .badge-admin { background-color: #ffc107; color: #000; }
        .badge-student { background-color: #0dcaf0; color: #fff; }
    </style>
</head>
<body>
    <div class="container">
        <div class="glass-panel shadow-lg">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold text-dark">User Management</h3>
                <a href="dashboard.php" class="btn btn-secondary btn-sm rounded-pill">Back to Dashboard</a>
            </div>

            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($user['fullname']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="badge <?php echo ($user['role'] === 'admin') ? 'badge-admin' : 'badge-student'; ?>">
                                    <?php echo strtoupper($user['role']); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <a href="user_management.php?delete_id=<?php echo $user['id']; ?>" 
                                       class="btn btn-outline-danger btn-sm rounded-pill" 
                                       onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                                <?php else: ?>
                                    <span class="text-muted small">Current User</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>