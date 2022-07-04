-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 06, 2020 at 04:59 PM
-- Server version: 10.4.10-MariaDB
-- PHP Version: 7.0.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sabicapital`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fund_exchange_rate` decimal(20,2) NOT NULL,
  `withdraw_exchange_rate` decimal(20,2) NOT NULL,
  `super_forex_account_number` varchar(100) NOT NULL,
  `live_account_request_approved` int(11) NOT NULL DEFAULT 0,
  `live_account_request_approved_date_time` varchar(50) NOT NULL,
  `live_account_request` int(11) NOT NULL DEFAULT 0,
  `live_account_request_date_time` varchar(50) NOT NULL,
  `active` int(11) NOT NULL DEFAULT 1,
  `dob` varchar(50) NOT NULL,
  `encrypted_pass` text NOT NULL,
  `created` int(11) NOT NULL DEFAULT 0,
  `created_date` varchar(30) NOT NULL,
  `created_time` varchar(30) NOT NULL,
  `sms_enabled` int(11) NOT NULL DEFAULT 0,
  `notif_enabled` int(11) NOT NULL DEFAULT 0,
  `email_enabled` int(11) NOT NULL DEFAULT 0,
  `admin_sms_enabled` int(11) NOT NULL DEFAULT 0,
  `admin_email_enabled` int(11) NOT NULL DEFAULT 0,
  `followers` text NOT NULL,
  `following` text NOT NULL,
  `user_name` varchar(40) NOT NULL,
  `sponsor_income` decimal(20,2) NOT NULL,
  `withdrawn` decimal(20,2) NOT NULL,
  `admin_vat_total` decimal(20,2) NOT NULL,
  `total_basic_withdrawn` decimal(20,2) NOT NULL,
  `total_basic_income` decimal(20,2) NOT NULL,
  `total_business_income` decimal(20,2) NOT NULL,
  `total_business_withdrawn` decimal(20,2) NOT NULL,
  `total_income` decimal(20,2) NOT NULL,
  `admin_mlm_withdrawn` decimal(20,2) NOT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `full_name` varchar(100) NOT NULL,
  `hashed` varchar(50) NOT NULL,
  `token` varchar(40) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `phone_code` int(11) DEFAULT NULL,
  `logo` varchar(50) DEFAULT NULL,
  `cover_photo` text DEFAULT NULL,
  `bio` text NOT NULL,
  `country_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `local_government` int(11) NOT NULL,
  `city` varchar(500) NOT NULL,
  `address` text NOT NULL,
  `place_of_use` varchar(500) NOT NULL,
  `whatsapp_user` int(11) NOT NULL DEFAULT 0,
  `center_leader` int(11) NOT NULL DEFAULT 0,
  `slug` varchar(50) NOT NULL,
  `register` int(11) NOT NULL DEFAULT 0,
  `is_admin` int(11) NOT NULL DEFAULT 0,
  `date` varchar(40) NOT NULL,
  `time` varchar(40) NOT NULL,
  `bank_name` varchar(50) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `recepient_code` varchar(100) NOT NULL,
  `referred_by` int(11) NOT NULL,
  `dispatcher` int(11) NOT NULL DEFAULT 0,
  `center_leader_selection_income` decimal(20,2) NOT NULL,
  `admin_center_leader_selection_income` decimal(20,2) NOT NULL,
  `mlm_withdrawn` decimal(20,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `last_activity` (`last_activity`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fund_exchange_rate`, `withdraw_exchange_rate`, `super_forex_account_number`, `live_account_request_approved`, `live_account_request_approved_date_time`, `live_account_request`, `live_account_request_date_time`, `active`, `dob`, `encrypted_pass`, `created`, `created_date`, `created_time`, `sms_enabled`, `notif_enabled`, `email_enabled`, `admin_sms_enabled`, `admin_email_enabled`, `followers`, `following`, `user_name`, `sponsor_income`, `withdrawn`, `admin_vat_total`, `total_basic_withdrawn`, `total_basic_income`, `total_business_income`, `total_business_withdrawn`, `total_income`, `admin_mlm_withdrawn`, `last_activity`, `full_name`, `hashed`, `token`, `email`, `phone`, `phone_code`, `logo`, `cover_photo`, `bio`, `country_id`, `state_id`, `local_government`, `city`, `address`, `place_of_use`, `whatsapp_user`, `center_leader`, `slug`, `register`, `is_admin`, `date`, `time`, `bank_name`, `account_number`, `recepient_code`, `referred_by`, `dispatcher`, `center_leader_selection_income`, `admin_center_leader_selection_income`, `mlm_withdrawn`) VALUES
