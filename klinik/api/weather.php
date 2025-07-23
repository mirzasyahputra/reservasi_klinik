<?php
include "config.php";

$city = "Malang";
$url = "https://api.openweathermap.org/data/2.5/weather?q=$city,id&units=metric&appid=$apiKey";

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

if ($httpCode == 200 && $response) {
    $data = json_decode($response, true);
    if (isset($data['main'], $data['weather'][0])) {
        $weather = [
            'suhu' => round($data['main']['temp']),
            'kelembapan' => $data['main']['humidity'],
            'kondisi' => ucfirst($data['weather'][0]['description']),
            'icon' => $data['weather'][0]['icon']
        ];
        echo json_encode($weather);
    } else {
        echo json_encode(['error' => 'Data tidak lengkap']);
    }
} else {
    echo json_encode(['error' => 'Gagal mengambil data']);
}
?>