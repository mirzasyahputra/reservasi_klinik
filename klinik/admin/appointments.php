<?php
require_once '../includes/functions.php';
require_once '../includes/auth.php';
require_once '../config/database.php';

// Check admin role
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Update appointment status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $appointmentId = (int)$_POST['appointment_id'];
    $newStatus = sanitize($_POST['status']);
    
    $validStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];
    
    if (!in_array($newStatus, $validStatuses)) {
        $_SESSION['error'] = "Invalid status";
        header("Location: appointments.php");
        exit();
    }

    try {
        // Check if appointment exists
        $checkStmt = $pdo->prepare("SELECT id FROM appointments WHERE id = ?");
        $checkStmt->execute([$appointmentId]);
        
        if ($checkStmt->rowCount() === 0) {
            $_SESSION['error'] = "Appointment not found";
            header("Location: appointments.php");
            exit();
        }

        // Update status
        $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $appointmentId]);
        
        $_SESSION['success'] = "Appointment status updated successfully";
        
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to update status: " . $e->getMessage();
    }
    
    header("Location: appointments.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
    $stmt->execute([$id]);
    
    $_SESSION['success'] = "Appointment deleted successfully!";
    header("Location: appointments.php");
    exit();
}

// Get all appointments with user and doctor info
try {
    $appointments = $pdo->query("
        SELECT a.*, u.name as patient_name, d.name as doctor_name, s.name as service_name
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        JOIN doctors d ON a.doctor_id = d.id
        JOIN services s ON a.service_id = s.id
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ")->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = "Failed to fetch appointments: " . $e->getMessage();
    $appointments = [];
}

$title = "Manage Appointments";
include '../includes/admin-header.php';
?>

<div class="container mt-4">
    <h2>Manage Appointments</h2>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Service</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($appointments)): ?>
                    <tr>
                        <td colspan="7" class="text-center">No appointments found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($appointment['id']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['service_name']); ?></td>
                        <td>
                            <?php echo htmlspecialchars(date('M j, Y', strtotime($appointment['appointment_date']))); ?><br>
                            <?php echo htmlspecialchars(date('h:i A', strtotime($appointment['appointment_time']))); ?>
                        </td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                <input type="hidden" name="update_status" value="1">
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="pending" <?php echo $appointment['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo $appointment['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="completed" <?php echo $appointment['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo $appointment['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <a href="#" class="btn btn-sm btn-info view-notes" 
                               data-notes="<?php echo htmlspecialchars($appointment['notes']); ?>">
                                View Notes</a>
                            <a href="?delete=<?= $appointment['id'] ?>" class="btn btn-sm btn-danger" 
                            onclick="return confirm('Yakin ingin menghapus appointment ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Notes Modal -->
<div class="modal fade" id="notesModal" tabindex="-1" aria-labelledby="notesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notesModalLabel">Appointment Notes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="notesContent">
                <!-- Notes will be inserted here by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show notes in modal
    const viewButtons = document.querySelectorAll('.view-notes');
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const notes = this.dataset.notes || 'No notes available';
            document.getElementById('notesContent').textContent = notes;
            
            const notesModal = new bootstrap.Modal(document.getElementById('notesModal'));
            notesModal.show();
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>