(10, '350.00', '400.00', '', 0, '', 0, '', 1, '', '', 1, '', '', 0, 0, 0, 0, 0, '', '', 'admin', '0.00', '20730.60', '50143.91', '0.00', '4521925.00', '325469.69', '0.00', '28110.75', '0.00', '2020-08-24 09:14:58', 'Meet Global', '4d887da68997a84d88a7245ca829c709843a684f', '90551e5a4e7988f7270222c3482721de', 'meetglobalresources@gmail.com', '08181006998', 234, 'fac047f6dcd4dbee194cdd57cd0a4f0a_thumb.png', '0731ae3f402d7cdb384a008da698e5fd.png', '', 151, 2516, 483, 'ikotun', '3,Funsho Obikoya Street', 'home', 0, 0, 'admin', 0, 1, '29 Jul 2019', '06:50:14am', '057', '2003625903', 'RCP_ge9bank3f6n8yef', 0, 0, '0.00', '0.00', '2000.00'),
(13, '0.00', '0.00', '', 0, '', 1, '4 Aug 2020 06:21:13am', 1, '1990-09-12', '5f62f1e6977e9cbcfc879ece8d6ec988f4ec5318c37ee1ac213924132f779b2dfd8476002e8a1fbe19810cceb368afa718a843cbfaf17164204cffd92aae3e24t7hyQI8iDvVUJldZD6Lj0rawNWIix3i+yHFbiVl7BrI=', 1, '22 Jul 2020', '06:26:40am', 0, 0, 0, 0, 0, '', '', 'dave1615', '0.00', '0.00', '0.00', '0.00', '150.00', '0.00', '0.00', '0.00', '0.00', '2020-08-04 06:22:14', 'Nwogo David', '229fb54989e5d6eed897b876d4b37d828402d202', 'fe3670c6a1b43462390b1803950e53ec', 'ikechukwunwogo@gmail.com', '07051942329', 234, NULL, NULL, '', 151, 0, 0, '', '3,Funsho Obikoya Street Ikotun Lagos.', '', 0, 0, 'dave1615', 0, 0, '22 Jul 2020', '06:26:40am', '', '', '', 0, 0, '0.00', '0.00', '0.00'),
(14, '0.00', '0.00', '', 0, '', 0, '', 1, '', '22ae58ba8f27d98522a92b71d34d60cfc8ea0b0546c0b07766c7f738de4022d769ec37d979589d98de7767c72888427516e0bc10163e67bc2bf112f54d418c6bDGW4GAfz9ealDLBvQS63vKZw5UM1c+J6tqJ4t2pTTnU=', 1, '22 Jul 2020', '06:27:49am', 0, 0, 0, 0, 0, '', '', 'dave1616', '0.00', '0.00', '0.00', '0.00', '150.00', '0.00', '0.00', '0.00', '0.00', '2020-07-24 17:53:43', 'Nwogo David', 'a7a121579fe3d2643198922afb906415214289f5', 'cf0435c74e663388d10dacf37554a7b3', 'ikechukwunwogo@gmail.com', '09123678907', 234, NULL, NULL, '', 151, 0, 0, '', '', '', 0, 0, 'dave1616', 0, 0, '22 Jul 2020', '06:27:49am', '', '', '', 0, 0, '0.00', '0.00', '0.00'),
(15, '0.00', '0.00', '', 0, '', 0, '', 1, '', '4a474099f88393bffe009e42973d16df8746e0767f284d4dcc997f8d2e2f9d820c107fc87f8ffc89de27532ee4937abb28f92a2febd0fe40c56fdd1cfa5a6895nOqhUYyudRKq78Srd6s2Z7w+q8M4i4uY7hj6pwZglFc=', 1, '22 Jul 2020', '06:28:40am', 0, 0, 0, 0, 0, '', '', 'dave1617', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '2020-07-24 17:53:47', 'Nwogo David', '92928d3ea8ddd6a0f91e8fb2b17e28a3c1489b8a', '5d9b10a9563991d5b1c7091d7e4894c1', 'ikechukwunwogo@gmail.com', '08127027321', 234, NULL, NULL, '', 151, 0, 0, '', '', '', 0, 0, 'dave1617', 0, 0, '22 Jul 2020', '06:28:40am', '', '', '', 0, 0, '0.00', '0.00', '0.00'),
(17, '0.00', '0.00', '', 0, '', 0, '', 1, '', '637136d1401b5fa6977ec16228d1793a1895582c18b5dd64494a693382c94e99b72119c5f156eb5a0d0038dc4bb6822fe4954c62dded77dabd8405385b48b529IwX1gJUocCFt5mfiP2rNI844+PWPW7AvCcI98dCYmwI=', 1, '22 Jul 2020', '07:32:50am', 0, 0, 0, 0, 0, '', '', 'dave1618', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '2020-07-24 17:53:51', 'Nwogo David', '129878d88246c3bc99828822de6b9d20bcec9666', '4fa338ffc4a8a477887c63f7e43f1edf', 'ikechukwunwogo@gmail.com', '08127027342', 234, NULL, NULL, '', 151, 0, 0, '', '', '', 0, 0, 'dave1618', 0, 0, '22 Jul 2020', '07:32:50am', '', '', '', 12, 0, '0.00', '0.00', '0.00'),
(18, '0.00', '0.00', '', 0, '', 0, '', 1, '1999-07-24', 'e78cc1e548a5c03d70e8d2f39edfeabede2ce417abe292c83615e0b4098c26cd14fc89446dc146f07fc39a16efba8c86bb34b7800bd4a241d46eb164c278bad0VoyloS+9Gfa8h2MTN0O5mAaDv6B/DEyQmwlOinEmTPI=', 1, '22 Jul 2020', '07:35:42am', 0, 0, 0, 0, 0, '', '', 'dave1619', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '2020-07-25 10:23:38', 'Nwogo David', 'c40b933429cbf7a975acda79a2b16547f4c6591e', 'fc267fb2c8e6db4a71182fef943fe583', 'ikechukwunwogo@gmail.com', '08174622455', 234, NULL, NULL, '', 151, 0, 0, '', '3,Funsho Obikoya Street Ikotun Lagos.', '', 0, 0, 'dave1619', 0, 0, '22 Jul 2020', '07:35:42am', '', '', '', 0, 0, '0.00', '0.00', '0.00'),
(12, '0.00', '0.00', '12345', 1, '5 Aug 2020 07:56:16am', 1, '3 Aug 2020 07:44:42pm', 1, '1999-07-24', 'b815330bcf3d4f1b51f7dc1a2ca143128575e42a97dc159b47299f3423c56c4ea31d550eea8a49b5eaa99ad96d0a5014a391b4bd6ef099c28107aa5f69ec0761viXyzpPqWifw2qjHKVe4SOTb8RoS4ANnV88qo/S+bh/eIguFbuUMUDh0MPIolN7a', 1, '21 Jul 2020', '08:06:26pm', 0, 0, 0, 0, 0, '', '', 'dave1614', '0.00', '151.00', '0.00', '0.00', '1150.00', '0.00', '0.00', '200.50', '0.00', '2020-10-05 13:17:44', 'Nwogo David', '03d604eddec9663732ce56455c190ca80e54c9b9', 'e26b7711a787b833b7f6477c50ff70a0', 'ikechukwunwogo@gmail.com', '0705194000000', 234, NULL, NULL, '', 151, 0, 0, '', '3,Funsho Obikoya Street Ikotun Lagos.', '', 0, 0, 'dave1614', 0, 0, '21 Jul 2020', '08:06:26pm', '011', '3143456772', 'RCP_9r6fi27sqwufh0l', 0, 0, '0.00', '0.00', '0.00');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
