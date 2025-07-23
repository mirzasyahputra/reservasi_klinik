<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

// Get user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get user appointments with additional details
$stmt = $pdo->prepare("
    SELECT a.*, d.name as doctor_name, s.name as service_name 
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.id
    JOIN services s ON a.service_id = s.id
    WHERE a.user_id = ?
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");
$stmt->execute([$_SESSION['user_id']]);
$appointments = $stmt->fetchAll();

// Handle success/error messages
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

$title = "My Profile";
include 'includes/header.php';
?>

<div class="container mt-4">
    <h2>My Profile</h2>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Personal Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                    <p><strong>Phone:</strong> <?= $user['phone'] ? htmlspecialchars($user['phone']) : 'Not provided' ?></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>My Appointments</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($appointments)): ?>
                        <p>You don't have any appointments yet. <a href="booking.php">Book one now</a>.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Doctor</th>
                                        <th>Service</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($appointments as $appointment): 
                                        $appointmentDateTime = new DateTime($appointment['appointment_date'] . ' ' . $appointment['appointment_time']);
                                        $isPastAppointment = $appointmentDateTime < new DateTime();
                                        $canCancel = !$isPastAppointment && in_array($appointment['status'], ['pending', 'confirmed']);
                                    ?>
                                    <tr>
                                        <td>
                                            <?= $appointmentDateTime->format('M j, Y') ?><br>
                                            <?= $appointmentDateTime->format('h:i A') ?>
                                            <?= $isPastAppointment ? '<br><small class="text-muted">Past appointment</small>' : '' ?>
                                        </td>
                                        <td><?= htmlspecialchars($appointment['doctor_name']) ?></td>
                                        <td><?= htmlspecialchars($appointment['service_name']) ?></td>
                                        <td>
                                            <?php
                                            $badgeClass = [
                                                'pending' => 'bg-warning',
                                                'confirmed' => 'bg-success',
                                                'completed' => 'bg-primary',
                                                'cancelled' => 'bg-secondary'
                                            ][$appointment['status']] ?? 'bg-secondary';
                                            ?>
                                            <span class="badge <?= $badgeClass ?>">
                                                <?= ucfirst($appointment['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($canCancel): ?>
                                                <a href="cancel-appointment.php?id=<?= $appointment['id'] ?>" 
                                                   class="btn btn-sm btn-danger cancel-btn"
                                                   onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                                    Cancel
                                                </a>
                                            <?php elseif ($appointment['status'] === 'cancelled'): ?>
                                                <span class="text-muted">Cancelled</span>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Enhanced cancellation confirmation
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.cancel-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to cancel this appointment?')) {
                e.preventDefault();
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>