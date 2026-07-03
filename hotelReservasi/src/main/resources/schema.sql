-- =============================================
-- Schema Database untuk Aplikasi Reservasi Hotel
-- Spring Boot & Spring Data JPA
-- Ahmad Zidan - Tugas Pemrograman 2
-- =============================================

-- Buat database jika belum ada
CREATE DATABASE IF NOT EXISTS `hotel_reservasi` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `hotel_reservasi`;

-- Buat tabel reservasi sesuai spesifikasi Entity POJO
DROP TABLE IF EXISTS `reservasi`;
CREATE TABLE IF NOT EXISTS `reservasi` (
    `id` BIGINT NOT NULL AUTO_INCREMENT,
    `nama_tamu` VARCHAR(255) NOT NULL,
    `tipe_kamar` VARCHAR(100) NOT NULL,
    `nomor_kamar` VARCHAR(50) DEFAULT NULL,
    `tanggal_check_in` DATE NOT NULL,
    `tanggal_check_out` DATE NOT NULL,
    `status` VARCHAR(50) NOT NULL DEFAULT 'AKTIF',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Menambahkan data dummy awal untuk pengujian
INSERT INTO `reservasi` (`nama_tamu`, `tipe_kamar`, `nomor_kamar`, `tanggal_check_in`, `tanggal_check_out`, `status`) 
VALUES 
('Zidan Ahmad', 'Deluxe', 'D-1', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'AKTIF'),
('Budi Santoso', 'Standard', 'S-1', DATE_ADD(CURDATE(), INTERVAL 1 DAY), DATE_ADD(CURDATE(), INTERVAL 3 DAY), 'AKTIF'),
('Siti Aminah', 'Suite', 'U-1', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 DAY), 'SELESAI'),
('Ahmad Fauzi', 'Standard', 'S-2', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 3 DAY), 'AKTIF'),
('Dewi Lestari', 'Deluxe', 'D-2', DATE_ADD(CURDATE(), INTERVAL 2 DAY), DATE_ADD(CURDATE(), INTERVAL 4 DAY), 'AKTIF'),
('Rendi Wijaya', 'Suite', 'U-2', DATE_ADD(CURDATE(), INTERVAL 1 DAY), DATE_ADD(CURDATE(), INTERVAL 3 DAY), 'AKTIF'),
('Megawati Putri', 'President Suite', 'P-1', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 5 DAY), 'AKTIF'),
('Hendra Kurniawan', 'Standard', 'S-3', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'SELESAI'),
('Lilis Suryani', 'Deluxe', 'D-3', DATE_ADD(CURDATE(), INTERVAL 3 DAY), DATE_ADD(CURDATE(), INTERVAL 5 DAY), 'AKTIF'),
('Aditya Pratama', 'Standard', 'S-4', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 DAY), 'SELESAI');

