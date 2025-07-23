<?php
require_once '../includes/functions.php'; 
require_once '../includes/auth.php';
require_once '../config/database.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// CRUD operations for doctors
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_doctor'])) {
        $name = sanitize($_POST['name']);
        $specialization = sanitize($_POST['specialization']);
        $schedule = sanitize($_POST['schedule']);
        
        $stmt = $pdo->prepare("INSERT INTO doctors (name, specialization, schedule) VALUES (?, ?, ?)");
        $stmt->execute([$name, $specialization, $schedule]);
        
        redirect('doctors.php');
    } elseif (isset($_POST['update_doctor'])) {
        $id = $_POST['id'];
        $name = sanitize($_POST['name']);
        $specialization = sanitize($_POST['specialization']);
        $schedule = sanitize($_POST['schedule']);
        
        $stmt = $pdo->prepare("UPDATE doctors SET name = ?, specialization = ?, schedule = ? WHERE id = ?");
        $stmt->execute([$name, $specialization, $schedule, $id]);
        
        redirect('doctors.php');
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    $stmt = $pdo->prepare("DELETE FROM doctors WHERE id = ?");
    $stmt->execute([$id]);
    
    redirect('doctors.php');
}

// Get all doctors
$doctors = $pdo->query("SELECT * FROM doctors")->fetchAll();

$title = "Manage Doctors";
include '../includes/admin-header.php';
?>

<div class="container mt-4">
    <h2>Manage Doctors</h2>
    
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addDoctorModal">
        Add New Doctor
    </button>
    
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Specialization</th>
                    <th>Schedule</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($doctors as $doctor): ?>
                <tr>
                    <td><?php echo $doctor['id']; ?></td>
                    <td><?php echo $doctor['name']; ?></td>
                    <td><?php echo $doctor['specialization']; ?></td>
                    <td><?php echo $doctor['schedule']; ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning edit-doctor" 
                                data-id="<?php echo $doctor['id']; ?>"
                                data-name="<?php echo htmlspecialchars($doctor['name']); ?>"
                                data-specialization="<?php echo htmlspecialchars($doctor['specialization']); ?>"
                                data-schedule="<?php echo htmlspecialchars($doctor['schedule']); ?>">
                            Edit
                        </button>
                        <a href="doctors.php?delete=<?php echo $doctor['id']; ?>" class="btn btn-sm btn-danger" 
                           onclick="return confirm('Are you sure you want to delete this doctor?')">
                            Delete
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Doctor Modal -->
<div class="modal fade" id="addDoctorModal" tabindex="-1" aria-labelledby="addDoctorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDoctorModalLabel">Add New Doctor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="specialization" class="form-label">Specialization</label>
                        <input type="text" class="form-control" id="specialization" name="specialization" required>
                    </div>
                    <div class="mb-3">
                        <label for="schedule" class="form-label">Schedule</label>
                        <textarea class="form-control" id="schedule" name="schedule" required></textarea>
                        <small class="text-muted">Example: Monday: 09:00-17:00, Tuesday: 08:00-16:00</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="add_doctor">Save Doctor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Doctor Modal -->
<div class="modal fade" id="editDoctorModal" tabindex="-1" aria-labelledby="editDoctorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDoctorModalLabel">Edit Doctor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_specialization" class="form-label">Specialization</label>
                        <input type="text" class="form-control" id="edit_specialization" name="specialization" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_schedule" class="form-label">Schedule</label>
                        <textarea class="form-control" id="edit_schedule" name="schedule" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="update_doctor">Update Doctor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit Doctor
    const editButtons = document.querySelectorAll('.edit-doctor');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_name').value = this.dataset.name;
            document.getElementById('edit_specialization').value = this.dataset.specialization;
            document.getElementById('edit_schedule').value = this.dataset.schedule;
            
            const editModal = new bootstrap.Modal(document.getElementById('editDoctorModal'));
            editModal.show();
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>