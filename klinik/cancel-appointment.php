<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Pastikan user sudah login
if (!isLoggedIn()) {
    redirect('/login.php');
}

// Validasi parameter ID
$appointment_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$appointment_id) {
    $_SESSION['error'] = "Invalid appointment ID";
    redirect('../profile.php');
}

try {
    // Dapatkan data appointment
    $stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ? AND user_id = ?");
    $stmt->execute([$appointment_id, $_SESSION['user_id']]);
    $appointment = $stmt->fetch();

    // Validasi appointment
    if (!$appointment) {
        $_SESSION['error'] = "Appointment not found or you don't have permission to cancel it";
        redirect('../profile.php');
    }

    // Validasi status appointment
    if (!in_array($appointment['status'], ['pending', 'confirmed'])) {
        $_SESSION['error'] = "Only pending or confirmed appointments can be cancelled";
        redirect('../profile.php');
    }

    // Validasi tanggal appointment (tidak bisa membatalkan appointment yang sudah lewat)
    $appointment_date = new DateTime($appointment['appointment_date'] . ' ' . $appointment['appointment_time']);
    $now = new DateTime();
    if ($appointment_date < $now) {
        $_SESSION['error'] = "Cannot cancel past appointments";
        redirect('../profile.php');
    }

    // Update status appointment
    $stmt = $pdo->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ?");
    $stmt->execute([$appointment_id]);

    $_SESSION['success'] = "Appointment has been cancelled successfully";
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $_SESSION['error'] = "Failed to cancel appointment. Please try again.";
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

redirect('./profile.php');