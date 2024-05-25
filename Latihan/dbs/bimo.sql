-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2023 at 05:28 AM
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
-- Database: `ridho`
--

-- --------------------------------------------------------

--
-- Table structure for table `lantai`
--

CREATE TABLE `lantai` (
  `id_lantai` int(11) NOT NULL,
  `nama_lantai` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lantai`
--

INSERT INTO `lantai` (`id_lantai`, `nama_lantai`) VALUES
(18, 'Lantai 1');

-- --------------------------------------------------------

--
-- Table structure for table `merk`
--

CREATE TABLE `merk` (
  `id_merk` int(11) NOT NULL,
  `nama_merk` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `merk`
--

INSERT INTO `merk` (`id_merk`, `nama_merk`) VALUES
(1, 'TP-LINK');

-- --------------------------------------------------------

--
-- Table structure for table `model`
--

CREATE TABLE `model` (
  `id_model` int(11) NOT NULL,
  `nama_model` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `model`
--

INSERT INTO `model` (`id_model`, `nama_model`) VALUES
(1, 'esp2866'),
(2, 'Modem'),
(3, 'Router'),
(4, 'Switch');

-- --------------------------------------------------------

--
-- Table structure for table `perangkat_jaringan`
--

CREATE TABLE `perangkat_jaringan` (
  `id_perangkat` int(11) NOT NULL,
  `nama_perangkat` varchar(255) NOT NULL,
  `id_ruangan` int(11) NOT NULL,
  `id_lantai` int(11) DEFAULT NULL,
  `id_model` int(11) DEFAULT NULL,
  `id_merk` int(11) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `keterangan_perangkat` enum('Aktif','Tidak Aktif') DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `perangkat_jaringan`
--

INSERT INTO `perangkat_jaringan` (`id_perangkat`, `nama_perangkat`, `id_ruangan`, `id_lantai`, `id_model`, `id_merk`, `jumlah`, `keterangan_perangkat`) VALUES
(93, '', 88, NULL, 1, 1, 2, 'Aktif');

-- --------------------------------------------------------

--
-- Table structure for table `ruangan`
--

CREATE TABLE `ruangan` (
  `id_ruangan` int(11) NOT NULL,
  `id_lantai` int(11) DEFAULT NULL,
  `nama_ruangan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ruangan`
--

INSERT INTO `ruangan` (`id_ruangan`, `id_lantai`, `nama_ruangan`) VALUES
(88, 18, '1.1');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_users` int(11) NOT NULL,
  `nama_admin` varchar(255) NOT NULL,
  `kode_admin` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_users`, `nama_admin`, `kode_admin`) VALUES
(1, 'admin', '123');

-- --------------------------------------------------------

--
-- Table structure for table `waktu`
--

CREATE TABLE `waktu` (
  `id_waktu` int(11) NOT NULL,
  `id_users` int(11) DEFAULT NULL,
  `waktu_login` datetime DEFAULT NULL,
  `waktu_logout` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `waktu`
--

INSERT INTO `waktu` (`id_waktu`, `id_users`, `waktu_login`, `waktu_logout`) VALUES
(1, 1, '2023-11-27 11:17:30', '2023-11-27 11:18:22'),
(2, 1, '2023-11-27 11:18:24', NULL),
(3, 1, '2023-11-27 11:18:30', NULL),
(4, 1, '2023-11-27 11:20:58', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lantai`
--
ALTER TABLE `lantai`
  ADD PRIMARY KEY (`id_lantai`),
  ADD UNIQUE KEY `nama` (`nama_lantai`);

--
-- Indexes for table `merk`
--
ALTER TABLE `merk`
  ADD PRIMARY KEY (`id_merk`),
  ADD UNIQUE KEY `nama` (`nama_merk`);

--
-- Indexes for table `model`
--
ALTER TABLE `model`
  ADD PRIMARY KEY (`id_model`),
  ADD UNIQUE KEY `nama` (`nama_model`);

--
-- Indexes for table `perangkat_jaringan`
--
ALTER TABLE `perangkat_jaringan`
  ADD PRIMARY KEY (`id_perangkat`),
  ADD KEY `id_ruangan` (`id_ruangan`),
  ADD KEY `id_model` (`id_model`),
  ADD KEY `id_merk` (`id_merk`),
  ADD KEY `id_lantai` (`id_lantai`);

--
-- Indexes for table `ruangan`
--
ALTER TABLE `ruangan`
  ADD PRIMARY KEY (`id_ruangan`),
  ADD UNIQUE KEY `nama` (`nama_ruangan`),
  ADD UNIQUE KEY `nama_ruangan` (`nama_ruangan`),
  ADD KEY `id_lantai` (`id_lantai`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_users`);

--
-- Indexes for table `waktu`
--
ALTER TABLE `waktu`
  ADD PRIMARY KEY (`id_waktu`),
  ADD KEY `id_users` (`id_users`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lantai`
--
ALTER TABLE `lantai`
  MODIFY `id_lantai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `merk`
--
ALTER TABLE `merk`
  MODIFY `id_merk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `model`
--
ALTER TABLE `model`
  MODIFY `id_model` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `perangkat_jaringan`
--
ALTER TABLE `perangkat_jaringan`
  MODIFY `id_perangkat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `ruangan`
--
ALTER TABLE `ruangan`
  MODIFY `id_ruangan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_users` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `waktu`
--
ALTER TABLE `waktu`
  MODIFY `id_waktu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `perangkat_jaringan`
--
ALTER TABLE `perangkat_jaringan`
  ADD CONSTRAINT `fk_ruangan` FOREIGN KEY (`id_ruangan`) REFERENCES `ruangan` (`id_ruangan`) ON DELETE CASCADE,
  ADD CONSTRAINT `perangkat_jaringan_ibfk_1` FOREIGN KEY (`id_ruangan`) REFERENCES `ruangan` (`id_ruangan`),
  ADD CONSTRAINT `perangkat_jaringan_ibfk_2` FOREIGN KEY (`id_model`) REFERENCES `model` (`id_model`),
  ADD CONSTRAINT `perangkat_jaringan_ibfk_3` FOREIGN KEY (`id_merk`) REFERENCES `merk` (`id_merk`),
  ADD CONSTRAINT `perangkat_jaringan_ibfk_4` FOREIGN KEY (`id_lantai`) REFERENCES `lantai` (`id_lantai`);

--
-- Constraints for table `ruangan`
--
ALTER TABLE `ruangan`
  ADD CONSTRAINT `fk_lantai` FOREIGN KEY (`id_lantai`) REFERENCES `lantai` (`id_lantai`) ON DELETE CASCADE,
  ADD CONSTRAINT `ruangan_ibfk_1` FOREIGN KEY (`id_lantai`) REFERENCES `lantai` (`id_lantai`);

--
-- Constraints for table `waktu`
--
ALTER TABLE `waktu`
  ADD CONSTRAINT `waktu_ibfk_1` FOREIGN KEY (`id_users`) REFERENCES `users` (`id_users`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
