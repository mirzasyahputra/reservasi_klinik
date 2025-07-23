<?php
require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Cek login
if (!isLoggedIn()) {
    redirect('login.php');
}

// Inisialisasi variabel
$error = '';
$doctors = [];
$services = [];

try {
    // Ambil data dokter dan layanan
    $doctors = $pdo->query("SELECT * FROM doctors ORDER BY name")->fetchAll();
    $services = $pdo->query("SELECT * FROM services ORDER BY name")->fetchAll();
    
    if (empty($doctors)) {
        throw new Exception("No doctors available");
    }
    
    if (empty($services)) {
        throw new Exception("No services available");
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $error = "Failed to load doctors and services. Please try again later.";
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validasi input
        $doctor_id = filter_input(INPUT_POST, 'doctor_id', FILTER_VALIDATE_INT);
        $service_id = filter_input(INPUT_POST, 'service_id', FILTER_VALIDATE_INT);
        $date = $_POST['date'] ?? '';
        $time = $_POST['time'] ?? '';
        $notes = sanitize($_POST['notes'] ?? '');

        // Validasi data wajib
        if (!$doctor_id || !$service_id || !$date || !$time) {
            throw new Exception("All fields are required");
        }

        // Validasi tanggal
        $today = new DateTime();
        $appointmentDate = new DateTime($date);
        if ($appointmentDate < $today) {
            throw new Exception("Appointment date must be in the future");
        }

        // Validasi waktu
        $validTimes = ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'];
        if (!in_array($time, $validTimes)) {
            throw new Exception("Invalid appointment time");
        }

        // Cek ketersediaan dokter
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments 
                              WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ?
                              AND status != 'cancelled'");
        $stmt->execute([$doctor_id, $date, $time]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Doctor is not available at the selected time");
        }

        // Buat appointment
        $stmt = $pdo->prepare("INSERT INTO appointments 
                              (user_id, doctor_id, service_id, appointment_date, appointment_time, notes, status) 
                              VALUES (?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([
            $_SESSION['user_id'],
            $doctor_id,
            $service_id,
            $date,
            $time,
            $notes
        ]);

        $_SESSION['success'] = "Appointment booked successfully!";
        redirect('profile.php');

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$title = "Book Appointment";
include 'includes/header.php';
?>

<div class="container mt-4">
    <h2>Book Appointment</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Doctor</label>
                            <select class="form-select" name="doctor_id" required>
                                <option value="">Select Doctor</option>
                                <?php foreach ($doctors as $doctor): ?>
                                    <option value="<?= $doctor['id'] ?>" 
                                        <?= isset($_POST['doctor_id']) && $_POST['doctor_id'] == $doctor['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($doctor['name']) ?> 
                                        (<?= htmlspecialchars($doctor['specialization']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Service</label>
                            <select class="form-select" name="service_id" required>
                                <option value="">Select Service</option>
                                <?php foreach ($services as $service): ?>
                                    <option value="<?= $service['id'] ?>"
                                        <?= isset($_POST['service_id']) && $_POST['service_id'] == $service['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($service['name']) ?> 
                                        (Rp <?= number_format($service['price'], 0, ',', '.') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" class="form-control" name="date" 
                                       min="<?= date('Y-m-d') ?>" 
                                       value="<?= $_POST['date'] ?? '' ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Time</label>
                                <select class="form-select" name="time" required>
                                    <option value="">Select Time</option>
                                    <option value="08:00" <?= isset($_POST['time']) && $_POST['time'] == '08:00' ? 'selected' : '' ?>>08:00 AM</option>
                                    <option value="09:00" <?= isset($_POST['time']) && $_POST['time'] == '09:00' ? 'selected' : '' ?>>09:00 AM</option>
                                    <option value="10:00" <?= isset($_POST['time']) && $_POST['time'] == '10:00' ? 'selected' : '' ?>>10:00 AM</option>
                                    <option value="11:00" <?= isset($_POST['time']) && $_POST['time'] == '11:00' ? 'selected' : '' ?>>11:00 AM</option>
                                    <option value="13:00" <?= isset($_POST['time']) && $_POST['time'] == '13:00' ? 'selected' : '' ?>>01:00 PM</option>
                                    <option value="14:00" <?= isset($_POST['time']) && $_POST['time'] == '14:00' ? 'selected' : '' ?>>02:00 PM</option>
                                    <option value="15:00" <?= isset($_POST['time']) && $_POST['time'] == '15:00' ? 'selected' : '' ?>>03:00 PM</option>
                                    <option value="16:00" <?= isset($_POST['time']) && $_POST['time'] == '16:00' ? 'selected' : '' ?>>04:00 PM</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" name="notes" rows="3"><?= $_POST['notes'] ?? '' ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Book Appointment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>