-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 16, 2026 at 08:50 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_edt`
--

-- --------------------------------------------------------

--
-- Table structure for table `getmast`
--

CREATE TABLE `getmast` (
  `Get_ID` int(11) NOT NULL,
  `Get_Date` date NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่รับ\r\nวันที่รับสินค้า',
  `Get_Product_ID` int(11) NOT NULL COMMENT 'รหัสสินค้า',
  `Get_Num` double NOT NULL COMMENT 'จำนวน',
  `Get_Name` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'รับสินค้าจากใคร'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `getmast`
--

INSERT INTO `getmast` (`Get_ID`, `Get_Date`, `Get_Product_ID`, `Get_Num`, `Get_Name`) VALUES
(3, '2026-01-16', 3, 5, 'test'),
(4, '2026-01-16', 3, 5, 'test'),
(5, '2026-01-16', 4, 45, 'คลังสินค้า'),
(9, '2026-01-16', 4, 2, '2'),
(10, '2026-01-16', 3, 50, 'test'),
(11, '2026-01-16', 3, 2, 'test');

-- --------------------------------------------------------

--
-- Table structure for table `productmast`
--

CREATE TABLE `productmast` (
  `Product_ID` int(11) NOT NULL COMMENT 'รหัสสินค้า',
  `Product_Name` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'ชื่อสินค้า',
  `Product_Cost` double NOT NULL COMMENT 'ราคาต้นทุน',
  `Product_Price` double NOT NULL COMMENT 'ราคาขาย',
  `Product_Stock` double NOT NULL COMMENT 'สต๊อก'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `productmast`
--

INSERT INTO `productmast` (`Product_ID`, `Product_Name`, `Product_Cost`, `Product_Price`, `Product_Stock`) VALUES
(3, 'เครื่องปริ้น', 12000, 15000, 50),
(4, 'น้ำยาซักผ้า', 200, 300, 40),
(6, 'แมส', 20, 600, 0);

-- --------------------------------------------------------

--
-- Table structure for table `salemast`
--

CREATE TABLE `salemast` (
  `Sale_ID` int(11) NOT NULL COMMENT 'รหัสการขาย',
  `Sale_Date` date NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่ขาย',
  `Sale_Name` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'ชื่อคนที่ขายให้',
  `Sale_Product_ID` int(11) NOT NULL COMMENT 'รหัสสินค้าที่ขาย',
  `Sale_Num` double NOT NULL COMMENT 'จำนวนที่ขาย',
  `Sale_Price` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `salemast`
--

INSERT INTO `salemast` (`Sale_ID`, `Sale_Date`, `Sale_Name`, `Sale_Product_ID`, `Sale_Num`, `Sale_Price`) VALUES
(3, '2026-01-16', 'test', 3, 2, 30000),
(4, '2026-01-16', 'test', 3, 3, 6000),
(7, '2026-01-16', 'test', 4, 2, 6000),
(8, '2026-01-16', 'test', 4, 3, 600),
(9, '2026-01-16', 'test', 4, 2, 30000),
(10, '2026-01-16', 'test', 3, 3, 45000);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `getmast`
--
ALTER TABLE `getmast`
  ADD PRIMARY KEY (`Get_ID`);

--
-- Indexes for table `productmast`
--
ALTER TABLE `productmast`
  ADD PRIMARY KEY (`Product_ID`),
  ADD UNIQUE KEY `Product_Name` (`Product_Name`);

--
-- Indexes for table `salemast`
--
ALTER TABLE `salemast`
  ADD PRIMARY KEY (`Sale_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `getmast`
--
ALTER TABLE `getmast`
  MODIFY `Get_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `productmast`
--
ALTER TABLE `productmast`
  MODIFY `Product_ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัสสินค้า', AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `salemast`
--
ALTER TABLE `salemast`
  MODIFY `Sale_ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัสการขาย', AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
