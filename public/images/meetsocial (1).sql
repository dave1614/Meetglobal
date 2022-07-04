-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 06, 2020 at 04:49 PM
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
-- Database: `meetsocial`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `center_connector_selection_income` decimal(20,2) NOT NULL,
  `vendor_selection_income` decimal(20,2) NOT NULL,
  `rgwb_paid` int(11) NOT NULL DEFAULT 0,
  `center_connector` int(11) NOT NULL DEFAULT 0,
  `vendor` int(11) NOT NULL DEFAULT 0,
  `transaction_password` varchar(100) NOT NULL,
  `registration_amt_paid` decimal(20,2) NOT NULL,
  `sponsor_user_id` int(11) NOT NULL,
  `jan_earnings` decimal(20,2) NOT NULL,
  `feb_earnings` decimal(20,2) NOT NULL,
  `mar_earnings` decimal(20,2) NOT NULL,
  `apr_earnings` decimal(20,2) NOT NULL,
  `may_earnings` decimal(20,2) NOT NULL,
  `jun_earnings` decimal(20,2) NOT NULL,
  `jul_earnings` decimal(20,2) NOT NULL,
  `aug_earnings` decimal(20,2) NOT NULL,
  `sep_earnings` decimal(20,2) NOT NULL,
  `oct_earnings` decimal(20,2) NOT NULL,
  `nov_earnings` decimal(20,2) NOT NULL,
  `dec_earnings` decimal(20,2) NOT NULL,
  `monnify_account_reference` varchar(1000) NOT NULL,
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
  `phone` bigint(20) NOT NULL,
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
