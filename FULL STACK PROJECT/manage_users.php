<?php
session_start();
include('db.php');

// 1. Security: Only Admins can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php?error=unauthorized");
    exit();
}

// 2. Handle Role Update Logic
if (isset($_GET['action']) && isset($_GET['id'])) {
    $target_id = intval($_GET['id']);
    $new_role = ($_GET['action'] == 'make_admin') ? 'admin' : 'student';
    
    $update_sql = "UPDATE users SET role = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_role, $target_id);
    $stmt->execute();
    header("Location: manage_users.php?msg=updated");
    exit();
}

// 3. Fetch all users
$result = $conn->query("SELECT id, fullname, email, role FROM users ORDER BY fullname ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">🎓 Admin Panel</a>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm">Back to Dashboard</a>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">User Directory</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Current Role</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($user = $result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($user['fullname']); ?></strong></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="badge <?php echo ($user['role'] == 'admin') ? 'bg-warning text-dark' : 'bg-info'; ?>">
                                    <?php echo strtoupper($user['role']); ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <?php if($user['id'] != $_SESSION['user_id']): ?>
                                    <?php if($user['role'] == 'student'): ?>
                                        <a href="manage_users.php?action=make_admin&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-warning">Make Admin</a>
                                    <?php else: ?>
                                        <a href="manage_users.php?action=make_student&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-secondary">Make Student</a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <small class="text-muted italic">(You)</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>