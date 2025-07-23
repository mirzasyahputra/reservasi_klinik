<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

$stmt = $pdo->query("SELECT * FROM services LIMIT 3");
$services = $stmt->fetchAll();

$title = "Home";
include 'includes/header.php';
?>

<div class="hero-section">
    <h1>Welcome to Klinik Sehat</h1>
    <p class="lead">Reservasi klinik secara online dengan mudah</p>
    <a href="booking.php" class="btn btn-primary btn-lg">Book Now</a>
    <br></br>
    
    <!-- Widget Cuaca dipindah ke sini -->
    <div class="weather-container">
        <div class="weather-widget" id="weatherWidget">
            <div class="spinner-border text-light" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <h2 class="text-center mb-4">Our Services</h2>
    <div class="row">
        <?php foreach ($services as $service): ?>
        <div class="col-md-4">
            <div class="card service-card">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($service['name']) ?></h5>
                    <p class="card-text"><?= substr(htmlspecialchars($service['description']), 0, 100) ?>...</p>
                    <a href="services.php" class="btn btn-primary">Learn More</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
fetch('api/weather.php')
    .then(response => response.json())
    .then(data => {
        const widget = document.getElementById('weatherWidget');
        
        if (!data.error) {
            widget.innerHTML = `
                <div class="weather-card">
                    <h4>Cuaca di Malang</h4>
                    <div class="weather-detail">
                        <img src="https://openweathermap.org/img/w/${data.icon}.png" 
                             alt="${data.kondisi}">
                        <span class="temperature">${data.suhu}°C</span>
                    </div>
                    <p class="condition">${data.kondisi}</p>
                    <p class="humidity">Kelembapan: ${data.kelembapan}%</p>
                </div>
            `;
        } else {
            widget.innerHTML = '<p class="text-warning">Data cuaca tidak tersedia</p>';
        }
    })
    .catch(error => {
        document.getElementById('weatherWidget').innerHTML = `
            <p class="text-warning">Gagal memuat data cuaca</p>
        `;
    });
</script>
<?php include 'includes/footer.php'; ?>