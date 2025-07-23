<?php
require_once '../includes/functions.php';
require_once '../includes/auth.php';
require_once '../config/database.php';

// Only admin can access
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Handle Role Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    $userId = (int)$_POST['user_id'];
    $newRole = sanitize($_POST['role']);
    
    // Prevent self-demotion
    if ($userId === $_SESSION['user_id']) {
        $_SESSION['error'] = "You cannot change your own role!";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$newRole, $userId]);
        $_SESSION['success'] = "User role updated successfully!";
    }
    
    header("Location: users.php");
    exit();
}

// Handle User Deletion
if (isset($_GET['delete'])) {
    $userId = (int)$_GET['delete'];
    
    // Prevent self-deletion
    if ($userId === $_SESSION['user_id']) {
        $_SESSION['error'] = "You cannot delete yourself!";
    } else {
        try {
            $pdo->beginTransaction();
            
            // Pertama hapus appointments yang terkait
            $stmt = $pdo->prepare("DELETE FROM appointments WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            // Kemudian hapus user
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            
            $pdo->commit();
            $_SESSION['success'] = "User deleted successfully!";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['error'] = "Failed to delete user: " . $e->getMessage();
        }
    }
    
    header("Location: users.php");
    exit();
}

// Get all users
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();

$title = "Manage Users";
include __DIR__ . '/../includes/admin-header.php';
?>

<div class="container mt-4">
    <h2>Manage Users</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= $user['phone'] ?: 'N/A' ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <select name="role" class="form-select form-select-sm" 
                                        onchange="this.form.submit()" 
                                        <?= $user['id'] === $_SESSION['user_id'] ? 'disabled' : '' ?>>
                                        <option value="patient" <?= $user['role'] === 'patient' ? 'selected' : '' ?>>Patient</option>
                                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                    <input type="submit" name="update_role" class="d-none">
                                </form>
                            </td>
                            <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                            <td>
                                <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                <a href="users.php?delete=<?= $user['id'] ?>" class="btn btn-sm btn-danger"
                                   onclick="return confirm('Are you sure you want to delete this user?')">
                                    Delete
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>