<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

$stmt = $pdo->query("SELECT * FROM services");
$services = $stmt->fetchAll();

$title = "Our Services";
include 'includes/header.php';
?>

<div class="container mt-4">
    <h2 class="text-center mb-4">Our Services</h2>
    
    <div class="row">
        <?php foreach ($services as $service): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="card-title"><?php echo $service['name']; ?></h4>
                    <p class="card-text"><?php echo $service['description']; ?></p>
                    <h5 class="text-primary">Rp <?php echo number_format($service['price'], 0, ',', '.'); ?></h5>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="booking.php?service=<?php echo $service['id']; ?>" class="btn btn-primary">Book Now</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>