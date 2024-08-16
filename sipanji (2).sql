-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 14, 2024 at 11:58 AM
-- Server version: 10.4.18-MariaDB
-- PHP Version: 7.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sipanji`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(225) NOT NULL,
  `alamat` varchar(225) NOT NULL,
  `tempat_lahir` varchar(225) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `foto` varchar(225) NOT NULL,
  `peran` enum('admin','kominfo','instansi','umum') NOT NULL,
  `telepon` int(20) NOT NULL,
  `status` varchar(20) NOT NULL,
  `login_terakhir` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `email`, `alamat`, `tempat_lahir`, `tanggal_lahir`, `foto`, `peran`, `telepon`, `status`, `login_terakhir`) VALUES
(17, 'irwan', 'irwan', 'irwanfirdaus107@gmail.com', 'Cempaka', 'Cempaka', '2024-07-19', '66baae509876d.jpg', 'admin', 2147483647, 'diterima', '2024-08-14 09:39:49'),
(18, 'yanda', 'yanda', 'admin@gmail.com', 'Cempaka', 'cempaka', '2024-07-24', '66a719896c2f1.jpg', 'umum', 2147483647, 'diterima', '2024-08-14 06:42:45'),
(19, 'iqbal', 'iqbal', 'iqbal@gmail.com', 'CEMPAKA', 'Cempaka', '2003-05-05', '66a878a9cb6c8.jpg', 'instansi', 2147483647, 'diterima', '2024-08-13 07:55:15'),
(20, 'user1', 'user1', 'user@gmail.com', 'CEMPAKA', 'cempaka', '2021-02-02', '66aa173b76a4e.jpg', 'umum', 2147483647, 'diterima', '2024-08-08 11:56:45'),
(21, 'kominfo', 'kominfo', 'admin@gmail.com', 'pelaihari', 'cempaka', '2024-08-07', '66b56cb6b242d.png', 'kominfo', 2147483647, 'diterima', '2024-08-13 07:56:26'),
(22, 'nanda123', 'nanda123', 'nanda123@gmail.com', 'Jl. Mistar Cokrokusumo Kelurahan Cempaka, Kecamatan Cempaka, No 21 (Seberang Kelurahan Cempaka)', 'Cempaka', '2004-06-07', '66bbe55d63061.png', 'umum', 2147483647, 'diterima', '2024-08-14 07:54:59'),
(23, 'putri', 'putri', 'putri@gmail.com', 'j', 'pelaihari', '2024-08-06', '66bbe5a419658.jpeg', 'umum', 2147483647, 'diterima', '2024-08-14 09:29:34');

-- --------------------------------------------------------

--
-- Table structure for table `alat`
--

CREATE TABLE `alat` (
  `id` int(11) NOT NULL,
  `tempat` varchar(255) NOT NULL,
  `device_id` varchar(255) NOT NULL,
  `kerusakan` varchar(225) NOT NULL,
  `status` enum('terhubung','terputus') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `alat`
--

INSERT INTO `alat` (`id`, `tempat`, `device_id`, `kerusakan`, `status`) VALUES
(38, 'SMPN 3 Banjarbaru', '198.168.10.1', '-', 'terhubung'),
(39, 'Puskesmas Cempaka', '198.168.10.2', 'kabel lan putus', 'terputus'),
(40, 'Kantor Kelurahan Cempaka', '198.168.10.3', '-', 'terhubung'),
(41, 'Taman VanderVil', '198.168.10.4', 'Poe Terbakar', 'terputus'),
(42, 'Puskesmas Guntung Manggis', '198.168.10.5', '-', 'terhubung');

-- --------------------------------------------------------

--
-- Table structure for table `data_pengajuan`
--

CREATE TABLE `data_pengajuan` (
  `id` int(11) NOT NULL,
  `no_pengajuan` varchar(255) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `tempat` varchar(100) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `nama_perwakilan` varchar(100) NOT NULL,
  `no_telpon` varchar(15) NOT NULL,
  `tgl_masuk` datetime NOT NULL,
  `surat_pengajuan` varchar(255) NOT NULL,
  `koordinat` varchar(225) NOT NULL,
  `foto` varchar(255) NOT NULL,
  `status` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `data_pengajuan`
--

INSERT INTO `data_pengajuan` (`id`, `no_pengajuan`, `kategori`, `tempat`, `alamat`, `nama_perwakilan`, `no_telpon`, `tgl_masuk`, `surat_pengajuan`, `koordinat`, `foto`, `status`, `user_id`) VALUES
(49, 'PNJ-1723525934', 'sekolahan', 'SMPN 3 Banjarbaru', 'Jl. Mistar Cokrokusumo Kelurahan Cempaka, Kecamatan Cempaka, No 21 (Seberang Kelurahan Cempaka)', 'Muhammad Hasannudin', '082151518785', '2024-08-02 13:11:00', 'BARU_PEDOMAN-PKL-FTI-Versi-2.0-Edit_2020.pdf', 'https://www.google.com/maps?q=-3.4784242,114.8505152', 'Screenshot (475).png', 'diterima', 18),
(50, 'PNJ-1723526133', 'puskesmas', 'Puskesmas Cempaka', 'Jl. Mistar Cokrokusumo Kelurahan Sungai Tiung, Kecamatan Cempaka', 'Anindiya Putri', '081252867234', '2024-08-14 13:14:00', 'BARU_PEDOMAN-PKL-FTI-Versi-2.0-Edit_2020.pdf', 'https://www.google.com/maps?q=-3.46475958222967,114.83116149902345', 'puskes cempaka.jfif', 'diterima', 18),
(51, 'PNJ-1723526323', 'perkantoran', 'Kantor Kelurahan Cempaka', 'Jl. Mistar Cokrokusumo Kelurahan Cempaka, Kecamatan Cempaka (Seberang SMPN 3 Banjarbaru)', 'Muhammad Iqbal', '081353545756', '2024-08-15 13:18:00', 'PHP CRUD KILAT DENGAN EMMET.pdf', 'https://www.google.com/maps?q=-3.4877198824272,114.85347747802736', '800px-Kantor_Kelurahan_Cempaka,_Banjarbaru.jfif', 'diterima', 18),
(53, 'PNJ-1723526728', 'publik', 'Taman VanderVil', 'Jl.Pangeran Suriansyah, Loktabat Utara, Kec. Banjarbaru Utara', 'abdul Rahman', '081252867234', '2024-08-14 13:25:00', 'PHP CRUD KILAT DENGAN EMMET.pdf', 'https://www.google.com/maps?q=-3.444883051464298,114.80987548828126', 'Taman-Van-Der-Pijl-Banjarbaru.jpg', 'diterima', 18),
(54, 'PNJ-1723589203', 'puskesmas', 'Puskesmas Guntung Manggis', ' Jl. Guntung Paring Komplek Agis Residence, Kelurahan Guntung Manggis, Kecamatan Landasan Ulin, Kota Banjarbaru', 'Nabila Putri', '081236478432', '2024-08-13 06:44:00', 'Laporan Data Pengajuan (2).pdf', 'https://www.google.com/maps?q=-3.4784265,114.8505665', 'puskesmas guntung manggis.jpg', 'diterima', 18),
(55, 'PNJ-1723589336', 'perkantoran', 'Balai Kota Banjarbaru', 'Jl. Panglima Batur Timur No.1, Loktabat Utara, Kec. Banjarbaru Utara, Kota Banjar Baru,', 'Nanda Puji Astuti', '081234567891', '2024-08-15 06:48:00', 'Laporan Data Pengajuan (2).pdf', 'https://www.google.com/maps?q=-3.3186067,114.5943784', 'Balai Kota BJB.jpeg', 'diterima', 18),
(56, 'PNJ-1723599329', 'sekolahan', 'SMPN 13 Banjarbaru', 'Jalan HM Mistar Cokro Kusumo, Bangkal, Kec. Cemp., Kota Banjar Baru,', 'Khalimatus Sya\'diah Putri', '082154678934', '2024-08-22 09:31:00', 'Laporan Stok Barang (2).pdf', 'https://www.google.com/maps?q=-3.480523430587774,114.83322143554689', 'Screenshot (476).png', 'diterima', 23),
(57, 'PNJ-1723599577', 'publik', 'RTH Bumi Cahaya Bintang', 'Loktabat Sel., Kec. Banjarbaru Selatan, Kota Banjar Baru(Komplek Bumi Cahaya Bintang)', 'Khalimatus Sya\'diah Putri', '081351518182', '2024-08-16 09:39:00', 'BARU_PEDOMAN-PKL-FTI-Versi-2.0-Edit_2020.pdf', 'https://www.google.com/maps?q=-3.3186067,114.5943784', 'RTH-Bumi-Cahaya-Bintang.jpg', 'diterima', 23);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `no_pengajuan` varchar(100) NOT NULL,
  `title` varchar(100) CHARACTER SET latin1 NOT NULL,
  `tanggal` datetime NOT NULL,
  `keterangan` varchar(255) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `no_pengajuan`, `title`, `tanggal`, `keterangan`) VALUES
(123, 'PNJ-1723525934', 'SMPN 3 Banjarbaru', '2024-08-01 21:06:00', 'pemeliharaan'),
(124, 'PNJ-1723526133', 'Puskesmas Cempaka', '2024-08-20 21:13:00', 'pemasangan'),
(125, 'PNJ-1723526323', 'Kantor Kelurahan Cempaka', '2024-08-16 21:24:00', 'survey'),
(126, 'PNJ-1723526728', 'Taman VanderVil', '2024-08-15 21:28:00', 'pemasangan'),
(127, 'PNJ-1723526323', 'Kantor Kelurahan Cempaka', '2024-08-15 21:33:00', 'pemasangan'),
(128, 'PNJ-1723526323', 'Kantor Kelurahan Cempaka', '2024-08-08 21:42:00', 'survey'),
(129, 'PNJ-1723526728', 'Taman VanderVil', '2024-08-14 21:43:00', 'pemasangan'),
(132, 'PNJ-1723589336', 'Balai Kota Banjarbaru', '2024-08-22 07:43:00', 'pemeliharaan');

-- --------------------------------------------------------

--
-- Table structure for table `gangguan`
--

CREATE TABLE `gangguan` (
  `id` int(11) NOT NULL,
  `no_pengajuan` varchar(100) NOT NULL,
  `nama_tempat` varchar(100) NOT NULL,
  `perwakilan` varchar(100) NOT NULL,
  `nomor_telepon` varchar(15) NOT NULL,
  `keterangan` varchar(225) NOT NULL,
  `tgl_masuk` datetime NOT NULL,
  `foto_kerusakan` varchar(225) NOT NULL,
  `status` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `gangguan`
--

INSERT INTO `gangguan` (`id`, `no_pengajuan`, `nama_tempat`, `perwakilan`, `nomor_telepon`, `keterangan`, `tgl_masuk`, `foto_kerusakan`, `status`, `user_id`) VALUES
(2, 'GGN-1723350724', 'Diskominfo Kota Banjarbaru', 'irwan', '088182828282', 'wifi nya mati', '2024-08-23 12:31:00', 'Taman-Van-Der-Pijl-Banjarbaru.jpg', 'ditolak', 18),
(4, 'GGN-1723350784', 'Diskominfo Kota Banjarbaru', 'irwan', '088182828282', 'wifi nya mati', '2024-08-23 12:31:00', 'Taman-Van-Der-Pijl-Banjarbaru.jpg', 'ditolak', 18),
(6, 'GGN-1723351141', 'Diskominfo Kota Banjarbaru', 'irwan', '088182828282', 'sampai soreee', '2024-08-23 12:38:00', 'Balai Kota BJB.jpeg', 'ditolak', 18);

-- --------------------------------------------------------

--
-- Table structure for table `kecepatan_jaringan`
--

CREATE TABLE `kecepatan_jaringan` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `download` float NOT NULL,
  `upload` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `kepuasan_pelayanan`
--

CREATE TABLE `kepuasan_pelayanan` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `penilaian` int(11) DEFAULT NULL CHECK (`penilaian` between 1 and 5),
  `komentar` text DEFAULT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kepuasan_pelayanan`
