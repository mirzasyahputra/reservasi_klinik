<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Get statistics
$totalAppointments = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
$totalDoctors = $pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn();
$totalPatients = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'patient'")->fetchColumn();

$title = "Admin Dashboard";
include '../includes/admin-header.php';
?>

<div class="container mt-4">
    <h2>Admin Dashboard</h2>
    
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Appointments</h5>
                    <h1 class="card-text"><?php echo $totalAppointments; ?></h1>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Doctors</h5>
                    <h1 class="card-text"><?php echo $totalDoctors; ?></h1>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Patients</h5>
                    <h1 class="card-text"><?php echo $totalPatients; ?></h1>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-header">
            <h5>Quick Links</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <a href="doctors.php" class="btn btn-outline-primary w-100 mb-2">Manage Doctors</a>
                </div>
                <div class="col-md-4">
                    <a href="appointments.php" class="btn btn-outline-primary w-100 mb-2">Manage Appointments</a>
                </div>
                <div class="col-md-4">
                    <a href="users.php" class="btn btn-outline-primary w-100 mb-2">Manage Users</a>
                </div>
                <div class="col-md-4">
                    <a href="services.php" class="btn btn-outline-primary w-100 mb-2">Manage Services</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>