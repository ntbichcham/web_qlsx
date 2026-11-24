-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 12, 2026 at 04:43 AM
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
-- Database: `qlphongkhamnhakhoa`
--

-- --------------------------------------------------------

--
-- Table structure for table `bac_si`
--

CREATE TABLE `bac_si` (
  `MaBS` int(11) NOT NULL,
  `MaND` int(11) NOT NULL,
  `HoTen` varchar(100) NOT NULL,
  `SDT` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `ChuyenMon` varchar(100) DEFAULT NULL,
  `GioiThieu` longtext DEFAULT NULL,
  `TrangThai` varchar(20) NOT NULL DEFAULT 'HOAT_DONG'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `benh_nhan`
--

CREATE TABLE `benh_nhan` (
  `MaBN` int(11) NOT NULL,
  `MaND` int(11) DEFAULT NULL,
  `HoTen` varchar(100) NOT NULL,
  `SDT` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `DiaChi` longtext DEFAULT NULL,
  `GioiTinh` varchar(10) DEFAULT NULL,
  `NgaySinh` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_don_thuoc`
--

CREATE TABLE `chi_tiet_don_thuoc` (
  `MaCTDT` int(11) NOT NULL,
  `MaDonThuoc` int(11) NOT NULL,
  `TenThuoc` varchar(200) NOT NULL,
  `SoLuong` decimal(10,2) NOT NULL,
  `LieuLuong` varchar(100) DEFAULT NULL,
  `CachDung` varchar(255) DEFAULT NULL,
  `ThoiGianBatDau` date DEFAULT NULL,
  `ThoiGianKetThuc` date DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `dich_vu`
--

CREATE TABLE `dich_vu` (
  `MaDV` int(11) NOT NULL,
  `TenDV` varchar(100) NOT NULL,
  `MoTa` longtext DEFAULT NULL,
  `ThoiLuong` int(11) NOT NULL,
  `GiaDV` decimal(10,2) NOT NULL,
  `TrangThai` varchar(20) NOT NULL DEFAULT 'DANG_AP_DUNG'
) ;

-- --------------------------------------------------------

--
-- Table structure for table `dich_vu_lan_kham`
--

CREATE TABLE `dich_vu_lan_kham` (
  `MaDVLK` int(11) NOT NULL,
  `MaLKham` int(11) NOT NULL,
  `MaDV` int(11) NOT NULL,
  `SoLuong` int(11) NOT NULL DEFAULT 1,
  `DonGiaApDung` decimal(10,2) NOT NULL,
  `ThoiGianThucHien` datetime DEFAULT NULL,
  `GhiChu` longtext DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `don_thuoc`
--

CREATE TABLE `don_thuoc` (
  `MaDonThuoc` int(11) NOT NULL,
  `MaLKham` int(11) NOT NULL,
  `MaBS` int(11) NOT NULL,
  `NgayKeDon` date NOT NULL,
  `LoiDan` longtext DEFAULT NULL,
  `GhiChu` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dot_dieu_tri`
--

CREATE TABLE `dot_dieu_tri` (
  `MaDT` int(11) NOT NULL,
  `MaHSBA` int(11) NOT NULL,
  `MaBS` int(11) NOT NULL,
  `LyDoDieuTri` varchar(300) DEFAULT NULL,
  `ChuanDoan` longtext DEFAULT NULL,
  `KeHoachDieuTri` longtext DEFAULT NULL,
  `NgayBatDau` date NOT NULL,
  `NgayKetThuc` date DEFAULT NULL,
  `TrangThai` varchar(20) NOT NULL DEFAULT 'DANG_DIEU_TRI',
  `KetQuaTongKet` longtext DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `ho_so_benh_an`
--

CREATE TABLE `ho_so_benh_an` (
  `MaHSBA` int(11) NOT NULL,
  `MaBN` int(11) NOT NULL,
  `TienSuBenh` longtext DEFAULT NULL,
  `DiUng` longtext DEFAULT NULL,
  `GhiChu` longtext DEFAULT NULL,
  `NgayLap` datetime NOT NULL DEFAULT current_timestamp(),
  `TrangThai` varchar(20) NOT NULL DEFAULT 'DANG_THEO_DOI'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lan_kham`
--

CREATE TABLE `lan_kham` (
  `MaLKham` int(11) NOT NULL,
  `MaDT` int(11) NOT NULL,
  `MaBS` int(11) NOT NULL,
  `MaLH` int(11) DEFAULT NULL,
  `NgayGioKham` datetime NOT NULL,
  `LyDoKham` varchar(300) DEFAULT NULL,
  `TrieuChung` longtext DEFAULT NULL,
  `ChanDoan` longtext DEFAULT NULL,
  `KetQuaKham` longtext DEFAULT NULL,
  `HuongXuLy` longtext DEFAULT NULL,
  `GhiChu` longtext DEFAULT NULL,
  `TrangThaiLanKham` varchar(20) NOT NULL DEFAULT 'DA_TIEP_NHAN',
  `NgayTao` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lich_hen_kham`
--

CREATE TABLE `lich_hen_kham` (
  `MaLH` int(11) NOT NULL,
  `MaBN` int(11) NOT NULL,
  `MaLLV` int(11) NOT NULL,
  `MaDV` int(11) NOT NULL,
  `MaNVTao` int(11) DEFAULT NULL,
  `NgayKham` date NOT NULL,
  `GioBatDau` time NOT NULL,
  `GioKetThuc` time NOT NULL,
  `TrangThai` varchar(20) NOT NULL DEFAULT 'CHO_XAC_NHAN',
  `GhiChu` longtext DEFAULT NULL,
  `NgayTao` datetime NOT NULL DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `lich_lam_viec`
--

CREATE TABLE `lich_lam_viec` (
  `MaLLV` int(11) NOT NULL,
  `MaBS` int(11) NOT NULL,
  `NgayLamViec` date NOT NULL,
  `GioBatDau` time NOT NULL,
  `GioKetThuc` time NOT NULL,
  `BatDauNghi` time DEFAULT NULL,
  `KetThucNghi` time DEFAULT NULL,
  `TrangThai` varchar(20) NOT NULL DEFAULT 'HOAT_DONG'
) ;

-- --------------------------------------------------------

--
-- Table structure for table `nhan_vien`
--

CREATE TABLE `nhan_vien` (
  `MaNV` int(11) NOT NULL,
  `MaND` int(11) NOT NULL,
  `HoTen` varchar(100) NOT NULL,
  `SDT` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `ChucVu` varchar(100) DEFAULT NULL,
  `TrangThai` varchar(20) NOT NULL DEFAULT 'HOAT_DONG'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tai_khoan_nguoi_dung`
--

CREATE TABLE `tai_khoan_nguoi_dung` (
  `MaND` int(11) NOT NULL,
  `TenDN` varchar(50) NOT NULL,
  `MatKhau` varchar(255) NOT NULL,
  `VaiTro` varchar(20) NOT NULL,
  `TrangThai` varchar(20) NOT NULL DEFAULT 'HOAT_DONG',
  `NgayTao` datetime NOT NULL DEFAULT current_timestamp()
) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bac_si`
--
ALTER TABLE `bac_si`
  ADD PRIMARY KEY (`MaBS`),
  ADD UNIQUE KEY `MaND` (`MaND`);

--
-- Indexes for table `benh_nhan`
--
ALTER TABLE `benh_nhan`
  ADD PRIMARY KEY (`MaBN`),
  ADD UNIQUE KEY `MaND` (`MaND`);

--
-- Indexes for table `chi_tiet_don_thuoc`
--
ALTER TABLE `chi_tiet_don_thuoc`
  ADD PRIMARY KEY (`MaCTDT`),
  ADD KEY `fk_ctdt_don_thuoc` (`MaDonThuoc`);

--
-- Indexes for table `dich_vu`
--
ALTER TABLE `dich_vu`
  ADD PRIMARY KEY (`MaDV`);

--
-- Indexes for table `dich_vu_lan_kham`
--
ALTER TABLE `dich_vu_lan_kham`
  ADD PRIMARY KEY (`MaDVLK`),
  ADD KEY `fk_dvlk_lan_kham` (`MaLKham`),
  ADD KEY `fk_dvlk_dich_vu` (`MaDV`);

--
-- Indexes for table `don_thuoc`
--
ALTER TABLE `don_thuoc`
  ADD PRIMARY KEY (`MaDonThuoc`),
  ADD UNIQUE KEY `MaLKham` (`MaLKham`),
  ADD KEY `fk_dt_bac_si` (`MaBS`);

--
-- Indexes for table `dot_dieu_tri`
--
ALTER TABLE `dot_dieu_tri`
  ADD PRIMARY KEY (`MaDT`),
  ADD KEY `fk_ddt_ho_so_benh_an` (`MaHSBA`),
  ADD KEY `fk_ddt_bac_si` (`MaBS`);

--
-- Indexes for table `ho_so_benh_an`
--
ALTER TABLE `ho_so_benh_an`
  ADD PRIMARY KEY (`MaHSBA`),
  ADD UNIQUE KEY `MaBN` (`MaBN`);

--
-- Indexes for table `lan_kham`
--
ALTER TABLE `lan_kham`
  ADD PRIMARY KEY (`MaLKham`),
  ADD UNIQUE KEY `MaLH` (`MaLH`),
  ADD KEY `fk_lk_dot_dieu_tri` (`MaDT`),
  ADD KEY `fk_lk_bac_si` (`MaBS`);

--
-- Indexes for table `lich_hen_kham`
--
ALTER TABLE `lich_hen_kham`
  ADD PRIMARY KEY (`MaLH`),
  ADD KEY `fk_lhk_benh_nhan` (`MaBN`),
  ADD KEY `fk_lhk_lich_lam_viec` (`MaLLV`),
  ADD KEY `fk_lhk_dich_vu` (`MaDV`),
  ADD KEY `idx_lhk_mnvt` (`MaNVTao`);

--
-- Indexes for table `lich_lam_viec`
--
ALTER TABLE `lich_lam_viec`
  ADD PRIMARY KEY (`MaLLV`),
  ADD KEY `fk_llv_bac_si` (`MaBS`);

--
-- Indexes for table `nhan_vien`
--
ALTER TABLE `nhan_vien`
  ADD PRIMARY KEY (`MaNV`),
  ADD UNIQUE KEY `uq_nhan_vien_mand` (`MaND`);

--
-- Indexes for table `tai_khoan_nguoi_dung`
--
ALTER TABLE `tai_khoan_nguoi_dung`
  ADD PRIMARY KEY (`MaND`),
  ADD UNIQUE KEY `TenDN` (`TenDN`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bac_si`
--
ALTER TABLE `bac_si`
  MODIFY `MaBS` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `benh_nhan`
--
ALTER TABLE `benh_nhan`
  MODIFY `MaBN` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chi_tiet_don_thuoc`
--
ALTER TABLE `chi_tiet_don_thuoc`
  MODIFY `MaCTDT` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dich_vu`
--
ALTER TABLE `dich_vu`
  MODIFY `MaDV` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dich_vu_lan_kham`
--
ALTER TABLE `dich_vu_lan_kham`
  MODIFY `MaDVLK` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `don_thuoc`
--
ALTER TABLE `don_thuoc`
  MODIFY `MaDonThuoc` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dot_dieu_tri`
--
ALTER TABLE `dot_dieu_tri`
  MODIFY `MaDT` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ho_so_benh_an`
--
ALTER TABLE `ho_so_benh_an`
  MODIFY `MaHSBA` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lan_kham`
--
ALTER TABLE `lan_kham`
  MODIFY `MaLKham` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lich_hen_kham`
--
ALTER TABLE `lich_hen_kham`
  MODIFY `MaLH` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lich_lam_viec`
--
ALTER TABLE `lich_lam_viec`
  MODIFY `MaLLV` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nhan_vien`
--
ALTER TABLE `nhan_vien`
  MODIFY `MaNV` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tai_khoan_nguoi_dung`
--
ALTER TABLE `tai_khoan_nguoi_dung`
  MODIFY `MaND` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bac_si`
--
ALTER TABLE `bac_si`
  ADD CONSTRAINT `fk_bac_si_tai_khoan` FOREIGN KEY (`MaND`) REFERENCES `tai_khoan_nguoi_dung` (`MaND`);

--
-- Constraints for table `benh_nhan`
--
ALTER TABLE `benh_nhan`
  ADD CONSTRAINT `fk_benh_nhan_tai_khoan` FOREIGN KEY (`MaND`) REFERENCES `tai_khoan_nguoi_dung` (`MaND`);

--
-- Constraints for table `chi_tiet_don_thuoc`
--
ALTER TABLE `chi_tiet_don_thuoc`
  ADD CONSTRAINT `fk_ctdt_don_thuoc` FOREIGN KEY (`MaDonThuoc`) REFERENCES `don_thuoc` (`MaDonThuoc`);

--
-- Constraints for table `dich_vu_lan_kham`
--
ALTER TABLE `dich_vu_lan_kham`
  ADD CONSTRAINT `fk_dvlk_dich_vu` FOREIGN KEY (`MaDV`) REFERENCES `dich_vu` (`MaDV`),
  ADD CONSTRAINT `fk_dvlk_lan_kham` FOREIGN KEY (`MaLKham`) REFERENCES `lan_kham` (`MaLKham`);

--
-- Constraints for table `don_thuoc`
--
ALTER TABLE `don_thuoc`
  ADD CONSTRAINT `fk_dt_bac_si` FOREIGN KEY (`MaBS`) REFERENCES `bac_si` (`MaBS`),
  ADD CONSTRAINT `fk_dt_lan_kham` FOREIGN KEY (`MaLKham`) REFERENCES `lan_kham` (`MaLKham`);

--
-- Constraints for table `dot_dieu_tri`
--
ALTER TABLE `dot_dieu_tri`
  ADD CONSTRAINT `fk_ddt_bac_si` FOREIGN KEY (`MaBS`) REFERENCES `bac_si` (`MaBS`),
  ADD CONSTRAINT `fk_ddt_ho_so_benh_an` FOREIGN KEY (`MaHSBA`) REFERENCES `ho_so_benh_an` (`MaHSBA`);

--
-- Constraints for table `ho_so_benh_an`
--
ALTER TABLE `ho_so_benh_an`
  ADD CONSTRAINT `fk_hsba_benh_nhan` FOREIGN KEY (`MaBN`) REFERENCES `benh_nhan` (`MaBN`);

--
-- Constraints for table `lan_kham`
--
ALTER TABLE `lan_kham`
  ADD CONSTRAINT `fk_lk_bac_si` FOREIGN KEY (`MaBS`) REFERENCES `bac_si` (`MaBS`),
  ADD CONSTRAINT `fk_lk_dot_dieu_tri` FOREIGN KEY (`MaDT`) REFERENCES `dot_dieu_tri` (`MaDT`),
  ADD CONSTRAINT `fk_lk_lich_hen_kham` FOREIGN KEY (`MaLH`) REFERENCES `lich_hen_kham` (`MaLH`);

--
-- Constraints for table `lich_hen_kham`
--
ALTER TABLE `lich_hen_kham`
  ADD CONSTRAINT `fk_lhk_benh_nhan` FOREIGN KEY (`MaBN`) REFERENCES `benh_nhan` (`MaBN`),
  ADD CONSTRAINT `fk_lhk_dich_vu` FOREIGN KEY (`MaDV`) REFERENCES `dich_vu` (`MaDV`),
  ADD CONSTRAINT `fk_lhk_lich_lam_viec` FOREIGN KEY (`MaLLV`) REFERENCES `lich_lam_viec` (`MaLLV`),
  ADD CONSTRAINT `fk_lhk_nhan_vien` FOREIGN KEY (`MaNVTao`) REFERENCES `nhan_vien` (`MaNV`);

--
-- Constraints for table `lich_lam_viec`
--
ALTER TABLE `lich_lam_viec`
  ADD CONSTRAINT `fk_llv_bac_si` FOREIGN KEY (`MaBS`) REFERENCES `bac_si` (`MaBS`);

--
-- Constraints for table `nhan_vien`
--
ALTER TABLE `nhan_vien`
  ADD CONSTRAINT `fk_nhan_vien_tai_khoan` FOREIGN KEY (`MaND`) REFERENCES `tai_khoan_nguoi_dung` (`MaND`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
