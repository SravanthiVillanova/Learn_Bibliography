-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 28, 2018 at 08:42 PM
-- Server version: 10.1.34-MariaDB
-- PHP Version: 7.1.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vubib_1`
--

-- --------------------------------------------------------

--
-- Table structure for table `attribute_option_subattribute`
--

CREATE TABLE `attribute_option_subattribute` (
  `id` int(11) NOT NULL,
  `workattribute_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `subattribute_id` int(11) NOT NULL,
  `subattr_value` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attribute_option_subattribute`
--
ALTER TABLE `attribute_option_subattribute`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workattribute_id` (`workattribute_id`),
  ADD KEY `option_id` (`option_id`),
  ADD KEY `subattribute_id` (`subattribute_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attribute_option_subattribute`
--
ALTER TABLE `attribute_option_subattribute`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attribute_option_subattribute`
--
ALTER TABLE `attribute_option_subattribute`
  ADD CONSTRAINT `option_subattr_id` FOREIGN KEY (`subattribute_id`) REFERENCES `workattribute_subattribute` (`id`),
  ADD CONSTRAINT `subattribute` FOREIGN KEY (`subattribute_id`) REFERENCES `workattribute_subattribute` (`id`),
  ADD CONSTRAINT `wkattr_option_id` FOREIGN KEY (`option_id`) REFERENCES `workattribute_option` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
