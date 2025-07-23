-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 22, 2025 at 06:28 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_klinik`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `user_id`, `doctor_id`, `service_id`, `appointment_date`, `appointment_time`, `status`, `notes`, `created_at`) VALUES
(1, 1, 1, 1, '2023-06-01', '09:00:00', 'completed', 'Kontrol rutin tekanan darah', '2023-05-25 07:30:00'),
(2, 2, 2, 2, '2023-06-01', '11:00:00', 'completed', 'Pemeriksaan gigi rutin', '2023-05-26 03:15:00'),
(3, 3, 3, 3, '2023-06-02', '10:30:00', 'completed', 'Imunisasi DPT', '2023-05-27 02:00:00'),
(4, 4, 4, 4, '2023-06-03', '13:00:00', 'completed', 'USG trimester 2', '2023-05-28 04:20:00'),
(6, 1, 2, 2, '2023-06-10', '15:00:00', 'completed', 'Cabut gigi geraham', '2023-06-01 05:30:00'),
(9, 1, 1, 3, '2025-07-23', '11:00:00', 'completed', 'imunisasi', '2025-07-21 03:57:29'),
(11, 1, 2, 2, '2025-07-22', '11:00:00', 'completed', '', '2025-07-21 04:03:34');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `specialization` varchar(100) NOT NULL,
  `schedule` text NOT NULL,
  `bio` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `name`, `specialization`, `schedule`, `bio`) VALUES
(1, 'Dr. Ahmad Yusril', 'Dokter Umum', 'Senin-Jumat: 08:00-15:00, Sabtu: 08:00-12:00', 'Spesialis penyakit dalam dengan pengalaman 10 tahun'),
(2, 'Dr. Budi Setiawan', 'Dokter Gigi', 'Senin-Rabu: 10:00-17:00, Kamis-Jumat: 13:00-19:00', 'Spesialis gigi dan mulut lulusan Universitas Indonesia'),
(3, 'Dr. Citra Dewi', 'Dokter Anak', 'Selasa-Kamis: 09:00-16:00, Jumat: 08:00-14:00', 'Spesialis anak dengan pendekatan ramah anak'),
(4, 'Dr. Dian Permata', 'Dokter Kandungan', 'Senin-Rabu: 08:00-14:00, Kamis-Sabtu: 10:00-16:00', 'Spesialis kandungan dan kebidanan'),
(8, 'Dr. Cornelia Gisela', 'Dokter Bedah', 'Senin, Rabu, Jumat: 09:00-17:00', NULL),
(9, 'Dr. Mirza Syahputra', 'Psikolog', 'Senin: 09:00-15:00, Selasa-Rabu: 08:00-16:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `price`) VALUES
(1, 'Konsultasi Umum', 'Pemeriksaan kesehatan umum dan konsultasi dokter', 150000.00),
(2, 'Pemeriksaan Gigi', 'Pemeriksaan kesehatan gigi dan mulut', 250000.00),
(3, 'Imunisasi Anak', 'Layanan imunisasi untuk anak usia 0-12 tahun', 350000.00),
(4, 'USG Kandungan', 'Pemeriksaan USG untuk ibu hamil', 350000.00),
(5, 'Tindakan Bedah Minor', 'Prosedur bedah kecil seperti jahit luka', 500000.00),
(9, 'Tes Psikologi', 'Layanan tes psikologi', 200000.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('patient','admin') DEFAULT 'patient',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `role`, `created_at`) VALUES
(1, 'John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08123456781', 'Jl. Merdeka No.1', 'patient', '2023-01-01 03:00:00'),
(2, 'Jane Smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08123456782', 'Jl. Sudirman No.45', 'patient', '2023-01-02 04:00:00'),
(3, 'Robert Johnson', 'robert@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08123456783', 'Jl. Gatot Subroto No.12', 'patient', '2023-01-03 02:30:00'),
(4, 'Sarah Williams', 'sarah@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08123456784', 'Jl. Thamrin No.8', 'patient', '2023-01-05 07:15:00'),
(6, 'Admin Klinik', 'admin@klinik.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08123450000', 'Jl. Administrasi No.1', 'admin', '2023-01-01 01:00:00'),
(7, 'Mirza Syahputra', 'mirzafrsyahputra@gmail.com', '$2y$10$ObS1MbfqP1Y5KWU0bcA.8uj/GmoGsehdUQbSp/Db3kUJypT/NYqRi', '081234393778', NULL, 'patient', '2025-07-21 04:33:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`),
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
