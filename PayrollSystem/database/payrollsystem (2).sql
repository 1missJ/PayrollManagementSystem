-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2024 at 01:17 PM
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
-- Table structure for table `branch`
--

CREATE TABLE `branch` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `department_manager` varchar(200) NOT NULL,
  `department_address` varchar(200) NOT NULL,
  `branch_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branch`
--

INSERT INTO `branch` (`id`, `employee_id`, `department_manager`, `department_address`, `branch_name`) VALUES
(8, NULL, 'Jaylin', 'Cabagan', 'WonderSaw Cabagan');

-- --------------------------------------------------------

--
-- Table structure for table `branch_employee`
--

CREATE TABLE `branch_employee` (
  `branch_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branch_employee`
--

INSERT INTO `branch_employee` (`branch_id`, `employee_id`) VALUES
(8, 17),
(8, 18);

-- --------------------------------------------------------

--
-- Table structure for table `custom_deductions`
--

CREATE TABLE `custom_deductions` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `percentage` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `custom_deductions`
--

INSERT INTO `custom_deductions` (`id`, `name`, `percentage`) VALUES
(5, 'SSS', 0.00),
(8, 'Pagibig', 0.00),
(9, 'Loan', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `deduction`
--

CREATE TABLE `deduction` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `deduction_name` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL,
  `amount` int(100) NOT NULL,
  `date` date DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('active','disabled') DEFAULT 'active',
  `salary_date` date DEFAULT NULL,
  `payroll_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deduction`
--

INSERT INTO `deduction` (`id`, `employee_id`, `deduction_name`, `description`, `amount`, `date`, `start_date`, `end_date`, `status`, `salary_date`, `payroll_id`) VALUES
(34, 17, 'Loan', '', 5000, '2024-12-06', '2024-12-20', '2024-12-10', 'active', NULL, NULL),
(35, 18, 'SSS', '', 400, '2024-12-06', '2024-12-06', '2025-01-11', 'active', NULL, NULL),
(36, 20, 'Pagibig', '', 1000, '2024-12-06', '2025-01-01', '2025-12-01', 'active', NULL, NULL),
(37, 21, 'SSS', '', 1000, '2024-12-08', '2024-12-02', '2025-01-08', 'active', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact_num` varchar(15) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `civil_status` varchar(20) DEFAULT NULL,
  `date_hired` date DEFAULT NULL,
  `monthly_salary` decimal(10,2) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `salary` int(11) NOT NULL,
  `net_salary` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `first_name`, `middle_name`, `last_name`, `address`, `contact_num`, `position`, `email`, `gender`, `civil_status`, `date_hired`, `monthly_salary`, `dob`, `salary`, `net_salary`) VALUES
(17, 'Larenz', 'P', 'Pacca', 'qee', '123', 'Manager', 'Ha@gmail.com', 'Male', 'Single', '2024-12-05', 10000.00, '2024-12-11', 0, 0),
(18, 'Jaylin', 'D', 'Fernandez', 'Pilig', '123', 'Manager', 'Hai@gmail.com', 'Female', 'Single', '2024-12-19', 50000.00, '2024-12-11', 0, 0),
(20, 'MArk', 'M.', 'Paccarangan', 'villa', '0009090909', 'Manager', '123@gmail.com', 'Male', 'Single', '2024-12-11', 10000.00, '2024-12-13', 0, 0),
(21, 'Shinji', 'm.', 'Rai', 'Naga', '0987654321', 'Manager', '123@gmail.com', 'Male', 'Single', '2024-12-05', 50000.00, '2024-12-12', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `payroll_type` varchar(50) NOT NULL,
  `status` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `pay_period` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll`
--

INSERT INTO `payroll` (`id`, `employee_id`, `date_from`, `date_to`, `payroll_type`, `status`, `date`, `pay_period`) VALUES
(47, NULL, '0000-00-00', '0000-00-00', '', '', '0000-00-00', '2024-12-12'),
(6759, 17, '0000-00-00', '0000-00-00', '', '', '2024-12-11', NULL),
(6760, 18, '0000-00-00', '0000-00-00', '', '', '2024-12-11', NULL),
(6761, 20, '0000-00-00', '0000-00-00', '', '', '2024-12-11', NULL),
(6762, 18, '0000-00-00', '0000-00-00', '', '', '2024-12-04', NULL),
(6763, 18, '2024-12-01', '2025-01-01', '', '', '0000-00-00', NULL),
(6764, 18, '2024-12-02', '2025-01-08', '', '', '0000-00-00', NULL),
(6765, 21, '2024-12-02', '2025-01-08', '', '', '0000-00-00', NULL),
(6766, 21, '2024-12-19', '2024-12-27', '', '', '0000-00-00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payroll_employees`
--

CREATE TABLE `payroll_employees` (
  `payroll_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `deductions` decimal(10,2) DEFAULT NULL,
  `net_salary` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salary_slip`
--

CREATE TABLE `salary_slip` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `payroll_id` int(11) NOT NULL,
  `deduction_id` int(11) NOT NULL,
  `net_pay` double NOT NULL,
  `pay_period` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salary_slips`
--

CREATE TABLE `salary_slips` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `month` varchar(20) NOT NULL,
  `year` int(11) DEFAULT year(curdate())
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `super_user`
--

CREATE TABLE `super_user` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `security_answer1` varchar(255) DEFAULT NULL,
  `security_answer2` varchar(255) DEFAULT NULL,
  `security_answer3` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `super_user`
--

INSERT INTO `super_user` (`id`, `name`, `username`, `password`, `security_answer1`, `security_answer2`, `security_answer3`) VALUES
(1, 'Super Admin', 'admin123', 'admin123', 'Lucena', 'Larenz', 'Paccarangan');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `branch`
--
ALTER TABLE `branch`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_employee_id` (`employee_id`);

--
-- Indexes for table `branch_employee`
--
ALTER TABLE `branch_employee`
  ADD PRIMARY KEY (`branch_id`,`employee_id`),
  ADD KEY `branch_employee_ibfk_2` (`employee_id`);

--
-- Indexes for table `custom_deductions`
--
ALTER TABLE `custom_deductions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deduction`
--
ALTER TABLE `deduction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deduction to employee` (`employee_id`),
  ADD KEY `fk_payroll_id` (`payroll_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payroll to employee` (`employee_id`);

--
-- Indexes for table `payroll_employees`
--
ALTER TABLE `payroll_employees`
  ADD PRIMARY KEY (`payroll_id`,`employee_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `salary_slip`
--
ALTER TABLE `salary_slip`
  ADD KEY `salary_slip to employee` (`employee_id`),
  ADD KEY `salary_slip to payroll` (`payroll_id`),
  ADD KEY `salary_slip to deduction` (`deduction_id`);

--
-- Indexes for table `salary_slips`
--
ALTER TABLE `salary_slips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `super_user`
--
ALTER TABLE `super_user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `branch`
--
ALTER TABLE `branch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `custom_deductions`
--
ALTER TABLE `custom_deductions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `deduction`
--
ALTER TABLE `deduction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6767;

--
-- AUTO_INCREMENT for table `salary_slips`
--
ALTER TABLE `salary_slips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `super_user`
--
ALTER TABLE `super_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `branch`
--
ALTER TABLE `branch`
  ADD CONSTRAINT `fk_employee_id` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `branch_employee`
--
ALTER TABLE `branch_employee`
  ADD CONSTRAINT `branch_employee_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`id`),
  ADD CONSTRAINT `branch_employee_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `deduction`
--
ALTER TABLE `deduction`
  ADD CONSTRAINT `deduction to employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `fk_payroll_id` FOREIGN KEY (`payroll_id`) REFERENCES `payroll` (`id`);

--
-- Constraints for table `payroll`
--
ALTER TABLE `payroll`
  ADD CONSTRAINT `payroll to employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `payroll_employees`
--
ALTER TABLE `payroll_employees`
  ADD CONSTRAINT `payroll_employees_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `payroll_employees_ibfk_2` FOREIGN KEY (`payroll_id`) REFERENCES `payroll` (`id`);

--
-- Constraints for table `salary_slip`
--
ALTER TABLE `salary_slip`
  ADD CONSTRAINT `salary_slip to deduction` FOREIGN KEY (`deduction_id`) REFERENCES `deduction` (`id`),
  ADD CONSTRAINT `salary_slip to employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `salary_slip to payroll` FOREIGN KEY (`payroll_id`) REFERENCES `payroll` (`id`),
  ADD CONSTRAINT `salary_slip_to_deduction` FOREIGN KEY (`deduction_id`) REFERENCES `deduction` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `salary_slips`
--
ALTER TABLE `salary_slips`
  ADD CONSTRAINT `salary_slips_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;