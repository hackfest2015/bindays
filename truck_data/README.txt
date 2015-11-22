
BinLogs structure (SQL)

-- phpMyAdmin SQL Dump
-- version 4.4.10
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Nov 21, 2015 at 01:56 PM
-- Server version: 5.5.42
-- PHP Version: 5.6.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `bindays`
--

-- --------------------------------------------------------

--
-- Table structure for table `BinLogs`
--

CREATE TABLE `BinLogs` (
  `UUID` bigint(20) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `truck_ID` varchar(16) DEFAULT NULL,
  `latitude` varchar(16) DEFAULT NULL,
  `longitude` varchar(16) DEFAULT NULL,
  `bin_QR` varchar(64) DEFAULT NULL,
  `property_number` mediumint(11) NOT NULL,
  `bin_type` varchar(16) DEFAULT NULL,
  `bin_size` varchar(16) DEFAULT NULL,
  `weight` mediumint(9) DEFAULT NULL,
  `content_png_size` int(11) DEFAULT NULL,
  `content_png_blob` blob
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `BinLogs`
--
ALTER TABLE `BinLogs`
  ADD UNIQUE KEY `UUID` (`UUID`);




import.php - used to import the council bin collection days data
simulate.php - used to generate the mock truck collection data
   generate_truck_data.php  (Newer version of simulate.php, which
   generates recycling bin data as well)
   
   With Recycling, and JOIN data

SELECT * FROM BinLogs JOIN SCC_source on SCC_source.Property_Number=BinLogs.property_number where BinLogs.property_number=220248

General waste is bin_type=1, recycling is bin_type=4

Daniel.

   