--

INSERT INTO `kepuasan_pelayanan` (`id`, `username`, `email`, `penilaian`, `komentar`, `tanggal`) VALUES
(3, 'yanda', 'admin@gmail.com', 3, 'Jaringannya mati mati di SMPN 3 Banjarbaru', '2024-08-08 02:05:15'),
(5, 'putri', 'putri@gmail.com', 4, 'Jaringan di taman vandervil kurang kuat', '2024-08-13 23:54:34'),
(6, 'nanda123', 'nanda123@gmail.com', 4, 'banyak kabel lan menjuntai di smpn 3 banjarbaru', '2024-08-13 23:55:22'),
(7, 'putri', 'putri@gmail.com', 2, 'jaringan di smp 13 lelet', '2024-08-14 01:37:04');

-- --------------------------------------------------------

--
-- Table structure for table `pengerjaan`
--

CREATE TABLE `pengerjaan` (
  `id_pengerjaan` int(11) NOT NULL,
  `no_pengajuan` varchar(100) NOT NULL,
  `tempat` varchar(100) NOT NULL,
  `tanggal` datetime NOT NULL,
  `nama_pemasang` varchar(100) NOT NULL,
  `biaya_tambahan` varchar(100) NOT NULL,
  `alat` varchar(100) NOT NULL,
  `stok_terpakai` bigint(20) NOT NULL,
  `foto_pengerjaan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pengerjaan`
--

INSERT INTO `pengerjaan` (`id_pengerjaan`, `no_pengajuan`, `tempat`, `tanggal`, `nama_pemasang`, `biaya_tambahan`, `alat`, `stok_terpakai`, `foto_pengerjaan`) VALUES
(48, 'PNJ-1723525934', 'SMPN 3 Banjarbaru', '2024-08-07 21:09:00', 'IRWAN', '150000', 'POE', 1, 'IMG_20231122_103346.jpg'),
(49, 'PNJ-1723526133', 'Puskesmas Cempaka', '2024-08-14 21:13:00', 'Muhammad Irwan Firdaus', '150000', 'kabel lan', 100, 'IMG_20231122_162717.jpg'),
(52, 'PNJ-1723526323', 'Kantor Kelurahan Cempaka', '2024-08-04 21:24:00', 'irwan', '150000', 'kabel lan', 34, 'IMG_20231122_172822.jpg'),
(53, 'PNJ-1723526728', 'Taman VanderVil', '2024-08-22 21:29:00', 'IRWAN', '150000', 'Connector Rj 45', 5, 'IMG_20231122_161457.jpg'),
(54, 'PNJ-1723526323', 'Kantor Kelurahan Cempaka', '2024-08-23 21:34:00', 'adul', '150000', 'POE', 5, 'IMG_20231122_162730.jpg'),
(57, 'PNJ-1723526728', 'Taman VanderVil', '2024-08-15 21:45:00', 'Irwan', '150000', 'POE', 5, 'IMG-20231113-WA0012.jpg'),
(58, 'PNJ-1723526728', 'Taman VanderVil', '2024-08-15 21:45:00', 'Irwan', '150000', 'Connector Rj 45', 4, 'IMG-20231113-WA0011.jpg'),
(59, 'PNJ-1723526323', 'Kantor Kelurahan Cempaka', '2024-08-14 22:37:00', 'IRWAN', '150000', 'POE', 5, 'IMG_20231122_162746.jpg'),
(60, 'PNJ-1723526323', 'Kantor Kelurahan Cempaka', '2024-08-14 22:37:00', 'IRWAN', '150000', 'kabel lan', 10, 'IMG-20231113-WA0011.jpg'),
(61, 'PNJ-1723526323', 'Kantor Kelurahan Cempaka', '2024-08-14 22:37:00', 'IRWAN', '150000', 'Connector Rj 45', 2, 'IMG_20231123_141648.jpg'),
(62, 'PNJ-1723589336', 'Balai Kota Banjarbaru', '2024-08-15 07:43:00', 'Muhammad Iqbal', '150000', 'kabel lan', 50, 'IMG_20231113_114115.jpg'),
(63, 'PNJ-1723589336', 'Balai Kota Banjarbaru', '2024-08-15 07:43:00', 'Muhammad Iqbal', '150000', 'Connector Rj 45', 3, 'IMG-20231113-WA0009.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `stok_alat`
--

CREATE TABLE `stok_alat` (
  `id_stok` int(11) NOT NULL,
  `nama_alat` varchar(100) NOT NULL,
  `jumlah` bigint(20) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `foto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `stok_alat`
--

INSERT INTO `stok_alat` (`id_stok`, `nama_alat`, `jumlah`, `harga`, `foto`) VALUES
(8, 'POE', 775, '100000.00', 'IMG_20231206_182417_063.jpg'),
(9, 'kabel lan', 603, '2000.00', '9802508_baaf06e0-4eed-49a5-87c0-a83b93d544b8_300_300.jpg'),
(10, 'Connector Rj 45', 986, '3000.00', 'conector rj 45.jpg'),
(13, 'toolbox', 10, '50000.00', 'toolbox.jpg'),
(15, 'netally', 1, '25000000.00', 'netally.jpg'),
(16, 'Tang Kabel lan', 5, '150000.00', 'tang kabel lan.jpg'),
(17, 'access point', 200, '1200000.00', 'TPLINK-Access-Points-EAP620-HD-04c26bde0e10478e89fbc5c04f28b44a_medium.jpg'),
(18, 'kabel tis', 10000, '500.00', 'kabel tis.jpg'),
(19, 'switch', 100, '2000000.00', 'switch.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `alat`
--
ALTER TABLE `alat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_pengajuan`
--
ALTER TABLE `data_pengajuan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `no_pengajuan` (`no_pengajuan`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `no_pengajuan` (`no_pengajuan`);

--
-- Indexes for table `gangguan`
--
ALTER TABLE `gangguan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kecepatan_jaringan`
--
ALTER TABLE `kecepatan_jaringan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kepuasan_pelayanan`
--
ALTER TABLE `kepuasan_pelayanan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengerjaan`
--
ALTER TABLE `pengerjaan`
  ADD PRIMARY KEY (`id_pengerjaan`);

--
-- Indexes for table `stok_alat`
--
ALTER TABLE `stok_alat`
  ADD PRIMARY KEY (`id_stok`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `alat`
--
ALTER TABLE `alat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `data_pengajuan`
--
ALTER TABLE `data_pengajuan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `gangguan`
--
ALTER TABLE `gangguan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `kecepatan_jaringan`
--
ALTER TABLE `kecepatan_jaringan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `kepuasan_pelayanan`
--
ALTER TABLE `kepuasan_pelayanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pengerjaan`
--
ALTER TABLE `pengerjaan`
  MODIFY `id_pengerjaan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `stok_alat`
--
ALTER TABLE `stok_alat`
  MODIFY `id_stok` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
