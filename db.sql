-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Apr 27, 2025 at 06:00 AM
-- Server version: 8.0.40
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `CRM`
--

-- --------------------------------------------------------

--
-- Table structure for table `Customers_Leads`
--

CREATE TABLE `Customers_Leads` (
  `customer_lead_id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `company` varchar(50) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `phone_num` char(13) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `account_created_time` datetime DEFAULT NULL,
  `notes` varchar(100) DEFAULT NULL,
  `status_id` int DEFAULT NULL,
  `customer_type` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Customers_Leads`
--

INSERT INTO `Customers_Leads` (`customer_lead_id`, `user_id`, `name`, `company`, `email`, `phone_num`, `address`, `account_created_time`, `notes`, `status_id`, `customer_type`) VALUES
(1, 1, 'Mark Johnson', 'TechCorp Solutions', 'mark.j@techcorp.com', '0115295037', '123 Tech Lane, San Francisco, CA', '2025-04-27 13:59:35', 'Potential large client', 1, 2),
(2, 2, 'Emma Watson', 'Startup Hub', 'emma.w@startuphub.io', '0153994127', '456 Innovation Street, Austin, TX', '2025-04-27 13:59:35', 'Early-stage startup', 2, 1),
(3, 3, 'Carlos Rodriguez', 'Global Consulting', 'carlos.r@globalconsult.com', '0105299686', '789 Business Road, Chicago, IL', '2025-04-27 13:59:35', 'Interested in long-term partnership', 3, 2),
(4, 4, 'Sophia Lee', 'Green Initiatives', 'sophia.l@greeninit.org', '0121952404', '321 Eco Drive, Seattle, WA', '2025-04-27 13:59:35', 'Non-profit looking for support', 1, 2),
(5, 5, 'Ryan Thompson', 'Freelance Design', 'ryan.t@freelancedesign.com', '0135291305', '654 Creative Street, Portland, OR', '2025-04-27 13:59:35', 'Individual designer', 2, 2),
(6, 6, 'Amanda Chen', 'Small Business Network', 'amanda.c@smallbiznet.com', '0116338374', '987 Commerce Avenue, Boston, MA', '2025-04-27 13:59:35', 'Local business network', 3, 1),
(7, 7, 'Michael Brown', 'Enterprise Solutions Inc', 'michael.b@enterprisesol.com', '0151055355', '741 Corporate Plaza, New York, NY', '2025-04-27 13:59:35', 'Large enterprise prospect', 4, 2),
(8, 8, 'Karen White', 'Community Support', 'karen.w@communitysupport.org', '0191467034', '852 Hope Street, Denver, CO', '2025-04-27 13:59:35', 'Community outreach program', 1, 2),
(9, 9, 'Daniel Kim', 'Digital Marketing Agency', 'daniel.k@digitalmarketingagency.com', '0178847837', '369 Media Lane, Los Angeles, CA', '2025-04-27 13:59:35', 'Potential marketing collaboration', 2, 2),
(10, 10, 'Jessica Martinez', 'Personal Branding Co', 'jessica.m@personalbranding.com', '0150710697', '159 Personal Street, Miami, FL', '2025-04-27 13:59:35', 'Individual entrepreneur', 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `Customer_Type`
--

CREATE TABLE `Customer_Type` (
  `customer_type_id` int NOT NULL,
  `customer_type_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Customer_Type`
--

INSERT INTO `Customer_Type` (`customer_type_id`, `customer_type_name`) VALUES
(1, 'Customer'),
(2, 'Lead');

-- --------------------------------------------------------

--
-- Table structure for table `Interaction_History`
--

CREATE TABLE `Interaction_History` (
  `interaction_history_id` int NOT NULL,
  `creator_user_id` int NOT NULL,
  `customer_lead_id` int NOT NULL,
  `created_time` datetime NOT NULL,
  `interaction_details` varchar(200) DEFAULT NULL,
  `interaction_type_id` int DEFAULT NULL,
  `interaction_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Interaction_History`
--

INSERT INTO `Interaction_History` (`interaction_history_id`, `creator_user_id`, `customer_lead_id`, `created_time`, `interaction_details`, `interaction_type_id`, `interaction_date`) VALUES
(1, 1, 1, '2025-04-27 13:59:35', 'Initial product presentation', 3, '2025-03-15'),
(2, 2, 2, '2025-04-27 13:59:35', 'Introductory email sent', 2, '2025-03-25'),
(3, 3, 3, '2025-04-27 13:59:35', 'Detailed consultation call', 1, '2025-03-30'),
(4, 4, 4, '2025-04-27 13:59:35', 'Discussed environmental impact', 4, '2025-04-10'),
(5, 5, 5, '2025-04-27 13:59:35', 'Design collaboration session', 3, '2025-04-25'),
(6, 6, 6, '2025-04-27 13:59:35', 'Networking event follow-up', 5, '2025-04-29'),
(7, 7, 7, '2025-04-27 13:59:35', 'Technical solution walkthrough', 4, '2025-04-11'),
(8, 8, 8, '2025-04-27 13:59:35', 'Community support proposal', 2, '2025-03-15'),
(9, 9, 9, '2025-04-27 13:59:35', 'Marketing strategy discussion', 1, '2025-04-13'),
(10, 10, 10, '2025-04-27 13:59:35', 'Brand positioning consultation', 3, '2025-03-15');

-- --------------------------------------------------------

--
-- Table structure for table `Interaction_Type`
--

CREATE TABLE `Interaction_Type` (
  `interaction_type_id` int NOT NULL,
  `interaction_type_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Interaction_Type`
--

INSERT INTO `Interaction_Type` (`interaction_type_id`, `interaction_type_name`) VALUES
(1, 'Phone Call'),
(2, 'Email'),
(3, 'Meeting'),
(4, 'Video Conference'),
(5, 'Social Media');

-- --------------------------------------------------------

--
-- Table structure for table `Reminder`
--

CREATE TABLE `Reminder` (
  `reminder_id` int NOT NULL,
  `creator_user_id` int NOT NULL,
  `customer_lead_id` int NOT NULL,
  `reminder_date` date NOT NULL,
  `reminder_created_time` datetime DEFAULT NULL,
  `notes` varchar(100) DEFAULT NULL,
  `interaction_type_id` int NOT NULL,
  `day_before_reminder` int DEFAULT '0',
  `same_day_reminder` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Reminder`
--

INSERT INTO `Reminder` (`reminder_id`, `creator_user_id`, `customer_lead_id`, `reminder_date`, `reminder_created_time`, `notes`, `interaction_type_id`, `day_before_reminder`, `same_day_reminder`) VALUES
(1, 1, 1, '2025-03-15', '2025-04-27 13:59:35', 'Follow up on product demo', 1, 0, 0),
(2, 2, 2, '2025-03-20', '2025-04-27 13:59:35', 'Schedule initial consultation', 2, 0, 0),
(3, 3, 3, '2025-03-25', '2025-04-27 13:59:35', 'Discuss contract details', 3, 0, 0),
(4, 4, 4, '2025-03-30', '2025-04-27 13:59:35', 'Review project proposal', 4, 0, 0),
(5, 5, 5, '2025-04-05', '2025-04-27 13:59:35', 'Check on design requirements', 5, 0, 0),
(6, 6, 6, '2025-04-10', '2025-04-27 13:59:35', 'Network meeting', 1, 0, 0),
(7, 7, 7, '2025-04-15', '2025-04-27 13:59:35', 'Enterprise solution pitch', 2, 0, 0),
(8, 8, 8, '2025-05-20', '2025-04-27 13:59:35', 'Community support discussion', 3, 0, 0),
(9, 9, 9, '2025-05-25', '2025-04-27 13:59:35', 'Marketing collaboration ideas', 4, 0, 0),
(10, 10, 10, '2025-05-30', '2025-04-27 13:59:35', 'Personal branding strategy', 5, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `Role_Type`
--

CREATE TABLE `Role_Type` (
  `role_type_id` int NOT NULL,
  `role_type_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Role_Type`
--

INSERT INTO `Role_Type` (`role_type_id`, `role_type_name`) VALUES
(1, 'Admin'),
(2, 'Sales Representative');

-- --------------------------------------------------------

--
-- Table structure for table `Status`
--

CREATE TABLE `Status` (
  `status_id` int NOT NULL,
  `status_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Status`
--

INSERT INTO `Status` (`status_id`, `status_type`) VALUES
(1, 'New'),
(2, 'Contacted'),
(3, 'In Progress'),
(4, 'Closed');

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `user_id` int NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_type_id` int NOT NULL,
  `email` varchar(50) NOT NULL,
  `account_created_time` datetime DEFAULT NULL,
  `user_status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`user_id`, `user_name`, `password`, `role_type_id`, `email`, `account_created_time`, `user_status`) VALUES
(1, 'john_doe', '$2y$12$Z2ow509eSRXON.jn8Jt11el6fjUp7gOZJYXUpzeRYVLMPlQo2lrV6', 1, 'john.doe@company.com', '2025-04-27 13:59:35', 'active'),
(2, 'sarah_smith', '$2y$12$Z2ow509eSRXON.jn8Jt11el6fjUp7gOZJYXUpzeRYVLMPlQo2lrV6', 2, 'sarah.smith@company.com', '2025-04-27 13:59:35', 'active'),
(3, 'mike_jackson', '$2y$12$Z2ow509eSRXON.jn8Jt11el6fjUp7gOZJYXUpzeRYVLMPlQo2lrV6', 1, 'mike.jackson@company.com', '2025-04-27 13:59:35', 'active'),
(4, 'emily_wong', '$2y$12$Z2ow509eSRXON.jn8Jt11el6fjUp7gOZJYXUpzeRYVLMPlQo2lrV6', 1, 'emily.wong@company.com', '2025-04-27 13:59:35', 'active'),
(5, 'alex_miller', '$2y$12$Z2ow509eSRXON.jn8Jt11el6fjUp7gOZJYXUpzeRYVLMPlQo2lrV6', 2, 'alex.miller@company.com', '2025-04-27 13:59:35', 'active'),
(6, 'rachel_green', '$2y$12$Z2ow509eSRXON.jn8Jt11el6fjUp7gOZJYXUpzeRYVLMPlQo2lrV6', 2, 'rachel.green@company.com', '2025-04-27 13:59:35', 'active'),
(7, 'david_kim', '$2y$12$Z2ow509eSRXON.jn8Jt11el6fjUp7gOZJYXUpzeRYVLMPlQo2lrV6', 2, 'david.kim@company.com', '2025-04-27 13:59:35', 'active'),
(8, 'lisa_chen', '$2y$12$Z2ow509eSRXON.jn8Jt11el6fjUp7gOZJYXUpzeRYVLMPlQo2lrV6', 2, 'lisa.chen@company.com', '2025-04-27 13:59:35', 'active'),
(9, 'tom_baker', '$2y$12$Z2ow509eSRXON.jn8Jt11el6fjUp7gOZJYXUpzeRYVLMPlQo2lrV6', 1, 'tom.baker@company.com', '2025-04-27 13:59:35', 'active'),
(10, 'julia_roberts', '$2y$12$Z2ow509eSRXON.jn8Jt11el6fjUp7gOZJYXUpzeRYVLMPlQo2lrV6', 2, 'julia.roberts@company.com', '2025-04-27 13:59:35', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Customers_Leads`
--
ALTER TABLE `Customers_Leads`
  ADD PRIMARY KEY (`customer_lead_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone_num` (`phone_num`),
  ADD KEY `fk_userId` (`user_id`),
  ADD KEY `fk_statusId` (`status_id`),
  ADD KEY `fk_customerType` (`customer_type`);

--
-- Indexes for table `Customer_Type`
--
ALTER TABLE `Customer_Type`
  ADD PRIMARY KEY (`customer_type_id`);

--
-- Indexes for table `Interaction_History`
--
ALTER TABLE `Interaction_History`
  ADD PRIMARY KEY (`interaction_history_id`),
  ADD KEY `fk_int_history_creator_user_id` (`creator_user_id`),
  ADD KEY `fk_int_history_creator_lead_id` (`customer_lead_id`),
  ADD KEY `fk_int_history_interaction_type` (`interaction_type_id`);

--
-- Indexes for table `Interaction_Type`
--
ALTER TABLE `Interaction_Type`
  ADD PRIMARY KEY (`interaction_type_id`);

--
-- Indexes for table `Reminder`
--
ALTER TABLE `Reminder`
  ADD PRIMARY KEY (`reminder_id`),
  ADD KEY `fk_creator_user_id` (`creator_user_id`),
  ADD KEY `fk_creator_lead_id` (`customer_lead_id`),
  ADD KEY `fk_interaction_type_id` (`interaction_type_id`);

--
-- Indexes for table `Role_Type`
--
ALTER TABLE `Role_Type`
  ADD PRIMARY KEY (`role_type_id`);

--
-- Indexes for table `Status`
--
ALTER TABLE `Status`
  ADD PRIMARY KEY (`status_id`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_role_type_id` (`role_type_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Customers_Leads`
--
ALTER TABLE `Customers_Leads`
  MODIFY `customer_lead_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `Interaction_History`
--
ALTER TABLE `Interaction_History`
  MODIFY `interaction_history_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `Reminder`
--
ALTER TABLE `Reminder`
  MODIFY `reminder_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Customers_Leads`
--
ALTER TABLE `Customers_Leads`
  ADD CONSTRAINT `fk_customerType` FOREIGN KEY (`customer_type`) REFERENCES `Customer_Type` (`customer_type_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_statusId` FOREIGN KEY (`status_id`) REFERENCES `Status` (`status_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_userId` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `Interaction_History`
--
ALTER TABLE `Interaction_History`
  ADD CONSTRAINT `fk_int_history_creator_lead_id` FOREIGN KEY (`customer_lead_id`) REFERENCES `Customers_Leads` (`customer_lead_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_int_history_creator_user_id` FOREIGN KEY (`creator_user_id`) REFERENCES `Users` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_int_history_interaction_type` FOREIGN KEY (`interaction_type_id`) REFERENCES `Interaction_Type` (`interaction_type_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `Reminder`
--
ALTER TABLE `Reminder`
  ADD CONSTRAINT `fk_creator_lead_id` FOREIGN KEY (`customer_lead_id`) REFERENCES `Customers_Leads` (`customer_lead_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_creator_user_id` FOREIGN KEY (`creator_user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_interaction_type_id` FOREIGN KEY (`interaction_type_id`) REFERENCES `Interaction_Type` (`interaction_type_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `Users`
--
ALTER TABLE `Users`
  ADD CONSTRAINT `fk_role_type_id` FOREIGN KEY (`role_type_id`) REFERENCES `Role_Type` (`role_type_id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
