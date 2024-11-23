-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 23, 2024 at 04:16 AM
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
-- Database: `payrollsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `position` varchar(50) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `date_joined` date DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `civil_status` varchar(20) DEFAULT NULL,
  `contact_num` varchar(20) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `department` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `first_name`, `position`, `salary`, `date_joined`, `email`, `gender`, `address`, `date_of_birth`, `civil_status`, `contact_num`, `last_name`, `department`) VALUES
(1, 'j', 'Cashier', NULL, NULL, '0', 'f', 'qee', '2024-11-12', 's', '111', '', ''),
(2, 'j', 'Cashier', NULL, NULL, '0', 'f', 'qee', '2024-11-12', 's', '111', '', ''),
(3, 'j', 'Cashier', NULL, NULL, '0', 'f', 'qee', '2024-11-12', 's', '111', '', ''),
(4, 'j', 'Cashier', NULL, NULL, '0', 'f', 'qee', '2024-11-12', 's', '111', '', ''),
(5, 'j', 'Cashier', NULL, NULL, '0', 'f', 'qee', '2024-11-12', 's', '111', '', ''),
(6, 'j', 'Cashier', NULL, NULL, '0', 'f', 'qee', '2024-11-12', 's', '111', '', ''),
(7, 'j', 'Cashier', NULL, NULL, '0', 'f', 'qee', '2024-11-12', 's', '111', '', ''),
(8, 'j', 'Cashier', NULL, NULL, '0', 'f', 'qee', '2024-11-12', 's', '111', '', ''),
(9, 'Jaylin', 'Cashier', 1000.00, '2024-11-20', NULL, NULL, NULL, NULL, NULL, NULL, 'Fernandez', 'aaa');

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `id` int(11) NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `payroll_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll`
--

INSERT INTO `payroll` (`id`, `date_from`, `date_to`, `payroll_type`) VALUES
(27, '0000-00-00', '0000-00-00', ''),
(39, '2024-11-15', '0000-00-00', 'monthly');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
